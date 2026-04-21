<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // publik, siapa saja boleh daftar
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'nik'      => ['required', 'digits:16', 'unique:users,nik'],
            'phone'    => ['required', 'string', 'regex:/^[0-9+\-\s]{8,15}$/'],
            'address'  => ['required', 'string', 'min:10', 'max:500'],
            'rt_rw'    => ['required', 'string', 'regex:/^\d{3}\/\d{3}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Email ini sudah terdaftar. Silakan gunakan email lain.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
            'nik.required'      => 'NIK wajib diisi.',
            'nik.digits'        => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'        => 'NIK ini sudah terdaftar di sistem.',
            'phone.regex'       => 'Format nomor HP tidak valid.',
            'address.min'       => 'Alamat terlalu pendek, minimal 10 karakter.',
            'rt_rw.regex'       => 'Format RT/RW tidak valid. Gunakan format 001/002.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'    => 'nama lengkap',
            'email'   => 'email',
            'nik'     => 'NIK',
            'phone'   => 'nomor HP',
            'address' => 'alamat',
            'rt_rw'   => 'RT/RW',
        ];
    }
}