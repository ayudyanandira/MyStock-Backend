<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\DetailPenggunaan;
use App\Models\Penggunaan;
use App\Models\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenggunaanService
{
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Penggunaan::query()->with(['user', 'detailPenggunaan.barang'])->latest();
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->input('tanggal_awal'));
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->input('tanggal_akhir'));
        }
        if ($request->filled('search')) {
            $query->where('nomor_transaksi', 'like', '%'.$request->input('search').'%');
        }

        return $query->paginate($request->integer('per_page', 10));
    }

    public function show(Penggunaan $penggunaan): Penggunaan
    {
        return $penggunaan->load(['user', 'detailPenggunaan.barang']);
    }

    public function store(array $data): Penggunaan
    {
        return DB::transaction(function () use ($data) {
            $penggunaan = Penggunaan::create(['nomor_transaksi' => $this->number(), 'tanggal' => $data['tanggal'], 'keterangan' => $data['keterangan'] ?? null, 'user_id' => Auth::id()]);
            $this->addItems($penggunaan, $data['items']);
            $this->audit('CREATE', $penggunaan->id);

            return $this->show($penggunaan);
        });
    }

    public function update(Penggunaan $penggunaan, array $data): Penggunaan
    {
        return DB::transaction(function () use ($penggunaan, $data) {
            $penggunaan->load('detailPenggunaan');
            $this->restoreStock($penggunaan);
            $penggunaan->detailPenggunaan()->delete();
            $penggunaan->update(['tanggal' => $data['tanggal'], 'keterangan' => $data['keterangan'] ?? null]);
            $this->addItems($penggunaan, $data['items']);
            $this->audit('UPDATE', $penggunaan->id);

            return $this->show($penggunaan->fresh());
        });
    }

    public function destroy(Penggunaan $penggunaan): void
    {
        DB::transaction(function () use ($penggunaan) {
            $penggunaan->load('detailPenggunaan');
            $this->restoreStock($penggunaan);
            $penggunaan->detailPenggunaan()->delete();
            $this->audit('DELETE', $penggunaan->id);
            $penggunaan->delete();
        });
    }

    private function addItems(Penggunaan $penggunaan, array $items): void
    {
        foreach ($items as $item) {
            $barang = Barang::lockForUpdate()->findOrFail($item['barang_id']);
            if ($barang->stok < $item['jumlah']) {
                throw ValidationException::withMessages(['items' => "Stok {$barang->nama_barang} tidak mencukupi."]);
            } $before = $barang->stok;
            $after = $before - $item['jumlah'];
            DetailPenggunaan::create(['penggunaan_id' => $penggunaan->id, 'barang_id' => $barang->id, 'jumlah' => $item['jumlah'], 'catatan' => $item['catatan'] ?? null]);
            $barang->update(['stok' => $after]);
            StockMovement::create(['barang_id' => $barang->id, 'reference_type' => 'PENGGUNAAN', 'reference_id' => $penggunaan->id, 'qty_in' => 0, 'qty_out' => $item['jumlah'], 'stock_before' => $before, 'stock_after' => $after, 'created_by' => Auth::id()]);
        }
    }

    private function restoreStock(Penggunaan $penggunaan): void
    {
        foreach ($penggunaan->detailPenggunaan as $detail) {
            $barang = Barang::lockForUpdate()->findOrFail($detail->barang_id);
            $barang->update(['stok' => $barang->stok + $detail->jumlah]);
            StockMovement::where(['reference_type' => 'PENGGUNAAN', 'reference_id' => $penggunaan->id, 'barang_id' => $barang->id])->delete();
        }
    }

    private function audit(string $activity, int $id): void
    {
        AuditLog::create(['user_id' => Auth::id(), 'modul' => 'PENGGUNAAN', 'aktivitas' => $activity, 'reference_id' => $id, 'ip_address' => request()->ip()]);
    }

    private function number(): string
    {
        $prefix = 'TRX-OUT-'.now()->format('Ymd').'-';
        for ($i = 0; $i < 20; $i++) {
            $number = $prefix.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            if (! Penggunaan::where('nomor_transaksi', $number)->exists()) {
                return $number;
            }
        } throw ValidationException::withMessages(['nomor_transaksi' => 'Nomor transaksi tidak dapat dibuat.']);
    }
}
