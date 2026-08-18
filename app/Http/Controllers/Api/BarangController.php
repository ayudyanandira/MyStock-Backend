<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barang\StoreBarangRequest;
use App\Http\Requests\Barang\UpdateBarangRequest;
use App\Http\Resources\BarangResource;
use App\Models\Barang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BarangController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Barang::with(['kategori', 'satuan']);

        // 1. Filter Pencarian Case-Insensitive untuk PostgreSQL
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ILIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'ILIKE', "%{$search}%");
            });
        }

        // 2. Filter Kategori (opsional jika frontend mengirim kategori_id)
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // 3. Tetap Alphabetical A-Z
        $query->orderBy('nama_barang', 'asc');

        // Jika frontend minta seluruh data (misal untuk dropdown)
        if ($request->has('all') && $request->boolean('all')) {
            return BarangResource::collection($query->get());
        }

        $perPage = $request->get('per_page', 50);

        return BarangResource::collection($query->paginate($perPage));
    }

    public function store(StoreBarangRequest $request): JsonResponse
    {
        $barang = Barang::create($request->validated());
        $barang->load(['kategori', 'satuan']);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data' => new BarangResource($barang),
        ], 201);
    }

    public function show(Barang $barang): BarangResource
    {
        $barang->load(['kategori', 'satuan']);
        return new BarangResource($barang);
    }

    public function update(UpdateBarangRequest $request, Barang $barang): JsonResponse
    {
        $barang->update($request->validated());
        $barang->load(['kategori', 'satuan']);

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data' => new BarangResource($barang),
        ]);
    }

    public function destroy(Barang $barang): JsonResponse
    {
        $barang->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus',
        ]);
    }
}