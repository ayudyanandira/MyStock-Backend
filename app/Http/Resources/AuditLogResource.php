<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user'         => new UserResource($this->whenLoaded('user')),
            'modul'        => $this->modul,
            'aktivitas'    => $this->aktivitas,
            'reference_id' => $this->reference_id,
            'ip_address'   => $this->ip_address,
            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}