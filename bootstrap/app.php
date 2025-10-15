<?php

// bootstrap/app.php
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Breeze auth
            Route::middleware(['web'])->group(base_path('routes/auth.php'));

            // Admin routes: only web here!
            Route::prefix('admin')
                ->as('admin.')
                ->middleware(['web'])
                ->group(base_path('routes/admin.php'));
            // Shop routes
            Route::prefix('shop')
                ->as('shop.')
                ->middleware(['web'])
                ->group(base_path('routes/shop.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'ensure.admin' => EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
