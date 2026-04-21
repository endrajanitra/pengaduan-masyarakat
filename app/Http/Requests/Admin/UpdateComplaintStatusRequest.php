<?php

namespace App\Http\Requests\Admin;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComplaintStatusRequest extends FormRequest
{
    // Peta transisi status yang diizinkan (sama dengan di controller)
    private const ALLOWED_TRANSITIONS = [
        'submitted'   => ['in_review', 'rejected'],
        'in_review'   => ['in_progress', 'rejected'],
        'in_progress' => ['resolved', 'rejected'],
        'resolved'    => [],
        'rejected'    => [],
        'draft'       => [],
    ];

    public function authorize(): bool
    {
        return auth()->user()->isStaff();
    }

    public function rules(): array
    {
        $complaint      = $this->route('complaint');
        $allowedStatuses = self::ALLOWED_TRANSITIONS[$complaint->status] ?? [];

        return [
            'status' => ['required', 'string', Rule::in($allowedStatuses)],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        $complaint      = $this->route('complaint');
        $allowedStatuses = self::ALLOWED_TRANSITIONS[$complaint->status] ?? [];
        $allowedLabels  = array_map(
            fn ($s) => Complaint::STATUS_LABELS[$s] ?? $s,
            $allowedStatuses
        );

        $allowed = empty($allowedLabels)
            ? 'tidak ada transisi yang tersedia'
            : implode(' atau ', $allowedLabels);

        return [
            'status.required' => 'Status baru wajib dipilih.',
            'status.in'       => "Status tidak valid. Dari status saat ini hanya bisa diubah ke: {$allowed}.",
            'notes.max'       => 'Catatan terlalu panjang, maksimal 1000 karakter.',
        ];
    }
}