<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'desa_name'       => ['required', 'string', 'max:100'],
            'desa_kecamatan'  => ['required', 'string', 'max:100'],
            'desa_kabupaten'  => ['required', 'string', 'max:100'],
            'desa_provinsi'   => ['required', 'string', 'max:100'],
            'alamat_kantor'   => ['required', 'string', 'max:500'],
            'phone_kantor'    => ['nullable', 'string', 'max:20'],
            'email_kantor'    => ['nullable', 'email', 'max:100'],
            'kepala_desa'     => ['required', 'string', 'max:100'],
            'app_description' => ['nullable', 'string', 'max:500'],
            'logo'            => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'desa_name.required'      => 'Nama desa wajib diisi.',
            'desa_kecamatan.required' => 'Kecamatan wajib diisi.',
            'desa_kabupaten.required' => 'Kabupaten wajib diisi.',
            'desa_provinsi.required'  => 'Provinsi wajib diisi.',
            'alamat_kantor.required'  => 'Alamat kantor desa wajib diisi.',
            'kepala_desa.required'    => 'Nama kepala desa wajib diisi.',
            'email_kantor.email'      => 'Format email kantor tidak valid.',
            'logo.image'              => 'File logo harus berupa gambar.',
            'logo.mimes'              => 'Format logo harus PNG, JPG, atau JPEG.',
            'logo.max'                => 'Ukuran logo maksimal 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'desa_name'      => 'nama desa',
            'desa_kecamatan' => 'kecamatan',
            'desa_kabupaten' => 'kabupaten',
            'desa_provinsi'  => 'provinsi',
            'alamat_kantor'  => 'alamat kantor',
            'phone_kantor'   => 'telepon kantor',
            'email_kantor'   => 'email kantor',
            'kepala_desa'    => 'nama kepala desa',
            'app_description'=> 'deskripsi aplikasi',
        ];
    }
}