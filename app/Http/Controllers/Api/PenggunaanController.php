<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penggunaan\StorePenggunaanRequest;
use App\Http\Requests\Penggunaan\UpdatePenggunaanRequest;
use App\Http\Resources\Penggunaan\PenggunaanResource;
use App\Models\Penggunaan;
use App\Services\PenggunaanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenggunaanController extends Controller
{
    public function __construct(private readonly PenggunaanService $service) {}

    public function index(Request $request)
    {
        return PenggunaanResource::collection($this->service->index($request));
    }

    public function store(StorePenggunaanRequest $request): JsonResponse
    {
        $penggunaan = $this->service->store($request->validated());

        return response()->json(['message' => 'Penggunaan berhasil dibuat.', 'data' => new PenggunaanResource($penggunaan)], 201);
    }

    public function show(Penggunaan $penggunaan): PenggunaanResource
    {
        return new PenggunaanResource($this->service->show($penggunaan));
    }

    public function update(UpdatePenggunaanRequest $request, Penggunaan $penggunaan): JsonResponse
    {
        return response()->json(['message' => 'Penggunaan berhasil diperbarui.', 'data' => new PenggunaanResource($this->service->update($penggunaan, $request->validated()))]);
    }

    public function destroy(Penggunaan $penggunaan): JsonResponse
    {
        $this->service->destroy($penggunaan);

        return response()->json(['message' => 'Penggunaan berhasil dihapus.']);
    }
}
