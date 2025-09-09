<?php

namespace Tests\Feature;

use App\Services\Marketplace\MarketplaceClient;
use App\Repositories\ApiIntegrationRepository;
use App\DTO\ListingDTO;
use App\DTO\ResponseDTO;
use App\Exceptions\MarketplaceAuthException;
use App\Exceptions\MarketplaceRateLimitException;
use App\Models\ApiIntegration;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketplaceClientTest extends TestCase
{
    use RefreshDatabase;

    private MarketplaceClient $client;
    private ApiIntegrationRepository $repository;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->repository = new ApiIntegrationRepository();
        $this->client = new MarketplaceClient(
            $this->repository,
            'https://api.marketplace.example.com',
            $this->company->id
        );
    }

    /** @test */
    public function it_uses_mock_mode_when_configured()
    {
        config(['services.marketplace.mock' => true]);

        $dto = new ListingDTO(
            title: 'Test Car',
            description: 'Test Description',
            priceCents: 2500000,
            images: []
        );

        $response = $this->client->createListing($dto);

        $this->assertTrue($response->success);
        $this->assertStringStartsWith('mock_listing_', $response->getData('id'));
        $this->assertEquals('Test Car', $response->getData('title'));
    }

    /** @test */
    public function it_handles_authentication_errors()
    {
        config(['services.marketplace.mock' => false]);

        Http::fake([
            'api.marketplace.example.com/*' => Http::response(['error' => 'Unauthorized'], 401)
        ]);

        $dto = new ListingDTO(
            title: 'Test Car',
            description: 'Test Description',
            priceCents: 2500000
        );

        $this->expectException(MarketplaceAuthException::class);
        $this->client->createListing($dto);
    }

    /** @test */
    public function it_handles_rate_limiting()
    {
        config(['services.marketplace.mock' => false]);

        Http::fake([
            'api.marketplace.example.com/*' => Http::response(
                ['error' => 'Rate limit exceeded'],
                429,
                ['Retry-After' => '60']
            )
        ]);

        // Store valid token
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'valid_token',
            'refresh_token',
            now()->addHour()
        );

        $dto = new ListingDTO(
            title: 'Test Car',
            description: 'Test Description',
            priceCents: 2500000
        );

        try {
            $this->client->createListing($dto);
            $this->fail('Expected MarketplaceRateLimitException');
        } catch (MarketplaceRateLimitException $e) {
            $this->assertEquals(60, $e->retryAfter);
        }
    }

    /** @test */
    public function it_refreshes_expired_tokens()
    {
        config(['services.marketplace.mock' => false]);

        // Store expired token with refresh token
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'expired_token',
            'refresh_token_123',
            now()->subHour()
        );

        // Mock token refresh
        Http::fake([
            'api.marketplace.example.com/oauth/token' => Http::response([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600
            ]),
            'api.marketplace.example.com/v1/listings' => Http::response([
                'id' => 'listing_123',
                'title' => 'Test Car',
                'status' => 'draft'
            ])
        ]);

        $dto = new ListingDTO(
            title: 'Test Car',
            description: 'Test Description',
            priceCents: 2500000
        );

        $response = $this->client->createListing($dto);

        $this->assertTrue($response->success);
        
        // Verify token was refreshed
        $newToken = $this->repository->getValidToken($this->company->id, 'marketplace');
        $this->assertEquals('new_access_token', $newToken);
    }

    /** @test */
    public function it_handles_successful_listing_creation()
    {
        config(['services.marketplace.mock' => false]);

        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'valid_token',
            'refresh_token',
            now()->addHour()
        );

        Http::fake([
            'api.marketplace.example.com/v1/listings' => Http::response([
                'id' => 'listing_123',
                'title' => 'Test Car',
                'status' => 'draft',
                'created_at' => now()->toISOString()
            ])
        ]);

        $dto = new ListingDTO(
            title: 'Test Car',
            description: 'Test Description',
            priceCents: 2500000
        );

        $response = $this->client->createListing($dto);

        $this->assertTrue($response->success);
        $this->assertEquals('listing_123', $response->getData('id'));
        $this->assertEquals('Test Car', $response->getData('title'));
    }

    /** @test */
    public function it_handles_image_upload()
    {
        config(['services.marketplace.mock' => true]);

        $response = $this->client->uploadImage('listing_123', '/fake/path/image.jpg');

        $this->assertTrue($response->success);
        $this->assertStringStartsWith('mock_image_', $response->getData('image_id'));
        $this->assertEquals('listing_123', $response->getData('listing_id'));
    }

    /** @test */
    public function it_handles_publishing()
    {
        config(['services.marketplace.mock' => true]);

        $response = $this->client->publish('listing_123');

        $this->assertTrue($response->success);
        $this->assertEquals('listing_123', $response->getData('id'));
        $this->assertEquals('published', $response->getData('status'));
        $this->assertStringStartsWith('https://marketplace.example.com', $response->getData('listing_url'));
    }

    /** @test */
    public function it_handles_unpublishing()
    {
        config(['services.marketplace.mock' => true]);

        $response = $this->client->unpublish('listing_123');

        $this->assertTrue($response->success);
        $this->assertEquals('listing_123', $response->getData('id'));
        $this->assertEquals('unpublished', $response->getData('status'));
    }

    /** @test */
    public function it_validates_listing_dto()
    {
        $invalidDto = new ListingDTO(
            title: '', // Invalid: empty title
            description: 'Test Description',
            priceCents: -100 // Invalid: negative price
        );

        $this->expectException(\App\Exceptions\MarketplaceValidationException::class);
        $this->client->createListing($invalidDto);
    }

    /** @test */
    public function response_dto_handles_errors_correctly()
    {
        $successResponse = ResponseDTO::success(['id' => 123]);
        $this->assertTrue($successResponse->success);
        $this->assertEquals(123, $successResponse->getData('id'));

        $errorResponse = ResponseDTO::error('Something went wrong');
        $this->assertFalse($errorResponse->success);
        $this->assertEquals('Something went wrong', $errorResponse->error);

        $this->expectException(\RuntimeException::class);
        $errorResponse->getDataOrFail();
    }
}
