<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'seller' => \App\Http\Middleware\SellerOnly::class,
            'seller.or.admin' => \App\Http\Middleware\SellerOrAdmin::class,
        ]);

        // Exclude webhook route from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhook/xoftware',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
