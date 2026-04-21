<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    /**
     * Tabel notifikasi hanya punya created_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'complaint_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read'    => 'boolean',
            'read_at'    => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Konstanta Tipe Notifikasi
    // ----------------------------------------------------------------

    const TYPE_COMPLAINT_SUBMITTED  = 'complaint_submitted';
    const TYPE_STATUS_UPDATED       = 'status_updated';
    const TYPE_NEW_COMMENT          = 'new_comment';
    const TYPE_COMPLAINT_RESOLVED   = 'complaint_resolved';
    const TYPE_COMPLAINT_REJECTED   = 'complaint_rejected';
    const TYPE_NEW_COMPLAINT_ADMIN  = 'new_complaint_admin';
    const TYPE_RATING_REQUESTED     = 'rating_requested';

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * User pemilik notifikasi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Pengaduan yang memicu notifikasi ini (nullable).
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------

    /**
     * Tandai notifikasi ini sebagai sudah dibaca.
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    // ----------------------------------------------------------------
    // Static Helper — Kirim notifikasi ke user tertentu
    // ----------------------------------------------------------------

    /**
     * Buat notifikasi baru untuk seorang user.
     *
     * Contoh penggunaan:
     * Notification::send($user->id, Notification::TYPE_STATUS_UPDATED, $complaint, [
     *     'title'   => 'Pengaduan diperbarui',
     *     'message' => 'Status pengaduan Anda berubah menjadi Sedang Diproses.',
     * ]);
     */
    public static function send(int $userId, string $type, ?Complaint $complaint, array $data): self
    {
        return self::create([
            'user_id'      => $userId,
            'complaint_id' => $complaint?->id,
            'type'         => $type,
            'title'        => $data['title'],
            'message'      => $data['message'],
        ]);
    }
}