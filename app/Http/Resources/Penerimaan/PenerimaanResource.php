<?php

namespace App\Http\Resources\Penerimaan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenerimaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nomor_transaksi' => $this->nomor_transaksi,

            'tanggal' => $this->tanggal,

            'supplier' => [

                'id' => $this->supplier?->id,

                'nama_supplier' => $this->supplier?->nama_supplier,
            ],

            'detail' => DetailPenerimaanResource::collection(
                $this->whenLoaded('detailPenerimaan')
            ),

            'foto' => FotoPenerimaanResource::collection(
                $this->whenLoaded('fotoPenerimaan')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
