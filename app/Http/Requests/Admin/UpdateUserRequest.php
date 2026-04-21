<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$userId}"],
            'role'  => ['required', 'in:warga,admin_desa,kepala_desa,super_admin'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s]{8,15}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'role.in'      => 'Role yang dipilih tidak valid.',
            'phone.regex'  => 'Format nomor HP tidak valid.',
        ];
    }
}