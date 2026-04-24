<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class ActivateUserAfterVerification
{
    /**
     * Listener ini dipanggil otomatis oleh Laravel setiap kali
     * user berhasil memverifikasi emailnya.
     *
     * Event Verified dijalankan oleh Laravel di dalam:
     * EmailVerificationRequest::fulfill()
     * yang dipanggil saat user klik link verifikasi di email.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Aktifkan akun jika belum aktif
        if (! $user->is_active) {
            $user->update(['is_active' => true]);
        }
    }
}