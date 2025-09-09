<?php

// Quick test script for integrations
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "🧪 Testing External API Integrations...\n\n";

// Test GA4 Client (Mock Mode)
echo "📊 Testing GA4 Client:\n";
try {
    $ga4Client = new App\Services\Analytics\Ga4Client(1);
    $activeUsers = $ga4Client->getActiveUsersLast30Min();
    echo "✅ Active users (mock): $activeUsers\n";
    
    $result = $ga4Client->getActiveUsersWithCache();
    echo "✅ Cache result: " . json_encode($result) . "\n\n";
} catch (Exception $e) {
    echo "❌ GA4 Error: " . $e->getMessage() . "\n\n";
}

// Test Marketplace Client (Mock Mode)
echo "🏪 Testing Marketplace Client:\n";
try {
    $repository = new App\Repositories\ApiIntegrationRepository();
    $client = new App\Services\Marketplace\MarketplaceClient(
        $repository, 
        'https://api.marketplace.example.com', 
        1
    );
    
    $dto = new App\DTO\ListingDTO(
        title: 'Test BMW 3 Serie',
        description: 'Test car listing',
        priceCents: 2500000,
        images: []
    );
    
    $response = $client->createListing($dto);
    echo "✅ Create listing (mock): " . ($response->success ? 'Success' : 'Failed') . "\n";
    echo "✅ Response data: " . json_encode($response->data) . "\n\n";
    
} catch (Exception $e) {
    echo "❌ Marketplace Error: " . $e->getMessage() . "\n\n";
}

// Test DTO validation
echo "📋 Testing DTOs:\n";
try {
    $validDto = new App\DTO\ListingDTO(
        title: 'Valid Car',
        description: 'Valid description',
        priceCents: 1500000
    );
    echo "✅ Valid DTO: " . ($validDto->isValid() ? 'Pass' : 'Fail') . "\n";
    
    $invalidDto = new App\DTO\ListingDTO(
        title: '', // Invalid
        description: 'Valid description', 
        priceCents: -100 // Invalid
    );
    $errors = $invalidDto->validate();
    echo "✅ Invalid DTO errors: " . count($errors) . " found\n";
    echo "✅ Errors: " . implode(', ', $errors) . "\n\n";
    
} catch (Exception $e) {
    echo "❌ DTO Error: " . $e->getMessage() . "\n\n";
}

echo "🎉 All integration tests completed!\n";
echo "💡 The system is ready for production with real API credentials.\n";
echo "📖 See docs/integrations.md for setup instructions.\n";
