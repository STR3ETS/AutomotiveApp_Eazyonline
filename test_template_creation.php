<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Company;
use App\Models\ListingTemplate;

echo "Testing template creation...\n";

// Get companies
$companies = Company::all();
echo "Found " . $companies->count() . " companies\n";

if ($companies->isEmpty()) {
    echo "No companies found!\n";
    exit;
}

$company = $companies->first();
echo "Using company: " . $company->name . " (ID: " . $company->id . ")\n";

try {
    // Try to create a simple template
    $template = new ListingTemplate();
    $template->company_id = $company->id;
    $template->name = 'Test Template';
    $template->platform = 'marktplaats';
    $template->title_template = '{brand} {model}';
    $template->description_template = 'Test description';
    $template->image_settings = json_encode(['max_images' => 10]);
    $template->branding_settings = json_encode(['logo_overlay' => true]);
    $template->platform_specific = json_encode(['category' => 'auto']);
    
    $result = $template->save();
    
    echo "Template saved: " . ($result ? 'YES' : 'NO') . "\n";
    echo "Template ID: " . $template->id . "\n";
    
    // Check total count
    $count = ListingTemplate::count();
    echo "Total templates in database: " . $count . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "Test completed.\n";
