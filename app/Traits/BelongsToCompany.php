<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        // Automatically add company_id when creating new records
        static::creating(function ($model) {
            if (!$model->company_id) {
                $companyId = null;
                
                // Try to get company_id from authenticated user first
                if (Auth::check() && Auth::user()->company_id) {
                    $companyId = Auth::user()->company_id;
                }
                // If no auth user, try to get from tenant() helper
                elseif (function_exists('tenant_id') && tenant_id()) {
                    $companyId = tenant_id();
                }
                // Last fallback: try session
                elseif (session('current_company_id')) {
                    $companyId = session('current_company_id');
                }
                
                if ($companyId) {
                    $model->company_id = $companyId;
                }
            }
        });

        // Global scope to filter by current user's company or tenant
        static::addGlobalScope('company', function (Builder $builder) {
            $companyId = null;
            
            // Priority 1: From config (set by middleware)
            if (config('app.current_company_id')) {
                $companyId = config('app.current_company_id');
            }
            // Priority 2: From app container
            elseif (app()->bound('current_company')) {
                $company = app('current_company');
                $companyId = $company ? $company->id : null;
            }
            // Priority 3: From session
            elseif (session()->has('current_company_id')) {
                $companyId = session('current_company_id');
            }
            // Priority 4: From authenticated user
            elseif (Auth::check() && Auth::user()->company_id) {
                $companyId = Auth::user()->company_id;
            }
            
            if ($companyId) {
                $builder->where('company_id', $companyId);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Helper method to get records without company scope
    public function scopeWithoutCompanyScope($query)
    {
        return $query->withoutGlobalScope('company');
    }
}
