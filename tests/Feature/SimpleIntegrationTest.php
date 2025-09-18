<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\DTO\ListingDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_listing_dto_works()
    {
        // Valid DTO
        $validDto = new ListingDTO(
            title: 'Valid Car Title',
            description: 'Valid description with enough characters',
            priceCents: 1500000
        );
        
        $this->assertTrue($validDto->isValid());
        $this->assertEmpty($validDto->validate());
        
        // Invalid DTO
        $invalidDto = new ListingDTO(
            title: '', // Too short
            description: 'Valid description',
            priceCents: -100 // Negative price
        );
        
        $this->assertFalse($invalidDto->isValid());
        $errors = $invalidDto->validate();
        $this->assertContains('Title is required', $errors);
        $this->assertContains('Price must be greater than 0', $errors);
    }

    /** @test */
    public function test_integration_configuration_exists()
    {
        // Test that integration config file exists and loads
        $this->assertTrue(file_exists(config_path('integrations.php')));
        
        // Load the config directly
        $config = include config_path('integrations.php');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('marketplace', $config);
        $this->assertArrayHasKey('ga4', $config);
        
        // Test mock mode is configured
        $this->assertTrue($config['marketplace']['mock']);
        $this->assertTrue($config['ga4']['mock']);
    }

    /** @test */
    public function test_oauth_routes_exist()
    {
        $response = $this->get('/oauth/marketplace/redirect');
        // Should not be 404 (route not found), might be redirect or error
        $this->assertNotEquals(404, $response->getStatusCode());
        
        $response = $this->get('/oauth/marketplace/callback');
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function test_api_routes_exist()
    {
        // Test routes with tenant prefix (as shown in route:list)
        $response = $this->postJson('/tenants/1/listings/1/publish', []);
        $this->assertNotEquals(404, $response->getStatusCode());
        
        $response = $this->getJson('/tenants/1/listings/1/status');
        $this->assertNotEquals(404, $response->getStatusCode());
        
        $response = $this->getJson('/tenants/1/analytics/active-users');
        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
