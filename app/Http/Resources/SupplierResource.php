<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'nama_supplier' => $this->nama_supplier,
            'alamat'        => $this->alamat,
            'no_telepon'    => $this->no_telepon,
            'email'         => $this->email,
            'jenis_barang'  => $this->jenis_barang,
            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}