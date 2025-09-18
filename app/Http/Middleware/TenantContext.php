<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract tenant_id from route parameter
        $tenantId = $request->route('tenant');

        if ($tenantId) {
            // Store in config for services to use
            config(['app.current_tenant_id' => $tenantId]);

            // Store in app container
            app()->instance('current_tenant_id', $tenantId);

            // Optional: Load and store the tenant model
            $tenant = \App\Models\Company::find($tenantId);
            if ($tenant) {
                app()->instance('current_tenant', $tenant);
                config(['app.current_company_id' => $tenantId]);
            }
        } else {
            // Fallback to existing tenant resolution logic
            $tenantId = tenant_id();
            if ($tenantId) {
                config(['app.current_tenant_id' => $tenantId]);
                app()->instance('current_tenant_id', $tenantId);
            }
        }

        return $next($request);
    }
}
