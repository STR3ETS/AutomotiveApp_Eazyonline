<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    public function index()
    {
        $company = app('current_company');
        
        return view('company-settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        $company = app('current_company');
        
        $validated = $request->validate([
            // Bedrijfsgegevens
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:50|unique:companies,subdomain,' . $company->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'kvk_number' => 'nullable|string|max:20',
            'btw_number' => 'nullable|string|max:30',
            
            // Werkplaats instellingen
            'opening_hours' => 'nullable|array',
            'max_appointments_per_day' => 'nullable|integer|min:1|max:50',
            'default_appointment_duration' => 'nullable|integer|min:15|max:480',
            'repair_hourly_rate' => 'nullable|numeric|min:0|max:500',
            
            // Pipeline instellingen
            'auto_progress_enabled' => 'boolean',
            'notification_emails' => 'nullable|array',
            'checklist_auto_complete' => 'boolean',
            
            // Marktplaats instellingen
            'marketplace_auto_publish' => 'boolean',
            'default_warranty_months' => 'nullable|integer|min:0|max:60',
            'show_company_logo' => 'boolean',
            
            // Financiële instellingen
            'currency' => 'required|string|max:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'invoice_prefix' => 'nullable|string|max:10',
            'invoice_counter' => 'nullable|integer|min:1',
            
            // Kleurinstellingen
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            
            // Logo upload
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete($company->logo);
            }
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Handle settings as JSON
        $settings = [
            'opening_hours' => $validated['opening_hours'] ?? [],
            'max_appointments_per_day' => $validated['max_appointments_per_day'] ?? 10,
            'default_appointment_duration' => $validated['default_appointment_duration'] ?? 60,
            'repair_hourly_rate' => $validated['repair_hourly_rate'] ?? 75,
            'auto_progress_enabled' => $validated['auto_progress_enabled'] ?? false,
            'notification_emails' => $validated['notification_emails'] ?? [],
            'checklist_auto_complete' => $validated['checklist_auto_complete'] ?? false,
            'marketplace_auto_publish' => $validated['marketplace_auto_publish'] ?? false,
            'default_warranty_months' => $validated['default_warranty_months'] ?? 6,
            'show_company_logo' => $validated['show_company_logo'] ?? true,
            'currency' => $validated['currency'] ?? 'EUR',
            'tax_rate' => $validated['tax_rate'] ?? 21,
            'invoice_prefix' => $validated['invoice_prefix'] ?? 'INV',
            'invoice_counter' => $validated['invoice_counter'] ?? 1,
            'secondary_color' => $validated['secondary_color'] ?? '#6b7280',
        ];

        $company->update([
            'name' => $validated['name'],
            'subdomain' => $validated['subdomain'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'website' => $validated['website'],
            'kvk_number' => $validated['kvk_number'],
            'btw_number' => $validated['btw_number'],
            'primary_color' => $validated['primary_color'] ?? $company->primary_color,
            'logo' => $validated['logo'] ?? $company->logo,
            'settings' => $settings,
        ]);

        return redirect()->route('company-settings.index')
            ->with('success', 'Bedrijfsinstellingen succesvol bijgewerkt!');
    }
}
