<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Tambahkan HTTP header agar browser tidak meng-cache halaman
     * yang memerlukan autentikasi. Mencegah user menekan tombol "Back"
     * setelah logout dan melihat halaman dashboard yang ter-cache.
     *
     * Daftarkan di bootstrap/app.php pada grup 'web':
     *   $middleware->appendToGroup('web', PreventBackHistory::class);
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya terapkan pada halaman yang memerlukan autentikasi
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}