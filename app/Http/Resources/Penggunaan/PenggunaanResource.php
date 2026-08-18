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
            'user' => [
                'id' => $this->user?->id, 
                'nama' => $this->user?->name
            ],
            'detail' => $this->whenLoaded('detailPenggunaan', fn () => $this->detailPenggunaan->map(fn ($detail) => [
                'id' => $detail->id,
                'barang' => [
                    'id' => $detail->barang?->id, 
                    'kode_barang' => $detail->barang?->kode_barang, 
                    'nama_barang' => $detail->barang?->nama_barang,
                    'satuan' => [
                        'id' => $detail->barang?->satuan?->id,
                        'nama_satuan' => $detail->barang?->satuan?->nama_satuan 
                            ?? $detail->barang?->satuan?->nama 
                            ?? '-',
                    ],
                ],
                'jumlah' => $detail->jumlah ?? $detail->jumlah_keluar ?? 0,
                'catatan' => $detail->catatan ?? $detail->keterangan,
                // Properti fallback satuan di level detail agar kompatibel dengan komponen cetak
                'satuan' => [
                    'id' => $detail->barang?->satuan?->id,
                    'nama_satuan' => $detail->barang?->satuan?->nama_satuan 
                        ?? $detail->barang?->satuan?->nama 
                        ?? '-',
                ],
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}