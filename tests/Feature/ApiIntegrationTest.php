<?php

namespace Tests\Feature;

use App\Models\ApiIntegration;
use App\Models\Company;
use App\Repositories\ApiIntegrationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private ApiIntegrationRepository $repository;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new ApiIntegrationRepository();
        $this->company = Company::factory()->create();
    }

    /** @test */
    public function it_can_store_oauth_tokens()
    {
        $integration = $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'access_token_123',
            'refresh_token_456',
            now()->addHour(),
            ['scope' => 'read write']
        );

        $this->assertInstanceOf(ApiIntegration::class, $integration);
        $this->assertEquals($this->company->id, $integration->tenant_id);
        $this->assertEquals('marketplace', $integration->provider);
        $this->assertEquals('access_token_123', $integration->access_token);
        $this->assertEquals('refresh_token_456', $integration->refresh_token);
        $this->assertEquals(['scope' => 'read write'], $integration->extra);
    }

    /** @test */
    public function it_can_retrieve_valid_tokens()
    {
        // Store a valid token
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'valid_token',
            'refresh_token',
            now()->addHour()
        );

        $token = $this->repository->getValidToken($this->company->id, 'marketplace');

        $this->assertEquals('valid_token', $token);
    }

    /** @test */
    public function it_returns_null_for_expired_tokens()
    {
        // Store an expired token
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'expired_token',
            'refresh_token',
            now()->subHour()
        );

        $token = $this->repository->getValidToken($this->company->id, 'marketplace');

        $this->assertNull($token);
    }

    /** @test */
    public function it_can_update_existing_integration()
    {
        // Create initial integration
        $integration = $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'old_token',
            'old_refresh',
            now()->addHour()
        );

        // Update with new tokens
        $updated = $this->repository->updateToken(
            $this->company->id,
            'marketplace',
            'new_token',
            'new_refresh',
            now()->addDay()
        );

        $this->assertEquals($integration->id, $updated->id);
        $this->assertEquals('new_token', $updated->access_token);
        $this->assertEquals('new_refresh', $updated->refresh_token);
    }

    /** @test */
    public function it_can_check_if_integration_has_valid_token()
    {
        // No integration exists
        $this->assertFalse(
            $this->repository->hasValidIntegration($this->company->id, 'marketplace')
        );

        // Create valid integration
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'valid_token',
            'refresh_token',
            now()->addHour()
        );

        $this->assertTrue(
            $this->repository->hasValidIntegration($this->company->id, 'marketplace')
        );

        // Create expired integration
        $this->repository->updateToken(
            $this->company->id,
            'marketplace',
            'expired_token',
            'refresh_token',
            now()->subHour()
        );

        $this->assertFalse(
            $this->repository->hasValidIntegration($this->company->id, 'marketplace')
        );
    }

    /** @test */
    public function it_can_manage_extra_data()
    {
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'token',
            'refresh',
            now()->addHour(),
            ['initial' => 'data']
        );

        // Set extra data
        $this->repository->setExtra($this->company->id, 'marketplace', 'test_key', 'test_value');

        // Get extra data
        $value = $this->repository->getExtra($this->company->id, 'marketplace', 'test_key');
        $this->assertEquals('test_value', $value);

        // Get with default
        $default = $this->repository->getExtra($this->company->id, 'marketplace', 'nonexistent', 'default');
        $this->assertEquals('default', $default);
    }

    /** @test */
    public function it_isolates_data_by_tenant()
    {
        $company2 = Company::factory()->create();

        // Store tokens for both companies
        $this->repository->storeTokens(
            $this->company->id,
            'marketplace',
            'token_company1',
            'refresh1',
            now()->addHour()
        );

        $this->repository->storeTokens(
            $company2->id,
            'marketplace',
            'token_company2',
            'refresh2',
            now()->addHour()
        );

        // Verify isolation
        $token1 = $this->repository->getValidToken($this->company->id, 'marketplace');
        $token2 = $this->repository->getValidToken($company2->id, 'marketplace');

        $this->assertEquals('token_company1', $token1);
        $this->assertEquals('token_company2', $token2);
        $this->assertNotEquals($token1, $token2);
    }
}
