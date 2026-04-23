<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ComplaintAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'complaint_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'file_size'  => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Pengaduan yang memiliki lampiran ini.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------

    /**
     * URL publik file lampiran.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Ukuran file dalam format yang mudah dibaca manusia.
     * Contoh: 1.2 MB, 450 KB
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1) . ' MB';
        }

        if ($bytes >= 1_024) {
            return round($bytes / 1_024, 0) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Apakah file ini adalah gambar.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    /**
     * Apakah file ini adalah PDF.
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }
}