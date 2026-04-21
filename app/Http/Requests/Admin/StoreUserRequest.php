<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'in:admin_desa,kepala_desa,super_admin'],
            'phone'    => ['nullable', 'string', 'regex:/^[0-9+\-\s]{8,15}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'      => 'Email ini sudah digunakan oleh pengguna lain.',
            'role.in'           => 'Role yang dipilih tidak valid.',
            'phone.regex'       => 'Format nomor HP tidak valid.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'nama',
            'email' => 'email',
            'role'  => 'role',
            'phone' => 'nomor HP',
        ];
    }
}