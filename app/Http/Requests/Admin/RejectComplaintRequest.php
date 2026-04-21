<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectComplaintRequest extends FormRequest
{
    private const REJECTABLE_STATUSES = ['submitted', 'in_review', 'in_progress'];

    public function authorize(): bool
    {
        $complaint = $this->route('complaint');

        return auth()->user()->isStaff()
            && in_array($complaint->status, self::REJECTABLE_STATUSES);
    }

    public function rules(): array
    {
        return [
            'admin_response' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_response.required' => 'Alasan penolakan wajib diisi.',
            'admin_response.min'      => 'Alasan penolakan terlalu pendek, minimal 20 karakter. Berikan alasan yang jelas kepada warga.',
            'admin_response.max'      => 'Alasan penolakan terlalu panjang, maksimal 2000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'admin_response' => 'alasan penolakan',
        ];
    }

    protected function failedAuthorization(): never
    {
        $complaint = $this->route('complaint');

        abort(422, "Pengaduan dengan status '{$complaint->status_label}' tidak dapat ditolak.");
    }
}