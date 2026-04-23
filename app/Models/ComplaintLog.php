<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintLog extends Model
{
    /**
     * Tabel log hanya punya created_at, tidak ada updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'complaint_id',
        'changed_by',
        'old_status',
        'new_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Pengaduan yang log-nya ini.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    /**
     * User (admin/warga) yang melakukan perubahan status.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------

    /**
     * Label perubahan status dalam Bahasa Indonesia.
     * Contoh: "Dikirim → Sedang Ditinjau"
     */
    public function getStatusTransitionAttribute(): string
    {
        $labels = Complaint::STATUS_LABELS;
        $old    = $labels[$this->old_status] ?? $this->old_status ?? 'Baru';
        $new    = $labels[$this->new_status] ?? $this->new_status;

        return "{$old} → {$new}";
    }
}