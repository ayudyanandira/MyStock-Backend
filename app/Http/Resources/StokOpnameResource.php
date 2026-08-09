<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StokOpnameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'nomor_transaksi' => $this->nomor_transaksi,
            'tanggal'         => $this->tanggal?->format('Y-m-d'),
            'created_by'      => new UserResource($this->whenLoaded('user')),
            'details'         => DetailStokOpnameResource::collection($this->whenLoaded('details')),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}