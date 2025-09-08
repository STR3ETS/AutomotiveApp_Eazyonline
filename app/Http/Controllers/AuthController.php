<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Simple hardcoded credentials for each company
        $credentials = [
            'piet' => ['password' => 'piet123', 'company_id' => 1],
            'snelle' => ['password' => 'snelle123', 'company_id' => 2],
            'premium' => ['password' => 'premium123', 'company_id' => 3],
            'jan' => ['password' => 'jan123', 'company_id' => 4],
        ];

        $username = $request->username;
        $password = $request->password;

        if (isset($credentials[$username]) && $credentials[$username]['password'] === $password) {
            $companyId = $credentials[$username]['company_id'];
            $company = Company::find($companyId);

            if ($company) {
                // Store in session
                session([
                    'authenticated' => true,
                    'current_company_id' => $company->id,
                    'username' => $username
                ]);

                // Store in app container
                app()->instance('current_company', $company);
                config(['app.current_company_id' => $company->id]);

                return redirect()->route('dashboard')->with('success', "Welkom bij {$company->name}!");
            }
        }

        return back()->withErrors(['De inloggegevens zijn niet correct.']);
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('auth.login')->with('success', 'Je bent uitgelogd.');
    }
}
