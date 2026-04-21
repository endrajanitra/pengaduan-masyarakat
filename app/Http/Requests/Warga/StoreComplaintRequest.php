<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isWarga();
    }

    public function rules(): array
    {
        return [
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'title'         => ['required', 'string', 'min:10', 'max:255'],
            'description'   => ['required', 'string', 'min:30', 'max:5000'],
            'location'      => ['required', 'string', 'max:255'],
            'is_anonymous'  => ['nullable', 'boolean'],
            'is_public'     => ['nullable', 'boolean'],
            'attachments'   => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:5120', // 5 MB per file
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'    => 'Kategori pengaduan wajib dipilih.',
            'category_id.exists'      => 'Kategori yang dipilih tidak valid.',
            'title.required'          => 'Judul pengaduan wajib diisi.',
            'title.min'               => 'Judul terlalu pendek, minimal 10 karakter.',
            'title.max'               => 'Judul terlalu panjang, maksimal 255 karakter.',
            'description.required'    => 'Deskripsi pengaduan wajib diisi.',
            'description.min'         => 'Deskripsi terlalu pendek, minimal 30 karakter. Jelaskan masalah Anda secara detail.',
            'description.max'         => 'Deskripsi terlalu panjang, maksimal 5000 karakter.',
            'location.required'       => 'Lokasi kejadian wajib diisi.',
            'attachments.max'         => 'Maksimal 5 file lampiran.',
            'attachments.*.mimes'     => 'Format file tidak didukung. Gunakan JPG, PNG, PDF, DOC, atau DOCX.',
            'attachments.*.max'       => 'Ukuran setiap file maksimal 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id'  => 'kategori',
            'title'        => 'judul pengaduan',
            'description'  => 'deskripsi',
            'location'     => 'lokasi kejadian',
            'attachments'  => 'lampiran',
        ];
    }

    /**
     * Persiapkan data sebelum validasi dijalankan.
     * Konversi checkbox boolean yang dikirim sebagai string '1'/'0'.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'is_public'    => $this->boolean('is_public', true),
        ]);
    }
}