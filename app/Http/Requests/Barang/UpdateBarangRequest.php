<?php

namespace App\Http\Requests\Barang;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_barang' => [
                'required',
                'string',
                'max:20',
                Rule::unique('barang', 'kode_barang')->ignore($this->route('barang')),
            ],
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori,id',
            'satuan_id'   => 'required|exists:satuan,id',
            'stok'        => 'nullable|integer|min:0',
            'stok_minimum'=> 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ];
    }
}