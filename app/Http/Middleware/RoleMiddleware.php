<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Cek apakah user yang login memiliki salah satu role yang diizinkan.
     *
     * Penggunaan di route:
     *   ->middleware('role:admin_desa,kepala_desa,super_admin')
     *   ->middleware('role:warga')
     *   ->middleware('role:super_admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Pastikan user sudah login (seharusnya sudah ditangani middleware 'auth' sebelumnya)
        if (! $user) {
            return redirect()->route('login');
        }

        // Pastikan akun aktif
        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        }

        // Cek apakah role user ada dalam daftar role yang diizinkan
        if (! in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}