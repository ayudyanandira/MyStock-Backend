<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penerimaan\StorePenerimaanRequest;
use App\Http\Requests\Penerimaan\UpdatePenerimaanRequest;
use App\Http\Resources\Penerimaan\PenerimaanResource;
use App\Models\Penerimaan;
use App\Services\PenerimaanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenerimaanController extends Controller
{
    public function __construct(
        private readonly PenerimaanService $service
    ) {}

    public function index(Request $request)
    {
        return PenerimaanResource::collection(
            $this->service->index($request)
        );
    }

    public function store(
        StorePenerimaanRequest $request
    ): JsonResponse {

        $penerimaan = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'message' => 'Penerimaan berhasil dibuat.',
            'data' => new PenerimaanResource($penerimaan),
        ], 201);
    }

    public function show(
        Penerimaan $penerimaan
    ): PenerimaanResource {

        return new PenerimaanResource(
            $this->service->show($penerimaan)
        );
    }

    public function update(
        UpdatePenerimaanRequest $request,
        Penerimaan $penerimaan
    ): JsonResponse {

        $result = $this->service->update(
            $penerimaan,
            $request->validated()
        );

        return response()->json([
            'message' => 'Penerimaan berhasil diperbarui.',
            'data' => new PenerimaanResource($result),
        ]);
    }

    public function destroy(
        Penerimaan $penerimaan
    ): JsonResponse {

        $this->service->destroy($penerimaan);

        return response()->json([
            'message' => 'Penerimaan berhasil dihapus.',
        ]);
    }
}
