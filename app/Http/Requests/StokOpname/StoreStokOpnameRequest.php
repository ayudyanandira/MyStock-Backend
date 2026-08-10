<?php

namespace App\Http\Requests\StokOpname;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Di dalam file StoreStokOpnameRequest.php
    public function rules(): array
    {
        return [
            'nomor_transaksi' => 'nullable|string', // <-- UBAH KE NULLABLE
            'tanggal'         => 'required|date',
            'items'           => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.stok_fisik' => 'required|numeric|min:0',
        ];
    }
}