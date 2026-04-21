<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Semua staf boleh menambahkan komentar
        return auth()->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'body'        => ['required', 'string', 'min:5', 'max:2000'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Isi komentar wajib diisi.',
            'body.min'      => 'Komentar terlalu pendek, minimal 5 karakter.',
            'body.max'      => 'Komentar terlalu panjang, maksimal 2000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'body' => 'komentar',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_internal' => $this->boolean('is_internal'),
        ]);
    }
}