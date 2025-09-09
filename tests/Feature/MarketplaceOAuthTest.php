<?php

namespace Tests\Feature;

use App\Http\Controllers\MarketplaceAuthController;
use App\Models\ApiIntegration;
use App\Models\Company;
use App\Repositories\ApiIntegrationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class MarketplaceOAuthTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private ApiIntegrationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->repository = new ApiIntegrationRepository();
        
        // Set up test configuration
        config([
            'services.marketplace.client_id' => 'test_client_id',
            'services.marketplace.client_secret' => 'test_client_secret',
            'services.marketplace.redirect_uri' => 'http://localhost/oauth/marketplace/callback',
            'services.marketplace.base_url' => 'https://api.marketplace.example.com',
            'services.marketplace.auth_url' => '/oauth/authorize',
            'services.marketplace.token_url' => '/oauth/token',
        ]);
    }

    /** @test */
    public function it_redirects_to_marketplace_oauth_with_correct_parameters()
    {
        // Mock session and tenant helper
        session(['current_company_id' => $this->company->id]);
        
        $response = $this->get('/oauth/marketplace/redirect');

        $response->assertStatus(302);
        
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://api.marketplace.example.com/oauth/authorize', $location);
        
        // Check that URL contains required parameters
        $this->assertStringContainsString('client_id=test_client_id', $location);
        $this->assertStringContainsString('response_type=code', $location);
        $this->assertStringContainsString('state=', $location);
        
        // Verify state is stored in session
        $this->assertNotNull(session('oauth_state'));
    }

    /** @test */
    public function it_handles_oauth_callback_successfully()
    {
        // Set up session state
        $state = base64_encode(json_encode([
            'tenant_id' => $this->company->id,
            'csrf' => 'test_csrf',
            'timestamp' => now()->timestamp,
        ]));
        
        session(['oauth_state' => $state]);

        // Mock the token exchange
        Http::fake([
            'api.marketplace.example.com/oauth/token' => Http::response([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600,
                'scope' => 'read write',
                'token_type' => 'Bearer'
            ])
        ]);

        $response = $this->get('/oauth/marketplace/callback?' . http_build_query([
            'code' => 'auth_code_123',
            'state' => $state,
        ]));

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        // Verify tokens were stored
        $integration = $this->repository->getByTenantAndProvider($this->company->id, 'marketplace');
        $this->assertNotNull($integration);
        $this->assertEquals('new_access_token', $integration->access_token);
        $this->assertEquals('new_refresh_token', $integration->refresh_token);
        
        // Verify state was cleared
        $this->assertNull(session('oauth_state'));
    }

    /** @test */
    public function it_handles_oauth_error_response()
    {
        $response = $this->get('/oauth/marketplace/callback?' . http_build_query([
            'error' => 'access_denied',
            'error_description' => 'User denied access',
        ]));

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        
        // Verify no integration was created
        $integration = $this->repository->getByTenantAndProvider($this->company->id, 'marketplace');
        $this->assertNull($integration);
    }

    /** @test */
    public function it_validates_state_parameter()
    {
        $validState = base64_encode(json_encode([
            'tenant_id' => $this->company->id,
            'csrf' => 'test_csrf',
            'timestamp' => now()->timestamp,
        ]));
        
        $invalidState = 'invalid_state';
        
        session(['oauth_state' => $validState]);

        $response = $this->get('/oauth/marketplace/callback?' . http_build_query([
            'code' => 'auth_code_123',
            'state' => $invalidState,
        ]));

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        
        // Verify no integration was created
        $integration = $this->repository->getByTenantAndProvider($this->company->id, 'marketplace');
        $this->assertNull($integration);
    }

    /** @test */
    public function it_handles_token_exchange_failure()
    {
        $state = base64_encode(json_encode([
            'tenant_id' => $this->company->id,
            'csrf' => 'test_csrf',
            'timestamp' => now()->timestamp,
        ]));
        
        session(['oauth_state' => $state]);

        // Mock failed token exchange
        Http::fake([
            'api.marketplace.example.com/oauth/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid authorization code'
            ], 400)
        ]);

        $response = $this->get('/oauth/marketplace/callback?' . http_build_query([
            'code' => 'invalid_code',
            'state' => $state,
        ]));

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        
        // Verify no integration was created
        $integration = $this->repository->getByTenantAndProvider($this->company->id, 'marketplace');
        $this->assertNull($integration);
    }

    /** @test */
    public function it_can_refresh_tokens_automatically()
    {
        $controller = new MarketplaceAuthController($this->repository);

        // Store an expired token with refresh capability
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'expired_token',
            'valid_refresh_token',
            now()->subHour(),
            ['scope' => 'read write']
        );

        // Mock successful refresh
        Http::fake([
            'api.marketplace.example.com/oauth/token' => Http::response([
                'access_token' => 'refreshed_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600,
            ])
        ]);

        $result = $controller->refreshIfNeeded($this->company->id);

        $this->assertTrue($result);
        
        // Verify tokens were updated
        $integration = $this->repository->getByTenantAndProvider($this->company->id, 'marketplace');
        $this->assertEquals('refreshed_access_token', $integration->access_token);
        $this->assertEquals('new_refresh_token', $integration->refresh_token);
        $this->assertTrue($integration->expires_at->isFuture());
    }

    /** @test */
    public function it_handles_refresh_failure_gracefully()
    {
        $controller = new MarketplaceAuthController($this->repository);

        // Store an expired token
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'expired_token',
            'invalid_refresh_token',
            now()->subHour()
        );

        // Mock failed refresh
        Http::fake([
            'api.marketplace.example.com/oauth/token' => Http::response([
                'error' => 'invalid_grant'
            ], 400)
        ]);

        $result = $controller->refreshIfNeeded($this->company->id);

        $this->assertFalse($result);
        
        // Original token should remain unchanged
        $integration = $this->repository->getByTenantAndProvider($this->company->id, 'marketplace');
        $this->assertEquals('expired_token', $integration->access_token);
    }

    /** @test */
    public function it_requires_tenant_context_for_oauth()
    {
        // No tenant in session
        session()->forget('current_company_id');
        
        $response = $this->get('/oauth/marketplace/redirect');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }
}
