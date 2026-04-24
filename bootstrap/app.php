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

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        $middleware->appendToGroup('web', [
            ShareSiteSettings::class,
            EnsureUserIsActive::class,
            PreventBackHistory::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.',
            ], 403);
        });

        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            return response()->view('errors.404', [], 404);
        });

    })

    // Daftarkan AppServiceProvider agar listener Verified aktif
    ->withProviders([
        App\Providers\AppServiceProvider::class,
    ])

    ->create();