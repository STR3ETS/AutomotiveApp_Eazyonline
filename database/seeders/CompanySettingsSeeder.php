<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $defaultSettings = [
                'opening_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '09:00', 'close' => '16:00'],
                    'sunday' => ['open' => '', 'close' => ''], // Closed
                ],
                'max_appointments_per_day' => 10,
                'default_appointment_duration' => 60,
                'repair_hourly_rate' => 75.00,
                'auto_progress_enabled' => false,
                'notification_emails' => [],
                'checklist_auto_complete' => false,
                'marketplace_auto_publish' => false,
                'default_warranty_months' => 6,
                'show_company_logo' => true,
                'currency' => 'EUR',
                'tax_rate' => 21.0,
                'invoice_prefix' => 'INV',
                'invoice_counter' => 1,
            ];

            // Add some realistic company data
            $companyData = [
                'piet' => [
                    'email' => 'info@autogarage-piet.nl',
                    'phone' => '+31 6 12345678',
                    'address' => 'Hoofdstraat 123, 1234 AB Autostad',
                    'website' => 'https://www.autogarage-piet.nl',
                    'kvk_number' => '12345678',
                    'btw_number' => 'NL123456789B01',
                ],
                'snelle' => [
                    'email' => 'contact@snellegarage.nl',
                    'phone' => '+31 20 9876543',
                    'address' => 'Snelweg 456, 5678 CD Speedtown',
                    'website' => 'https://www.snellegarage.nl',
                    'kvk_number' => '87654321',
                    'btw_number' => 'NL987654321B02',
                ],
                'premium' => [
                    'email' => 'service@premiummotors.nl',
                    'phone' => '+31 40 5555555',
                    'address' => 'Premium Plaza 789, 9012 EF Luxetown',
                    'website' => 'https://www.premiummotors.nl',
                    'kvk_number' => '11111111',
                    'btw_number' => 'NL111111111B03',
                ],
                'buurgarage' => [
                    'email' => 'hallo@buurgarage.nl',
                    'phone' => '+31 6 98765432',
                    'address' => 'Buurtstraat 321, 3456 GH Wijkdorp',
                    'website' => 'https://www.buurgarage.nl',
                    'kvk_number' => '22222222',
                    'btw_number' => 'NL222222222B04',
                ],
            ];

            $data = $companyData[$company->subdomain] ?? [
                'email' => 'info@' . $company->subdomain . '.nl',
                'phone' => '+31 6 12345678',
                'address' => 'Voorbeeldstraat 123, 1234 AB Voorbeeldstad',
                'kvk_number' => '12345678',
                'btw_number' => 'NL123456789B01',
            ];

            $company->update([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'website' => $data['website'] ?? null,
                'kvk_number' => $data['kvk_number'],
                'btw_number' => $data['btw_number'],
                'secondary_color' => '#6b7280',
                'settings' => $defaultSettings,
            ]);
        }

        echo "✅ Company settings initialized for " . $companies->count() . " companies!\n";
    }
}
