<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Try to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->active) {
                Auth::logout();
                return back()->withErrors(['Je account is gedeactiveerd.']);
            }

            // Check if company is active
            if (!$user->company->active) {
                Auth::logout();
                return back()->withErrors(['Je bedrijf is gedeactiveerd.']);
            }

            // Update last login
            $user->last_login_at = now();
            $user->save();

            // Set tenant context
            $this->setTenantContext($user->company);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', "Welkom terug, {$user->name}!");
        }

        return back()->withErrors(['De inloggegevens zijn niet correct.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('auth.login')->with('success', 'Je bent uitgelogd.');
    }

    private function setTenantContext(Company $company): void
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
}
