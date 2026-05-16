<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // CSRF Exceptions
        $middleware->validateCsrfTokens(except: [
            'api/stripe/webhook',
            'api/*', // Alle API-Routen von CSRF ausnehmen
        ]);

        // Custom Middleware Aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Logging aller Exceptions
        $exceptions->report(function (Throwable $e) {
            logger()->error($e);
        });

        // JSON Responses für API-Routen
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                return response()->json([
                    'message' => $e->getMessage(),
                ], $status);
            }
        });

    })
    ->create();