<?php

namespace App\Http\Requests\Penggunaan;

use Illuminate\Foundation\Http\FormRequest;

class StorePenggunaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:65535'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'integer', 'distinct', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.catatan' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
