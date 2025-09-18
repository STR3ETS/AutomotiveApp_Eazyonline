<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First priority: Company ID from URL parameter
        $companyId = $request->route('company') ?? $request->get('company');
        
        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                $this->setCurrentCompany($company);
            }
        } else {
            // Second priority: Subdomain detection
            $subdomain = $this->getSubdomain($request);
            
            if ($subdomain && $subdomain !== 'www') {
                $company = Company::where('subdomain', $subdomain)->first();
                
                if (!$company) {
                    abort(404, 'Company not found');
                }
                
                if (!$company->active) {
                    abort(403, 'Company account is inactive');
                }
                
                $this->setCurrentCompany($company);
            } else {
                // Third priority: Session fallback
                if (session()->has('current_company_id')) {
                    $company = Company::find(session('current_company_id'));
                    if ($company) {
                        $this->setCurrentCompany($company);
                    }
                }
            }
        }
        
        return $next($request);
    }
    
    private function setCurrentCompany(Company $company): void
    {
        // Store current company in app container
        app()->instance('current_company', $company);
        
        // Store in session for persistence
        session(['current_company_id' => $company->id]);
        
        // Store in config for easy access
        config(['app.current_company_id' => $company->id]);
        
        // Add company info to view
        view()->share('currentCompany', $company);
    }
    
    private function getSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // For localhost development (like laragon)
        if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
            return null; // Skip subdomain detection for localhost
        }
        
        // Return subdomain if exists
        return count($parts) > 2 ? $parts[0] : null;
    }
}
