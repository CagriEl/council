<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // --- BU SATIR EKLENMELİ ---
        api: __DIR__.'/../routes/api.php', 
        // --------------------------
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(HandleCors::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e): void {
            $path = request()?->path() ?? '';
            if (str_contains($path, 'livewire') || str_contains($path, 'upload')) {
                \Illuminate\Support\Facades\Log::error('Upload/Livewire 500', [
                    'message' => $e->getMessage(),
                    'path' => $path,
                    'file' => $e->getFile().':'.$e->getLine(),
                ]);
            }
        });
    })->create();