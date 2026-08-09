<?php
namespace App\Http\Requests\Satuan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSatuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:50',
                Rule::unique('satuan', 'nama')->ignore($this->route('satuan')),
            ],
        ];
    }
}