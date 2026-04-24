<?php

namespace App\Providers;

use App\Listeners\ActivateUserAfterVerification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Daftar event beserta listener-nya.
     */
    protected $listen = [

        // Ketika user daftar → kirim email verifikasi
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Ketika user klik link verifikasi → aktifkan akun
        Verified::class => [
            ActivateUserAfterVerification::class,
        ],

    ];

    public function boot(): void
    {
        //
    }
}