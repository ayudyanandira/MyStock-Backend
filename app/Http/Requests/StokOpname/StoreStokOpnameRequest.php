<?php

namespace App\Http\Requests\StokOpname;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_transaksi'      => 'required|string|max:40|unique:stok_opname,nomor_transaksi',
            'tanggal'              => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.barang_id'    => 'required|exists:barang,id',
            'items.*.stok_fisik'   => 'required|numeric|min:0',
            'items.*.keterangan'   => 'nullable|string',
        ];
    }
}