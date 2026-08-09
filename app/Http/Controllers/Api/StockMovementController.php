<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockMovementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = StockMovement::with(['barang.kategori', 'barang.satuan', 'user'])
            ->latest('id');

        // Filter berdasarkan barang
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $movements = $query->paginate(15);

        return StockMovementResource::collection($movements);
    }

    public function show(StockMovement $stockMovement): StockMovementResource
    {
        $stockMovement->load(['barang.kategori', 'barang.satuan', 'user']);
        return new StockMovementResource($stockMovement);
    }
}