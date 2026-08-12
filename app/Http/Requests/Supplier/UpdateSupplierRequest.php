<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID supplier dari route agar validasi unique kode/email tidak bentrok dengan dirinya sendiri
        $supplierId = $this->route('supplier')?->id ?? $this->route('supplier');

        return [
            'kode_supplier' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
                Rule::unique('supplier', 'kode_supplier')->ignore($supplierId),
            ],
            'nama_supplier' => 'sometimes|required|string|max:150',
            'alamat'        => 'nullable|string',
            'no_telepon'    => 'nullable|string|max:20',
            'email'         => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('supplier', 'email')->ignore($supplierId),
            ],
            'jenis_barang'  => 'sometimes|required|string|max:150',
            'is_active'     => 'nullable|boolean',
        ];
    }
}