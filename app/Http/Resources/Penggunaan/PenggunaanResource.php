<?php

namespace App\Http\Resources\Penggunaan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_transaksi' => $this->nomor_transaksi,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'user' => ['id' => $this->user?->id, 'nama' => $this->user?->name],
            'detail' => $this->whenLoaded('detailPenggunaan', fn () => $this->detailPenggunaan->map(fn ($detail) => [
                'id' => $detail->id,
                'barang' => ['id' => $detail->barang->id, 'kode_barang' => $detail->barang->kode_barang, 'nama_barang' => $detail->barang->nama_barang],
                'jumlah' => $detail->jumlah,
                'catatan' => $detail->catatan,
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
