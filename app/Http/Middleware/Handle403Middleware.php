<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Handle403Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if response is 403 Forbidden
        if ($response->getStatusCode() === 403) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Je hebt geen toegang tot deze actie.',
                    'redirect' => '/'
                ], 403);
            }

            return redirect('/')
                ->with('error', 'Je hebt geen toegang tot deze pagina. Je bent doorverwezen naar het dashboard.');
        }

        return $response;
    }
}
