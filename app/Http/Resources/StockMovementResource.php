<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'barang'         => new BarangResource($this->whenLoaded('barang')),
            'reference_type' => $this->reference_type,
            'reference_id'   => $this->reference_id,
            'qty_in'         => $this->qty_in,
            'qty_out'        => $this->qty_out,
            'stock_before'   => $this->stock_before,
            'stock_after'    => $this->stock_after,
            'created_by'     => new UserResource($this->whenLoaded('user')),
            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}