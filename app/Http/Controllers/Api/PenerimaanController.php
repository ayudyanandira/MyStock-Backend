<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penerimaan;
use App\Models\DetailPenerimaan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    // 1. Tampilkan Semua List Nota / PO
    public function index()
    {
        $penerimaan = Penerimaan::with(['supplier', 'details.barang'])->latest()->get();
        return response()->json(['data' => $penerimaan]);
    }

    // 2. Simpan PO Baru (Generate Nomor PO Otomatis di Backend)
    public function store(Request $request)
    {
        // Validasi tanpa perlu mengecek nomor_transaksi dari frontend
        $request->validate([
            'supplier_id'            => 'required|exists:supplier,id',
            'tanggal'                => 'nullable|date',
            'tanggal_pesan'          => 'nullable|date',
            'items'                  => 'required|array|min:1',
            'items.*.barang_id'      => 'required|exists:barang,id',
            'items.*.jumlah_pesanan' => 'required|numeric|min:0.01',
            'items.*.jumlah_diterima' => 'nullable|numeric|min:0',
        ]);

        $penerimaan = null;

        DB::transaction(function () use ($request, &$penerimaan) {
            // A. Auto-generate nomor transaksi unik: PO-YYYYMMDD-XXXX
            $today = Carbon::now()->format('Ymd');
            $prefix = 'PO-' . $today . '-';

            // Ambil nomor transaksi terakhir di hari yang sama
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

            // Sesuaikan input tanggal (fallback ke tanggal hari ini jika kosong)
            $tanggalTrans = $request->tanggal ?? $request->tanggal_pesan ?? Carbon::now()->toDateString();

            // B. Simpan Header Penerimaan dengan status 'pending'
            $penerimaan = Penerimaan::create([
                'nomor_transaksi' => $nomorTransaksiOtomatis,
                'supplier_id'     => $request->supplier_id,
                'tanggal'         => $tanggalTrans,
                'status'          => 'pending',
            ]);

            // C. Simpan Detail Pesanan
            foreach ($request->items as $item) {
                DetailPenerimaan::create([
                    'penerimaan_id'  => $penerimaan->id,
                    'barang_id'      => $item['barang_id'],
                    'jumlah_pesanan' => $item['jumlah_pesanan'],
                    'jumlah_diterima'=> 0,
                    'selisih'        => $item['jumlah_pesanan'],
                    'status'         => 'Pending',
                    'kondisi'        => 'Belum Diterima',
                ]);
            }
        });

        return response()->json([
            'message' => 'Dokumen PO berhasil dibuat!',
            'data'    => $penerimaan->load('details.barang')
        ], 201);
    }

    // 3. Konfirmasi Penerimaan Barang (STOK BERTAMBAH OTOMATIS)
    public function confirmReceipt(Request $request, $id)
    {
        $penerimaan = Penerimaan::findOrFail($id);

        if ($penerimaan->status === 'completed') {
            return response()->json(['message' => 'PO ini sudah pernah diproses sebelumnya!'], 400);
        }

        $request->validate([
            'tanggal_terima'          => 'required|date',
            'items'                   => 'required|array|min:1',
            'items.*.barang_id'       => 'required',
            'items.*.jumlah_diterima' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $penerimaan) {
            foreach ($request->items as $item) {
                $detail = DetailPenerimaan::where('penerimaan_id', $penerimaan->id)
                            ->where('barang_id', $item['barang_id'])->first();

                if ($detail) {
                    $jmlDiterima = $item['jumlah_diterima'];
                    $selisih = $detail->jumlah_pesanan - $jmlDiterima;
                    $statusSesuai = $selisih == 0 ? 'Sesuai' : 'Selisih';

                    // Update detail barang yang diterima
                    $detail->update([
                        'jumlah_diterima' => $jmlDiterima,
                        'selisih'         => $selisih,
                        'status'          => $statusSesuai,
                        'kondisi'         => $item['kondisi'] ?? 'Baik',
                        'keterangan'      => $item['keterangan'] ?? null,
                    ]);

                    // Tambahkan stok barang jika diterima
                    if ($jmlDiterima > 0) {
                        Barang::where('id', $item['barang_id'])->increment('stok', $jmlDiterima);
                    }
                }
            }

            // Ubah Status PO menjadi Completed
            $penerimaan->update([
                'status'         => 'completed',
                'tanggal_terima' => $request->tanggal_terima,
            ]);
        });

        return response()->json(['message' => 'Penerimaan barang berhasil dikonfirmasi! Stok telah diperbarui.']);
    }

    // 4. Lihat Detail Nota/PO tertentu
    public function show($id)
    {
        $penerimaan = Penerimaan::with(['supplier', 'details.barang'])->findOrFail($id);
        return response()->json(['data' => $penerimaan]);
    }
}