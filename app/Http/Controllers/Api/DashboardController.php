<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Penerimaan;
use App\Models\Penggunaan;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // 1. Total ringkasan angka
        $totalBarang = Barang::where('is_active', true)->count();
        $totalSupplier = Supplier::count();
        $stokMinimum = Barang::where('is_active', true)
            ->whereRaw('stok <= stok_minimum')
            ->count();

        // 2. Barang dengan stok paling kritis (Limit 5)
        $barangStokKritis = Barang::with(['kategori', 'satuan'])
            ->where('is_active', true)
            ->whereRaw('stok <= stok_minimum')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        // 3. Ringkasan singkat aktivitas terbaru
        $penerimaanTerakhir = Penerimaan::with('supplier')->latest()->limit(5)->get();
        $penggunaanTerakhir = Penggunaan::latest()->limit(5)->get();

        return response()->json([
            'message' => 'Data dashboard berhasil dimuat',
            'data' => [
                'summary' => [
                    'total_barang'   => $totalBarang,
                    'total_supplier' => $totalSupplier,
                    'stok_minimum'   => $stokMinimum,
                ],
                'barang_stok_kritis'  => $barangStokKritis,
                'penerimaan_terakhir' => $penerimaanTerakhir,
                'penggunaan_terakhir' => $penggunaanTerakhir,
            ]
        ]);
    }
}