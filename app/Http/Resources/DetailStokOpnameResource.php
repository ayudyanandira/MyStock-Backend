<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailStokOpnameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'barang'      => new BarangResource($this->whenLoaded('barang')),
            'stok_sistem' => $this->stok_sistem,
            'stok_fisik'  => $this->stok_fisik,
            'selisih'     => $this->selisih,
            'keterangan'  => $this->keterangan,
        ];
    }
}