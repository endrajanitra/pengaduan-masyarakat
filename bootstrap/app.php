<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\ShareSiteSettings;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Middleware alias ─────────────────────────────────────────
        // Dipakai di route: ->middleware('role:admin_desa,super_admin')
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // ── Middleware global untuk semua request web ────────────────
        // Urutan penting: ShareSiteSettings sebelum EnsureUserIsActive
        // agar data desa tetap tersedia di halaman error/redirect
        $middleware->appendToGroup('web', [
            ShareSiteSettings::class,   // inject $siteName, $siteLogo, dll ke semua view
            EnsureUserIsActive::class,  // paksa logout jika akun dinonaktifkan
            PreventBackHistory::class,  // cegah tombol back browser setelah logout
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Halaman 403 — akses ditolak
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.',
            ], 403);
        });

        // Halaman 404 — halaman tidak ditemukan
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            return response()->view('errors.404', [], 404);
        });

        // Halaman 422 — data tidak valid / kondisi bisnis tidak terpenuhi
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($e->getStatusCode() === 422) {
                return back()->withErrors(['error' => $e->getMessage()])->withInput();
            }
        });

    })->create();