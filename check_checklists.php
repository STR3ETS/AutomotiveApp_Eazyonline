<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Checklist;
use App\Models\Company;

echo "Checklist count per company:\n";
echo "============================\n";

$companies = Company::all();

foreach ($companies as $company) {
    $count = Checklist::whereHas('car', function($q) use ($company) {
        $q->where('company_id', $company->id);
    })->count();
    
    echo "{$company->name}: {$count} checklists\n";
}

echo "\nDone!\n";
