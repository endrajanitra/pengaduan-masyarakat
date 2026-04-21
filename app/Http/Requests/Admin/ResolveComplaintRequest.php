<?php

namespace App\Http\Requests\Admin;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

class ResolveComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        $complaint = $this->route('complaint');

        return auth()->user()->isStaff()
            && $complaint->status === Complaint::STATUS_IN_PROGRESS;
    }

    public function rules(): array
    {
        return [
            'admin_response' => ['required', 'string', 'min:20', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_response.required' => 'Balasan resmi wajib diisi sebelum menyelesaikan pengaduan.',
            'admin_response.min'      => 'Balasan resmi terlalu pendek, minimal 20 karakter. Jelaskan tindakan yang sudah dilakukan.',
            'admin_response.max'      => 'Balasan resmi terlalu panjang, maksimal 5000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'admin_response' => 'balasan resmi',
        ];
    }

    protected function failedAuthorization(): never
    {
        $complaint = $this->route('complaint');

        if ($complaint->status !== Complaint::STATUS_IN_PROGRESS) {
            abort(422, "Pengaduan harus berstatus 'Sedang Diproses' sebelum dapat diselesaikan. Status saat ini: {$complaint->status_label}.");
        }

        abort(403, 'Anda tidak memiliki izin untuk menyelesaikan pengaduan.');
    }
}