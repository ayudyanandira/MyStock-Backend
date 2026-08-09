<?php

namespace App\Http\Resources\Penerimaan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FotoPenerimaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nama_file' => $this->nama_file,

            'path_file' => Storage::disk('public')->url($this->path_file),

            'mime_type' => $this->mime_type,

            'ukuran_file' => $this->ukuran_file,

            'created_at' => $this->created_at,
        ];
    }
}
