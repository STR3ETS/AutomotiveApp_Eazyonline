<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function generateCSS($subdomain)
    {
        $company = Company::where('subdomain', $subdomain)->first();
        
        if (!$company) {
            abort(404);
        }
        
        $colors = $company->generateColorVariants();
        
        $css = view('css.company-theme', compact('company', 'colors'))->render();
        
        return response($css, 200, [
            'Content-Type' => 'text/css',
            'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
        ]);
    }
    
    public function showThemeSettings()
    {
        $company = tenant();
        
        if (!$company) {
            abort(404, 'No company found');
        }
        
        return view('admin.theme-settings', compact('company'));
    }
    
    public function updateThemeSettings(Request $request)
    {
        $company = tenant();
        
        if (!$company) {
            abort(404, 'No company found');
        }
        
        $request->validate([
            'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|max:2048',
        ]);
        
        $company->primary_color = $request->primary_color;
        
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $company->logo_path = $logoPath;
        }
        
        $company->save();
        
        return redirect()->back()->with('success', 'Theme updated successfully!');
    }
}
