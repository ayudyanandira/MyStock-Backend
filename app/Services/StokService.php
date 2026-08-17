<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokService
{
    /**
     * Catat mutasi stok (MASUK / KELUAR) sekaligus perbarui stok barang secara atomik.
     *
     * @param int    $barangId       ID Barang
     * @param string $jenisTransaksi 'MASUK' atau 'KELUAR'
     * @param float  $jumlah         Jumlah kuantitas
     * @param string $referensi      Nomor Nota / PO / Transaksi (e.g. PO-20260818-0001)
     * @param string $keterangan     Keterangan detail mutasi
     */
    public static function catatMutasi(
        int $barangId,
        string $jenisTransaksi,
        float $jumlah,
        string $referensi = '-',
        string $keterangan = '-'
    ): void {
        DB::transaction(function () use ($barangId, $jenisTransaksi, $jumlah, $referensi, $keterangan) {
            // 1. Lock data barang untuk cegah race condition saat update stok
            $barang = Barang::lockForUpdate()->findOrFail($barangId);

            $stokAwal = (float) $barang->stok;
            $jenis = strtoupper($jenisTransaksi);

            // 2. Hitung Stok Akhir & tentukan qty_in / qty_out
            if ($jenis === 'MASUK') {
                $stokAkhir = $stokAwal + $jumlah;
                $qtyIn     = $jumlah;
                $qtyOut    = 0;
            } else { // KELUAR / OPNAME
                $stokAkhir = $stokAwal - $jumlah;
                $qtyIn     = 0;
                $qtyOut    = $jumlah;
            }

            // 3. Perbarui Stok Barang
            $barang->update([
                'stok' => $stokAkhir
            ]);

            // 4. Catat Log Pergerakan ke Model StockMovement Bawaan
            StockMovement::create([
                'barang_id'      => $barang->id,
                'reference_type' => $jenis === 'MASUK' ? 'PENERIMAAN' : 'PENGGUNAAN',
                'reference_id'   => $referensi,
                'qty_in'         => $qtyIn,
                'qty_out'        => $qtyOut,
                'stock_before'   => $stokAwal,
                'stock_after'    => $stokAkhir,
                'keterangan'     => $keterangan,
                'created_by'     => Auth::id() ?? 1,
            ]);
        });
    }
}