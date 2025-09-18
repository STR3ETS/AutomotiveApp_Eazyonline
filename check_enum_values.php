<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $columns = DB::select("SHOW COLUMNS FROM car_videos WHERE Field = 'category'");
    if (!empty($columns)) {
        echo "Category column details:\n";
        print_r($columns[0]);
    } else {
        echo "Category column not found\n";
    }
    
    $platformColumns = DB::select("SHOW COLUMNS FROM car_videos WHERE Field = 'platform'");
    if (!empty($platformColumns)) {
        echo "\nPlatform column details:\n";
        print_r($platformColumns[0]);
    } else {
        echo "Platform column not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
