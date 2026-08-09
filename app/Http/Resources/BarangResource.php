<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'stok' => $this->stok,
            'stok_minimum' => $this->stok_minimum,
            'is_active' => $this->is_active,
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'satuan' => new SatuanResource($this->whenLoaded('satuan')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}