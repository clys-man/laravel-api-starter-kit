<?php

declare(strict_types=1);

use App\Exceptions\ApiExceptionRenderer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(
            using: function (Throwable $e, Request $request): ?Illuminate\Http\JsonResponse {
                if ($request->expectsJson()) {
                    return new ApiExceptionRenderer(
                        exception: $e,
                    )->render();
                }

                return null; // Fallback to default rendering.
            },
        );
    })
    ->create();
