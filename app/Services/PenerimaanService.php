<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\FotoPenerimaan;
use App\Models\Penerimaan;
use App\Models\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PenerimaanService
{
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Penerimaan::query()
            ->with(['supplier', 'detailPenerimaan.barang', 'fotoPenerimaan'])
            ->latest();

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->input('tanggal_awal'));
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->input('tanggal_akhir'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search): void {
                $query->where('nomor_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($supplier) => $supplier->where('nama_supplier', 'like', "%{$search}%"));
            });
        }

        return $query->paginate($request->integer('per_page', 10));
    }

    public function show(Penerimaan $penerimaan): Penerimaan
    {
        return $penerimaan->load(['supplier', 'detailPenerimaan.barang', 'fotoPenerimaan']);
    }

    public function store(array $data): Penerimaan
    {
        return DB::transaction(function () use ($data): Penerimaan {
            $penerimaan = Penerimaan::create([
                'nomor_transaksi' => $this->generateNomor(),
                'supplier_id' => $data['supplier_id'],
                'tanggal' => $data['tanggal'],
            ]);

            $this->addItems($penerimaan, $data['items']);
            $this->addPhotos($penerimaan, $data['photos'] ?? []);
            $this->audit('CREATE', $penerimaan->id);

            return $this->show($penerimaan);
        });
    }

    public function update(Penerimaan $penerimaan, array $data): Penerimaan
    {
        return DB::transaction(function () use ($penerimaan, $data): Penerimaan {
            $penerimaan->load('detailPenerimaan', 'fotoPenerimaan');
            $this->reverseStock($penerimaan);
            $penerimaan->detailPenerimaan()->delete();

            $penerimaan->update([
                'supplier_id' => $data['supplier_id'],
                'tanggal' => $data['tanggal'],
            ]);

            $this->addItems($penerimaan, $data['items']);
            $this->addPhotos($penerimaan, $data['photos'] ?? []);
            $this->audit('UPDATE', $penerimaan->id);

            return $this->show($penerimaan->fresh());
        });
    }

    public function destroy(Penerimaan $penerimaan): void
    {
        DB::transaction(function () use ($penerimaan): void {
            $penerimaan->load('detailPenerimaan', 'fotoPenerimaan');
            $this->reverseStock($penerimaan);

            foreach ($penerimaan->fotoPenerimaan as $photo) {
                Storage::disk('public')->delete($photo->path_file);
            }

            $penerimaan->fotoPenerimaan()->delete();
            $penerimaan->detailPenerimaan()->delete();
            $this->audit('DELETE', $penerimaan->id);
            $penerimaan->delete();
        });
    }

    private function addItems(Penerimaan $penerimaan, array $items): void
    {
        foreach ($items as $item) {
            $barang = Barang::lockForUpdate()->findOrFail($item['barang_id']);
            $stockBefore = $barang->stok;
            $stockAfter = $stockBefore + $item['jumlah_diterima'];
            $selisih = $item['jumlah_diterima'] - $item['jumlah_pesanan'];
            $status = $selisih === 0.0 ? 'sesuai' : ($selisih < 0 ? 'kurang' : 'lebih');

            DetailPenerimaan::create([
                'penerimaan_id' => $penerimaan->id,
                'barang_id' => $barang->id,
                'jumlah_pesanan' => $item['jumlah_pesanan'],
                'jumlah_diterima' => $item['jumlah_diterima'],
                'selisih' => $selisih,
                'status' => $status,
                'kondisi' => $item['kondisi'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);

            $barang->update(['stok' => $stockAfter]);
            StockMovement::create([
                'barang_id' => $barang->id,
                'reference_type' => 'PENERIMAAN',
                'reference_id' => $penerimaan->id,
                'qty_in' => $item['jumlah_diterima'],
                'qty_out' => 0,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'created_by' => Auth::id(),
            ]);
        }
    }

    private function reverseStock(Penerimaan $penerimaan): void
    {
        foreach ($penerimaan->detailPenerimaan as $detail) {
            $barang = Barang::lockForUpdate()->findOrFail($detail->barang_id);

            if ($barang->stok < $detail->jumlah) {
                throw ValidationException::withMessages([
                    'items' => 'Penerimaan tidak dapat diubah atau dihapus karena stok barang sudah digunakan oleh transaksi lain.',
                ]);
            }

            $stockBefore = $barang->stok;
            $stockAfter = $stockBefore - $detail->jumlah;
            $barang->update(['stok' => $stockAfter]);

            StockMovement::where([
                'reference_type' => 'PENERIMAAN',
                'reference_id' => $penerimaan->id,
                'barang_id' => $barang->id,
            ])->delete();
        }
    }

    private function addPhotos(Penerimaan $penerimaan, array $photos): void
    {
        foreach ($photos as $photo) {
            $path = $photo->store('penerimaan', 'public');
            FotoPenerimaan::create([
                'penerimaan_id' => $penerimaan->id,
                'nama_file' => $photo->getClientOriginalName(),
                'path_file' => $path,
                'mime_type' => $photo->getMimeType() ?? 'application/octet-stream',
                'ukuran_file' => $photo->getSize(),
                'uploaded_by' => Auth::id(),
            ]);
        }
    }

    private function audit(string $activity, int $referenceId): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'modul' => 'PENERIMAAN',
            'aktivitas' => $activity,
            'reference_id' => $referenceId,
            'ip_address' => request()->ip(),
        ]);
    }

    private function generateNomor(): string
    {
        $prefix = 'IN-'.now()->format('YmdHis').'-';

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $number = $prefix.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

            if (! Penerimaan::query()->where('nomor_transaksi', $number)->exists()) {
                return $number;
            }
        }

        throw ValidationException::withMessages([
            'nomor_transaksi' => 'Nomor transaksi tidak dapat dibuat. Silakan coba lagi.',
        ]);
    }
}
