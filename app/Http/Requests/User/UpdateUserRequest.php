<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['role_id' => ['required', 'integer', Rule::exists('roles', 'id')->whereIn('name', Role::allowedNames())], 'name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->route('user'))], 'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'], 'is_active' => ['required', 'boolean']];
    }
}
