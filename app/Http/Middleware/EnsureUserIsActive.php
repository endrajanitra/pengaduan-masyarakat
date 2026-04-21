<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Cek setiap request apakah akun user masih aktif.
     *
     * Berguna untuk kasus: admin menonaktifkan akun warga saat user
     * sedang aktif ber-sesi — tanpa middleware ini user bisa terus
     * mengakses sampai session-nya habis sendiri.
     *
     * Daftarkan di bootstrap/app.php:
     *   $middleware->appendToGroup('web', EnsureUserIsActive::class);
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator desa untuk informasi lebih lanjut.',
                ]);
        }

        return $next($request);
    }
}