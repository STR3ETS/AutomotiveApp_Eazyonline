<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        // Check if user is active
        if (!Auth::user()->active) {
            Auth::logout();
            return redirect()->route('auth.login')
                ->withErrors(['Je account is gedeactiveerd.']);
        }

        // Check if company is active  
        if (!Auth::user()->company->active) {
            Auth::logout();
            return redirect()->route('auth.login')
                ->withErrors(['Je bedrijf is gedeactiveerd.']);
        }

        return $next($request);
    }
}
