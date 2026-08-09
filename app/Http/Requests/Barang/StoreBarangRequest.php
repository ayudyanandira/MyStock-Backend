<?php

namespace App\Http\Requests\Barang;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_barang' => 'required|string|max:20|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori,id',
            'satuan_id'   => 'required|exists:satuan,id',
            'stok'        => 'nullable|integer|min:0',
            'stok_minimum'=> 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ];
    }
}