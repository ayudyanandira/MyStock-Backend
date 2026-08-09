<?php
namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_supplier' => 'required|string|max:150',
            'alamat'        => 'nullable|string',
            'no_telepon'    => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'jenis_barang'  => 'required|string|max:150',
        ];
    }
}