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
                'sometimes', 
                'required',
                'string',
                'max:20',
                Rule::unique('barang', 'kode_barang')->ignore($this->route('barang')),
            ],
            'nama_barang'  => 'sometimes|required|string|max:150', // <-- Tambahkan sometimes
            'kategori_id'  => 'sometimes|required|exists:kategori,id', // <-- Tambahkan sometimes
            'satuan_id'    => 'sometimes|required|exists:satuan,id', // <-- Tambahkan sometimes
            'stok'         => 'nullable|numeric|min:0', // Sekalian ganti ke numeric agar aman untuk desimal!
            'stok_minimum' => 'nullable|numeric|min:0',
            'is_active'    => 'nullable|boolean',
        ];
    }
}