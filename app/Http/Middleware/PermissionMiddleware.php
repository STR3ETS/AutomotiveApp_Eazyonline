<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        $user = Auth::user();

        // Check permission based on user role
        $hasPermission = match($permission) {
            'manage.company' => $user->canManageCompany(),
            'manage.employees' => $user->canManageEmployees(),
            'manage.cars' => $user->canManageCars(),
            'view.reports' => $user->canViewReports(),
            'assign.cars' => $user->canAssignCars(),
            default => false,
        };

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Geen toegang'], 403);
            }
            
            abort(403, 'Je hebt geen toegang tot deze functie.');
        }

        return $next($request);
    }
}
