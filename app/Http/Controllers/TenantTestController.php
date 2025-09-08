<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantTestController extends Controller
{
    public function index()
    {
        $currentCompany = tenant();
        
        // Stats with tenant filtering
        $stats = [
            'companies' => Company::count(),
            'cars' => Car::count(), // This should be filtered by global scope
            'customers' => Customer::count(),
            'appointments' => Appointment::count(),
        ];
        
        // Get cars for current tenant (filtered by global scope)
        $cars = Car::with('company')->get();
        
        // Get cars WITHOUT scope for comparison
        $allCars = Car::withoutGlobalScope('company')->with('company')->get();
        
        // Get all companies with car counts (admin view)
        $companies = Company::withCount('cars')->get();
        
        return view('tenant-test', compact('currentCompany', 'stats', 'cars', 'allCars', 'companies'));
    }
    
    public function setTenant($companyId)
    {
        $company = Company::findOrFail($companyId);
        
        // Store company in session for testing
        session(['current_company_id' => $company->id]);
        
        // Store in app container
        app()->instance('current_company', $company);
        
        return redirect()->route('tenant.test')->with('success', "Switched to company: {$company->name}");
    }
}
