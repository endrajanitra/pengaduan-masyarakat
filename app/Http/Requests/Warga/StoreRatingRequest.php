<?php

namespace App\Http\Requests\Warga;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $complaint = $this->route('complaint');

        // Hanya pemilik pengaduan
        if ($complaint->user_id !== auth()->id()) {
            return false;
        }

        // Hanya jika sudah resolved
        if (! $complaint->isResolved()) {
            return false;
        }

        // Belum pernah memberi rating
        return ! $complaint->hasRating();
    }

    public function rules(): array
    {
        return [
            'score'    => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required' => 'Penilaian bintang wajib dipilih.',
            'score.min'      => 'Penilaian minimal 1 bintang.',
            'score.max'      => 'Penilaian maksimal 5 bintang.',
            'feedback.max'   => 'Komentar terlalu panjang, maksimal 1000 karakter.',
        ];
    }

    protected function failedAuthorization(): never
    {
        $complaint = $this->route('complaint');

        if ($complaint->hasRating()) {
            abort(422, 'Pengaduan ini sudah dinilai sebelumnya.');
        }

        if (! $complaint->isResolved()) {
            abort(422, 'Pengaduan belum selesai, belum bisa dinilai.');
        }

        abort(403, 'Anda tidak berhak menilai pengaduan ini.');
    }
}