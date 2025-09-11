<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Running ListingTemplateSeeder directly...\n";

$seeder = new \Database\Seeders\ListingTemplateSeeder();

// Create a mock command for output
$command = new class {
    public function info($message) {
        echo "[INFO] $message\n";
    }
    
    public function warn($message) {
        echo "[WARN] $message\n";
    }
    
    public function error($message) {
        echo "[ERROR] $message\n";
    }
};

// Set the command property using reflection
$reflection = new ReflectionClass($seeder);
$commandProperty = $reflection->getProperty('command');
$commandProperty->setAccessible(true);
$commandProperty->setValue($seeder, $command);

try {
    $seeder->run();
    echo "Seeder completed successfully!\n";
} catch (Exception $e) {
    echo "Seeder failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
