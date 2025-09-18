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
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'auth.simple' => \App\Http\Middleware\AuthMiddleware::class,
            'handle403' => \App\Http\Middleware\Handle403Middleware::class,
        ]);
        
        // Voeg 403 middleware toe aan alle web routes
        $middleware->web(append: [
            \App\Http\Middleware\Handle403Middleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Je hebt geen toegang tot deze actie.',
                    'redirect' => '/'
                ], 403);
            }

            return redirect('/')
                ->with('error', 'Je hebt geen toegang tot deze pagina. Je bent doorverwezen naar het dashboard.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Je hebt geen toegang tot deze actie.',
                        'redirect' => '/'
                    ], 403);
                }

                return redirect('/')
                    ->with('error', 'Je hebt geen toegang tot deze pagina. Je bent doorverwezen naar het dashboard.');
            }
        });
    })->create();
