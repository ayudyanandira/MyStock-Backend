<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Satuan\StoreSatuanRequest;
use App\Http\Requests\Satuan\UpdateSatuanRequest;
use App\Http\Resources\SatuanResource;
use App\Models\Satuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SatuanController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $satuan = Satuan::latest()->paginate(10);
        return SatuanResource::collection($satuan);
    }

    public function store(StoreSatuanRequest $request): JsonResponse
    {
        $satuan = Satuan::create($request->validated());

        return response()->json([
            'message' => 'Satuan berhasil ditambahkan',
            'data' => new SatuanResource($satuan),
        ], 201);
    }

    public function show(Satuan $satuan): SatuanResource
    {
        return new SatuanResource($satuan);
    }

    public function update(UpdateSatuanRequest $request, Satuan $satuan): JsonResponse
    {
        $satuan->update($request->validated());

        return response()->json([
            'message' => 'Satuan berhasil diperbarui',
            'data' => new SatuanResource($satuan),
        ]);
    }

    public function destroy(Satuan $satuan): JsonResponse
    {
        $satuan->delete();

        return response()->json([
            'message' => 'Satuan berhasil dihapus',
        ]);
    }
}