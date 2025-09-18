<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle 403 Authorization exceptions
        if ($exception instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Je hebt geen toegang tot deze actie.',
                    'redirect' => '/'
                ], 403);
            }

            return redirect('/')
                ->with('error', 'Je hebt geen toegang tot deze pagina. Je bent doorverwezen naar het dashboard.');
        }

        // Handle 403 HTTP exceptions
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Je hebt geen toegang tot deze actie.',
                    'redirect' => '/'
                ], 403);
            }

            return redirect('/')
                ->with('error', 'Je hebt geen toegang tot deze pagina. Je bent doorverwezen naar het dashboard.');
        }

        return parent::render($request, $exception);
    }
}
