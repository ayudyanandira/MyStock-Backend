<?php
namespace App\Http\Requests\Satuan;

use Illuminate\Foundation\Http\FormRequest;

class StoreSatuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:50|unique:satuan,nama',
        ];
    }
}