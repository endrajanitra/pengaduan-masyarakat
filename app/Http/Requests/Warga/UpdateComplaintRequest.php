<?php

namespace App\Http\Requests\Warga;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        $complaint = $this->route('complaint');

        // Hanya pemilik pengaduan yang boleh edit
        if ($complaint->user_id !== auth()->id()) {
            return false;
        }

        // Hanya bisa edit jika masih draft atau submitted
        return in_array($complaint->status, [
            Complaint::STATUS_DRAFT,
            Complaint::STATUS_SUBMITTED,
        ]);
    }

    public function rules(): array
    {
        return [
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'title'        => ['required', 'string', 'min:10', 'max:255'],
            'description'  => ['required', 'string', 'min:30', 'max:5000'],
            'location'     => ['required', 'string', 'max:255'],
            'is_anonymous' => ['nullable', 'boolean'],
            'is_public'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori pengaduan wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'title.required'       => 'Judul pengaduan wajib diisi.',
            'title.min'            => 'Judul terlalu pendek, minimal 10 karakter.',
            'description.required' => 'Deskripsi pengaduan wajib diisi.',
            'description.min'      => 'Deskripsi terlalu pendek, minimal 30 karakter.',
            'location.required'    => 'Lokasi kejadian wajib diisi.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'is_public'    => $this->boolean('is_public', true),
        ]);
    }

    /**
     * Pesan error jika authorize() mengembalikan false.
     */
    protected function failedAuthorization(): never
    {
        $complaint = $this->route('complaint');

        if ($complaint->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengedit pengaduan ini.');
        }

        abort(403, 'Pengaduan yang sudah diproses tidak dapat diedit.');
    }
}