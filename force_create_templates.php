<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Company;
use App\Models\ListingTemplate;
use Illuminate\Support\Facades\DB;

echo "Creating templates for all companies...\n";

$companies = Company::all();
echo "Found {$companies->count()} companies\n";

foreach ($companies as $company) {
    echo "Creating templates for company: {$company->name} (ID: {$company->id})\n";
    
    $templates = [
        [
            'company_id' => $company->id,
            'name' => 'Marktplaats Standaard',
            'platform' => 'marktplaats',
            'title_template' => '{brand} {model} {year} - {mileage}km - €{price}',
            'description_template' => "🚗 {brand} {model} {year}\n\n📊 SPECIFICATIES:\n• Kilometerstand: {mileage} km\n• Bouwjaar: {year}\n• Kenteken: {license_plate}\n\n💰 PRIJS: €{price}\n\n📞 CONTACT:\n{company_name}\nTelefoon: {company_phone}\nE-mail: {company_email}\n\n✅ Bezichtiging en proefrit mogelijk\n✅ Inruil bespreekbaar\n✅ Financiering mogelijk\n\n📍 Bezoek onze showroom voor meer informatie!",
            'image_settings' => json_encode(['max_images' => 10]),
            'branding_settings' => json_encode(['logo_overlay' => true]),
            'platform_specific' => json_encode(['category' => 'auto']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'company_id' => $company->id,
            'name' => 'Instagram Standaard',
            'platform' => 'instagram',
            'title_template' => '{brand} {model} {year} 🚗',
            'description_template' => "🚗 {brand} {model} {year}\n📊 {mileage}km | €{price}\n\n#auto #car #verkoop #tweedehands #{brand} #{model} #autoverkoop #automotive #cars #carsforsale #nederland #autohandel",
            'image_settings' => json_encode(['max_images' => 10, 'square_format' => true]),
            'branding_settings' => json_encode(['logo_overlay' => true, 'instagram_style' => true]),
            'platform_specific' => json_encode(['use_hashtags' => true, 'story_format' => true]),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'company_id' => $company->id,
            'name' => 'Facebook Marketplace',
            'platform' => 'facebook',
            'title_template' => '{brand} {model} {year} - {mileage}km',
            'description_template' => "🚗 {brand} {model} {year}\n\nKilometerstand: {mileage} km\nPrijs: €{price}\n\n{company_name}\n📞 {company_phone}\n📧 {company_email}\n\nBezichtiging mogelijk na afspraak!",
            'image_settings' => json_encode(['max_images' => 20]),
            'branding_settings' => json_encode(['minimal_branding' => true]),
            'platform_specific' => json_encode(['category' => 'vehicles']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];
    
    foreach ($templates as $template) {
        // Check if template already exists
        $exists = DB::table('listing_templates')
            ->where('company_id', $template['company_id'])
            ->where('platform', $template['platform'])
            ->where('name', $template['name'])
            ->exists();
            
        if (!$exists) {
            $id = DB::table('listing_templates')->insertGetId($template);
            echo "  ✅ Created {$template['platform']} template (ID: {$id})\n";
        } else {
            echo "  ⚠️ {$template['platform']} template already exists\n";
        }
    }
}

$totalTemplates = DB::table('listing_templates')->count();
echo "\n🎉 Total templates in database: {$totalTemplates}\n";
