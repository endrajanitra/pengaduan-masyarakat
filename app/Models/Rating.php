<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Rating extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'score',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'score'      => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Pengaduan yang dinilai.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    /**
     * Warga yang memberikan penilaian.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------

    /**
     * Label bintang dalam teks.
     * Contoh: score 5 → "Sangat Puas"
     */
    public function getScoreLabelAttribute(): string
    {
        return match ($this->score) {
            1 => 'Sangat Tidak Puas',
            2 => 'Tidak Puas',
            3 => 'Cukup',
            4 => 'Puas',
            5 => 'Sangat Puas',
            default => '-',
        };
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeHighRated(Builder $query, int $min = 4): Builder
    {
        return $query->where('score', '>=', $min);
    }

    public function scopeLowRated(Builder $query, int $max = 2): Builder
    {
        return $query->where('score', '<=', $max);
    }
}