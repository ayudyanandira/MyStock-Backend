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

        // 1. Tangkap parameter search jika suatu saat frontend mengirimnya
        $search = $request->input('search') ?? $request->input('q') ?? $request->input('keyword');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ILIKE', "%{$search}%")
                ->orWhere('kode_barang', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // 2. Urutkan A-Z
        $query->orderBy('nama_barang', 'asc');

        // 3. KUNCI FIX: Kembalikan SEMUA data tanpa dipaginasi (atau set limit sangat tinggi)
        // Ini membuat frontend menerima 100% data master barang untuk di-search di browser
        if ($request->has('all') || $request->boolean('all') || !$request->has('per_page')) {
            return BarangResource::collection($query->get());
        }

        // Jika frontend mengirim per_page khusus, naikkan batasnya menjadi 1000
        $perPage = $request->get('per_page', 1000);

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