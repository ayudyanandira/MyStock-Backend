<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StokOpname\StoreStokOpnameRequest;
use App\Http\Resources\StokOpnameResource;
use App\Models\Barang;
use App\Models\StockMovement;
use App\Models\StokOpname;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        // 1. Tambahkan .satuan di sini agar data satuan terangkut ke frontend
        $stokOpname = StokOpname::with(['user', 'details.barang.satuan', 'details.barang.kategori'])
            ->latest()
            ->paginate(10);

        return StokOpnameResource::collection($stokOpname);
    }

    public function store(StoreStokOpnameRequest $request): JsonResponse
    {
        $stokOpname = DB::transaction(function () use ($request) {
            $today = \Carbon\Carbon::now()->format('Ymd');
            $prefix = 'SOP-' . $today . '-';

            $lastOpname = StokOpname::where('nomor_transaksi', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastOpname) {
                $lastNumber = (int) substr($lastOpname->nomor_transaksi, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $nomorTransaksiOtomatis = $prefix . $nextNumber;

            $opname = StokOpname::create([
                'nomor_transaksi' => $nomorTransaksiOtomatis,
                'tanggal'         => $request->tanggal,
                'created_by'      => $request->user()->id,
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                
                $stokSistem = (float)$barang->stok;
                $stokFisik  = (float)$item['stok_fisik'];
                $selisih    = $stokFisik - $stokSistem;

                $opname->details()->create([
                    'barang_id'   => $barang->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik'  => $stokFisik,
                    'selisih'     => $selisih,
                    'keterangan'  => $item['keterangan'] ?? null,
                ]);

                $barang->update(['stok' => $stokFisik]);

                StockMovement::create([
                    'barang_id'      => $barang->id,
                    'reference_type' => 'STOK_OPNAME',
                    'reference_id'   => $opname->id,
                    'qty_in'         => $selisih > 0 ? $selisih : 0,
                    'qty_out'        => $selisih < 0 ? abs($selisih) : 0,
                    'stock_before'   => $stokSistem,
                    'stock_after'    => $stokFisik,
                    'created_by'     => $request->user()->id,
                ]);
            }

            return $opname;
        });

        // 2. Tambahkan juga .satuan di penutupan response store ini
        $stokOpname->load(['user', 'details.barang.satuan', 'details.barang.kategori']);

        return response()->json([
            'message' => 'Stok Opname berhasil dicatat dan stok barang telah diperbarui',
            'data'    => new StokOpnameResource($stokOpname),
        ], 201);
    }

    public function show(StokOpname $stokOpname): StokOpnameResource
    {
        $stokOpname->load(['user', 'details.barang.kategori', 'details.barang.satuan']);
        return new StokOpnameResource($stokOpname);
    }
}