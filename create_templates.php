<?php

require_once 'vendor/autoload.php';

use App\Models\ListingTemplate;
use App\Models\Company;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Maak templates aan voor alle companies
$companies = Company::all();

foreach ($companies as $company) {
    // Marktplaats Template
    ListingTemplate::firstOrCreate(
        [
            'company_id' => $company->id,
            'platform' => 'marktplaats',
            'name' => 'Marktplaats Standaard'
        ],
        [
            'title_template' => '{brand} {model} {year} - {mileage}km - €{price}',
            'description_template' => "🚗 {brand} {model} {year}

📊 SPECIFICATIES:
• Kilometerstand: {mileage} km
• Bouwjaar: {year}
• Kenteken: {license_plate}

💰 PRIJS: €{price}

📞 CONTACT:
{company_name}

✅ Bezichtiging en proefrit mogelijk
✅ Inruil bespreekbaar
✅ Financiering mogelijk"
        ]
    );

    // Instagram Template
    ListingTemplate::firstOrCreate(
        [
            'company_id' => $company->id,
            'platform' => 'instagram',
            'name' => 'Instagram Standaard'
        ],
        [
            'title_template' => '{brand} {model} {year} 🚗',
            'description_template' => "🚗 {brand} {model} {year}
📊 {mileage}km | €{price}

#auto #car #verkoop #tweedehands #{brand} #{model} #autoverkoop #automotive #cars #carsforsale #nederland #autohandel"
        ]
    );

    // Facebook Template
    ListingTemplate::firstOrCreate(
        [
            'company_id' => $company->id,
            'platform' => 'facebook',
            'name' => 'Facebook Marketplace'
        ],
        [
            'title_template' => '{brand} {model} {year} - {mileage}km',
            'description_template' => "🚗 {brand} {model} {year}

Kilometerstand: {mileage} km
Prijs: €{price}

{company_name}

Bezichtiging mogelijk na afspraak!"
        ]
    );
}

echo "Created templates for " . $companies->count() . " companies\n";
echo "Total templates: " . ListingTemplate::count() . "\n";
