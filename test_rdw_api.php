<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\AutoController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test RDW API functie
$controller = new AutoController();

// Test met een paar verschillende kentekens
$testKentekens = ['68TFL9', '06HJNJ', '51XFJ8', 'NP-871-H'];

foreach ($testKentekens as $kenteken) {
    echo "\n=== Testing kenteken: $kenteken ===\n";
    $request = new Request(['kenteken' => $kenteken]);
    
    try {
        $response = $controller->getRdwData($request);
        echo "Response status: " . $response->getStatusCode() . "\n";
        echo "Response body: " . $response->getContent() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
