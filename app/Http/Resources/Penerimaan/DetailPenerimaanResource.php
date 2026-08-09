<?php

namespace App\Http\Resources\Penerimaan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPenerimaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'barang' => [

                'id' => $this->barang->id,

                'kode_barang' => $this->barang->kode_barang,

                'nama_barang' => $this->barang->nama_barang,
            ],

            'jumlah_pesanan' => $this->jumlah_pesanan,
            'jumlah_diterima' => $this->jumlah_diterima,
            'selisih' => $this->selisih,
            'status' => $this->status,
            'kondisi' => $this->kondisi,
            'keterangan' => $this->keterangan,
        ];
    }
}
