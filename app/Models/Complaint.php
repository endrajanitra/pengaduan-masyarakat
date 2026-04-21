<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'complaint_number',
        'title',
        'description',
        'status',
        'priority',
        'location',
        'admin_response',
        'handled_by',
        'handled_at',
        'resolved_at',
        'is_anonymous',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'is_public'    => 'boolean',
            'handled_at'   => 'datetime',
            'resolved_at'  => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // Konstanta Status & Prioritas
    // ----------------------------------------------------------------

    const STATUS_DRAFT       = 'draft';
    const STATUS_SUBMITTED   = 'submitted';
    const STATUS_IN_REVIEW   = 'in_review';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED    = 'resolved';
    const STATUS_REJECTED    = 'rejected';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';
    const PRIORITY_URGENT = 'urgent';

    const STATUS_LABELS = [
        'draft'       => 'Draft',
        'submitted'   => 'Dikirim',
        'in_review'   => 'Sedang Ditinjau',
        'in_progress' => 'Sedang Diproses',
        'resolved'    => 'Selesai',
        'rejected'    => 'Ditolak',
    ];

    const PRIORITY_LABELS = [
        'low'    => 'Rendah',
        'medium' => 'Sedang',
        'high'   => 'Tinggi',
        'urgent' => 'Mendesak',
    ];

    // ----------------------------------------------------------------
    // Boot — Auto-generate nomor tiket
    // ----------------------------------------------------------------

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Complaint $complaint) {
            if (empty($complaint->complaint_number)) {
                $complaint->complaint_number = self::generateComplaintNumber();
            }
        });

        /**
         * Setelah status berubah, catat ke complaint_logs secara otomatis.
         */
        static::updating(function (Complaint $complaint) {
            if ($complaint->isDirty('status')) {
                $complaint->complaintLogs()->create([
                    'changed_by' => auth()->id(),
                    'old_status' => $complaint->getOriginal('status'),
                    'new_status' => $complaint->status,
                ]);
            }

            // Catat handled_at saat pertama kali status masuk in_review
            if ($complaint->isDirty('status') && $complaint->status === self::STATUS_IN_REVIEW && is_null($complaint->handled_at)) {
                $complaint->handled_at = now();
                $complaint->handled_by = auth()->id();
            }

            // Catat resolved_at saat status menjadi resolved
            if ($complaint->isDirty('status') && $complaint->status === self::STATUS_RESOLVED) {
                $complaint->resolved_at = now();
            }
        });
    }

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Warga yang membuat pengaduan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin/petugas yang menangani pengaduan ini.
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Kategori pengaduan.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Semua lampiran file milik pengaduan ini.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class, 'complaint_id');
    }

    /**
     * Semua log perubahan status pengaduan ini.
     */
    public function complaintLogs(): HasMany
    {
        return $this->hasMany(ComplaintLog::class, 'complaint_id')->orderBy('created_at', 'asc');
    }

    /**
     * Semua komentar pada pengaduan ini (publik + internal).
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'complaint_id')->orderBy('created_at', 'asc');
    }

    /**
     * Hanya komentar publik (terlihat oleh warga).
     */
    public function publicComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'complaint_id')
            ->where('is_internal', false)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Hanya catatan internal (hanya terlihat staf).
     */
    public function internalComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'complaint_id')
            ->where('is_internal', true)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Semua notifikasi yang dipicu pengaduan ini.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'complaint_id');
    }

    /**
     * Rating kepuasan dari warga (satu pengaduan satu rating).
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class, 'complaint_id');
    }

    // ----------------------------------------------------------------
    // Helper: Status
    // ----------------------------------------------------------------

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_REJECTED]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITY_LABELS[$this->priority] ?? $this->priority;
    }

    /**
     * Nama pelapor — kembalikan "Anonim" jika is_anonymous = true
     * dan yang mengakses bukan staf.
     */
    public function getReporterNameAttribute(): string
    {
        if ($this->is_anonymous && ! auth()->check()) {
            return 'Anonim';
        }

        if ($this->is_anonymous && auth()->user() && ! auth()->user()->isStaff()) {
            return 'Anonim';
        }

        return $this->user->name ?? 'Tidak diketahui';
    }

    /**
     * Apakah pengaduan ini sudah punya rating dari warga.
     */
    public function hasRating(): bool
    {
        return $this->rating()->exists();
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_REJECTED]);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_URGENT)
            ->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_REJECTED]);
    }

    // ----------------------------------------------------------------
    // Static Helper
    // ----------------------------------------------------------------

    /**
     * Generate nomor tiket unik: ADU-2024-0001
     */
    public static function generateComplaintNumber(): string
    {
        $year  = now()->year;
        $prefix = "ADU-{$year}-";

        $last = self::where('complaint_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('complaint_number');

        $sequence = $last
            ? (int) substr($last, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}