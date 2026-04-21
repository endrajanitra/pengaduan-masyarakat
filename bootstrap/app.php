<?php

use App\Http\Middleware\RoleMiddleware;
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

        // Daftarkan alias 'role' agar bisa dipakai di route
        // Contoh: ->middleware('role:admin_desa,super_admin')
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Tambahkan middleware global untuk web group jika diperlukan
        // $middleware->web(append: [...]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Tangani error 403 (akses ditolak) dengan view khusus
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });

        // Tangani error 404 dengan view khusus
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return response()->view('errors.404', [], 404);
        });

    })->create();