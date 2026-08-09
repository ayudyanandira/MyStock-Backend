<?php

namespace App\Http\Requests\Penerimaan;

use Illuminate\Foundation\Http\FormRequest;

class StorePenerimaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'supplier_id' => [
                'required',
                'exists:supplier,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.barang_id' => [
                'required',
                'integer',
                'distinct',
                'exists:barang,id',
            ],

            'items.*.jumlah_pesanan' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.jumlah_diterima' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.kondisi' => [
                'required',
                'string',
                'max:100',
            ],
            'items.*.keterangan' => [
                'nullable',
                'string',
                'max:65535',
            ],

            'photos' => [
                'nullable',
                'array',
            ],

            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'supplier_id.required' => 'Supplier wajib dipilih.',

            'supplier_id.exists' => 'Supplier tidak ditemukan.',

            'items.required' => 'Minimal terdapat satu barang.',

            'items.min' => 'Minimal terdapat satu barang.',

            'items.*.barang_id.distinct' => 'Barang tidak boleh dipilih lebih dari satu kali.',

            'photos.*.image' => 'File harus berupa gambar.',

            'photos.*.max' => 'Ukuran maksimal foto adalah 5 MB.',
        ];
    }
}
