<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penerimaan;
use App\Models\DetailPenerimaan;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenerimaanController extends Controller
{
    // 1. Tampilkan Semua List Nota / PO
    public function index()
    {
        $penerimaan = Penerimaan::with(['supplier', 'details.barang.satuan'])->latest()->get();
        return response()->json(['data' => $penerimaan]);
    }

    // 2. Simpan PO Baru (Generate Nomor PO Otomatis di Backend)
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'             => 'required|exists:supplier,id',
            'tanggal'                 => 'nullable|date',
            'tanggal_pesan'           => 'nullable|date',
            'items'                   => 'required|array|min:1',
            'items.*.barang_id'       => 'required|exists:barang,id',
            'items.*.jumlah_pesanan'  => 'required|numeric|min:0.01',
            'items.*.jumlah_diterima' => 'nullable|numeric|min:0',
        ]);

        $penerimaan = null;

        DB::transaction(function () use ($request, &$penerimaan) {
            // Auto-generate nomor transaksi unik: PO-YYYYMMDD-XXXX
            $today = Carbon::now()->format('Ymd');
            $prefix = 'PO-' . $today . '-';

            $lastTransaction = Penerimaan::where('nomor_transaksi', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastTransaction) {
                $lastNumber = (int) substr($lastTransaction->nomor_transaksi, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $nomorTransaksiOtomatis = $prefix . $nextNumber;
            $tanggalTrans = $request->tanggal ?? $request->tanggal_pesan ?? Carbon::now()->toDateString();

            // Simpan Header
            $penerimaan = Penerimaan::create([
                'nomor_transaksi' => $nomorTransaksiOtomatis,
                'supplier_id'     => $request->supplier_id,
                'tanggal'         => $tanggalTrans,
                'status'          => 'pending',
            ]);

            // Simpan Detail Pesanan
            foreach ($request->items as $item) {
                DetailPenerimaan::create([
                    'penerimaan_id'   => $penerimaan->id,
                    'barang_id'       => $item['barang_id'],
                    'jumlah_pesanan'  => $item['jumlah_pesanan'],
                    'jumlah_diterima' => 0,
                    'selisih'         => $item['jumlah_pesanan'],
                    'status'          => 'Pending',
                    'kondisi'         => 'Belum Diterima',
                ]);
            }
        });

        return response()->json([
            'message' => 'Dokumen PO berhasil dibuat!',
            'data'    => $penerimaan->load('details.barang.satuan')
        ], 201);
    }

    // 3. Konfirmasi Penerimaan Barang (LOG MUTASI, STOK AUTOMATIC & AUTO-DEDUCT PER ITEM)
    // 3. Konfirmasi Penerimaan Barang (LOG MUTASI, STOK AUTOMATIC & AUTO-DEDUCT PER ITEM)
public function confirmReceipt(Request $request, $id)
{
    $penerimaan = Penerimaan::findOrFail($id);

    if ($penerimaan->status === 'completed') {
        return response()->json(['message' => 'PO ini sudah pernah diproses sebelumnya!'], 400);
    }

    $request->validate([
        'tanggal_terima'                => 'required|date',
        'items'                         => 'required|array|min:1',
        'items.*.barang_id'             => 'required|exists:barang,id',
        'items.*.jumlah_diterima'       => 'required|numeric|min:0',
        'items.*.is_direct_consumption' => 'nullable|boolean', // Validasi boolean item
    ]);

    DB::transaction(function () use ($request, $penerimaan) {
        foreach ($request->items as $item) {
            Log::info('DEBUG CONFIRM ITEM:', [
        'item' => $item,
        'is_direct' => $item['is_direct_consumption'] ?? 'TIDAK ADA KEY'
    ]);

            $detail = DetailPenerimaan::where('penerimaan_id', $penerimaan->id)
                                        ->where('barang_id', $item['barang_id'])->first();

            if ($detail) {
                $jmlDiterima = (float) $item['jumlah_diterima'];
                $jmlPesanan  = (float) $detail->jumlah_pesanan;
                $selisih     = $jmlPesanan - $jmlDiterima;

                $statusSesuai = abs($selisih) < 0.001 ? 'Sesuai' : 'Selisih';

                // AMBIL FLAG DIRECT CONSUMPTION DARI ITEM (BUKAN DARI ROOT REQUEST)
                $isItemDirect = isset($item['is_direct_consumption']) ? filter_var($item['is_direct_consumption'], FILTER_VALIDATE_BOOLEAN) : false;

                // Update detail penerimaan
                $detail->update([
                    'jumlah_diterima' => $jmlDiterima,
                    'selisih'         => $selisih,
                    'status'          => $statusSesuai,
                    'kondisi'         => $item['kondisi'] ?? 'Baik',
                    'keterangan'      => $item['keterangan'] ?? null,
                ]);

                if ($jmlDiterima > 0) {
                    // 1. Catat Mutasi MASUK
                    StokService::catatMutasi(
                        (int) $item['barang_id'],
                        'MASUK',
                        $jmlDiterima,
                        $penerimaan->nomor_transaksi,
                        'Penerimaan Bahan Pangan dari Supplier'
                    );

                    // 2. Catat Mutasi KELUAR jika item dicentang (Auto-Deduct)
                    if ($isItemDirect) {
                        StokService::catatMutasi(
                            (int) $item['barang_id'],
                            'KELUAR',
                            $jmlDiterima,
                            $penerimaan->nomor_transaksi . '-AUTO',
                            'Direct Consumption (Langsung Digunakan untuk Menu Harian)'
                        );
                    }
                }
            }
        }

        // Ubah Status PO menjadi Completed
        $penerimaan->update([
            'status'         => 'completed',
            'tanggal_terima' => $request->tanggal_terima,
        ]);
    });

    return response()->json([
        'message' => 'Penerimaan barang berhasil dikonfirmasi! Stok dan Log Mutasi telah diperbarui.'
    ]);
}

    // 4. Lihat Detail Nota/PO tertentu
    public function show($id)
    {
        $penerimaan = Penerimaan::with(['supplier', 'details.barang.satuan'])->findOrFail($id);
        return response()->json(['data' => $penerimaan]);
    }
}