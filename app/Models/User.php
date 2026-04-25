<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'nik',
        'address',
        'rt_rw',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Pengaduan yang dibuat oleh user ini (sebagai pelapor).
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    /**
     * Pengaduan yang ditangani oleh user ini (sebagai admin/petugas).
     */
    public function handledComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'handled_by');
    }

    /**
     * Semua komentar yang ditulis oleh user ini.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * Semua notifikasi milik user ini.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Semua rating yang diberikan oleh user ini.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    /**
     * Semua log perubahan status yang dilakukan oleh user ini.
     */
    public function complaintLogs(): HasMany
    {
        return $this->hasMany(ComplaintLog::class, 'changed_by');
    }

    // ----------------------------------------------------------------
    // Helper: Role Checks
    // ----------------------------------------------------------------

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    public function isAdminDesa(): bool
    {
        return $this->role === 'admin_desa';
    }

    public function isKepalaDesa(): bool
    {
        return $this->role === 'kepala_desa';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user memiliki akses level staf (admin ke atas).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin_desa', 'kepala_desa', 'super_admin']);
    }

    // ----------------------------------------------------------------
    // Helper: Notifikasi
    // ----------------------------------------------------------------

    /**
     * Jumlah notifikasi yang belum dibaca.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }
    
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'is_active'         => true,
        ])->save();
    }
}