<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StokMutasi;
use Illuminate\Http\Request;

class StokMutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = StokMutasi::with(['barang.satuan'])->latest();

        // Filter berdasarkan Barang (jika dipillih)
        if ($request->has('barang_id') && $request->barang_id != '') {
            $query->where('barang_id', $request->barang_id);
        }

        // Filter berdasarkan Rentang Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $mutasi = $query->paginate(20);

        return response()->json($mutasi);
    }
}