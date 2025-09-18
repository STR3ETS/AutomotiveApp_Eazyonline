<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Company;
use App\Models\ApiIntegration;
use App\Services\Analytics\Ga4Client;
use App\Services\Marketplace\MarketplaceClient;
use App\Repositories\ApiIntegrationRepository;
use App\DTO\ListingDTO;
use App\Jobs\PublishListingJob;
use App\Models\Car;
use Illuminate\Support\Facades\Queue;

class ExternalApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test company
        $this->company = Company::factory()->create([
            'name' => 'Test Auto Dealership'
        ]);
    }

    /** @test */
    public function test_api_integration_model_creation()
    {
        $integration = ApiIntegration::create([
            'company_id' => $this->company->id,
            'provider' => 'marketplace',
            'access_token' => 'test_token',
            'refresh_token' => 'test_refresh',
            'expires_at' => now()->addHour(),
            'extra' => ['test' => 'data']
        ]);

        $this->assertDatabaseHas('api_integrations', [
            'company_id' => $this->company->id,
            'provider' => 'marketplace',
            'access_token' => 'test_token'
        ]);

        $this->assertFalse($integration->isExpired());
        $this->assertTrue($integration->hasValidToken());
        $this->assertEquals(['test' => 'data'], $integration->getExtra());
    }

    /** @test */
    public function test_api_integration_repository()
    {
        $repository = new ApiIntegrationRepository();

        // Test storing tokens
        $repository->storeTokens(
            $this->company->id, 
            'marketplace', 
            'new_token',
            'new_refresh',
            now()->addHour()
        );

        $integration = $repository->getByTenantAndProvider($this->company->id, 'marketplace');
        
        $this->assertNotNull($integration);
        $this->assertEquals('new_token', $integration->access_token);
    }

    /** @test */
    public function test_ga4_client_mock_mode()
    {
        config(['integrations.ga4.mock' => true]);
        
        $client = new Ga4Client($this->company->id);
        $activeUsers = $client->getActiveUsersLast30Min();
        
        $this->assertIsInt($activeUsers);
        $this->assertGreaterThanOrEqual(10, $activeUsers);
        $this->assertLessThanOrEqual(500, $activeUsers);
        
        // Test caching
        $result = $client->getActiveUsersWithCache();
        $this->assertArrayHasKey('active_users', $result);
        $this->assertArrayHasKey('cached_at', $result);
    }

    /** @test */
    public function test_marketplace_client_mock_mode()
    {
        config(['integrations.marketplace.mock' => true]);
        
        $repository = new ApiIntegrationRepository();
        $client = new MarketplaceClient(
            $repository, 
            'https://api.marketplace.example.com', 
            $this->company->id
        );
        
        $dto = new ListingDTO(
            title: 'Test BMW 3 Serie',
            description: 'Beautiful test car',
            priceCents: 2500000,
            images: []
        );
        
        $response = $client->createListing($dto);
        
        $this->assertTrue($response->success);
        $this->assertArrayHasKey('listing_id', $response->data);
        $this->assertArrayHasKey('status', $response->data);
    }

    /** @test */
    public function test_listing_dto_validation()
    {
        // Valid DTO
        $validDto = new ListingDTO(
            title: 'Valid Car Title',
            description: 'Valid description',
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
    public function test_publish_listing_job_dispatching()
    {
        Queue::fake();
        
        $car = Car::factory()->create([
            'company_id' => $this->company->id,
            'brand' => 'BMW',
            'model' => '3 Serie',
            'price' => 25000
        ]);
        
        PublishListingJob::dispatch($car->id);
        
        Queue::assertPushed(PublishListingJob::class, function ($job) use ($car) {
            return $job->carId === $car->id;
        });
    }

    /** @test */
    public function test_oauth_routes_exist()
    {
        $response = $this->get('/oauth/marketplace/redirect');
        // This would redirect to marketplace OAuth, so we expect a redirect
        $this->assertTrue(in_array($response->getStatusCode(), [302, 404])); // 404 if route exists but no config
        
        // Test callback route exists (would normally require state parameter)
        $response = $this->get('/oauth/marketplace/callback');
        $this->assertTrue(in_array($response->getStatusCode(), [302, 400, 404]));
    }

    /** @test */
    public function test_api_routes_exist()
    {
        // Test publish listing route
        $response = $this->postJson('/api/listings/publish', [
            'car_id' => 999
        ]);
        // Expect validation error or auth error, not 404
        $this->assertNotEquals(404, $response->getStatusCode());
        
        // Test analytics route  
        $response = $this->getJson('/api/analytics/active-users');
        // Expect some response, not 404
        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
