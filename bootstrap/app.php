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
            'role' => \App\Http\Middleware\CheckRole::class,
            'ngrok' => \App\Http\Middleware\HandleNgrok::class,
            'auth-ngrok' => \App\Http\Middleware\HandleAuthNgrok::class,
            'audit' => \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
        
        // Thêm middleware ngrok vào web group (chỉ cho routes cần thiết)
        $middleware->web(append: [
            \App\Http\Middleware\HandleNgrok::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
