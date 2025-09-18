<?php

if (!function_exists('tenant')) {
    /**
     * Get the current tenant/company
     */
    function tenant(): ?\App\Models\Company
    {
        // Try to get from app container first
        if (app()->bound('current_company')) {
            return app('current_company');
        }

        // Try to get from session
        if (session()->has('current_company_id')) {
            $company = \App\Models\Company::find(session('current_company_id'));
            if ($company) {
                app()->instance('current_company', $company);
                return $company;
            }
        }

        // Try to get from authenticated user
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->company_id ?? null) {
            $company = \App\Models\Company::find(\Illuminate\Support\Facades\Auth::user()->company_id);
            if ($company) {
                app()->instance('current_company', $company);
                return $company;
            }
        }

        return null;
    }
}

if (!function_exists('tenant_id')) {
    /**
     * Get the current tenant ID
     */
    function tenant_id(): ?int
    {
        $tenant = tenant();
        return $tenant ? $tenant->id : session('current_company_id');
    }
}

if (!function_exists('is_multi_tenant_enabled')) {
    /**
     * Check if multi-tenancy is enabled
     */
    function is_multi_tenant_enabled(): bool
    {
        return config('app.multi_tenant_enabled', true);
    }
}
