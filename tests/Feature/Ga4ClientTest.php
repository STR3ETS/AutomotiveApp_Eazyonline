<?php

namespace Tests\Feature;

use App\Services\Analytics\Ga4Client;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Ga4ClientTest extends TestCase
{
    use RefreshDatabase;

    private Ga4Client $client;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->client = new Ga4Client($this->company->id);
    }

    /** @test */
    public function it_returns_mock_data_when_mock_enabled()
    {
        config(['services.ga4.mock' => true]);

        $activeUsers = $this->client->getActiveUsersLast30Min();

        $this->assertIsInt($activeUsers);
        $this->assertGreaterThanOrEqual(0, $activeUsers);
    }

    /** @test */
    public function it_falls_back_to_mock_when_no_credentials()
    {
        config([
            'services.ga4.mock' => false,
            'services.ga4.credentials_path' => '/nonexistent/path.json'
        ]);

        $activeUsers = $this->client->getActiveUsersLast30Min();

        $this->assertIsInt($activeUsers);
        $this->assertGreaterThanOrEqual(0, $activeUsers);
    }

    /** @test */
    public function it_stores_metrics_in_database()
    {
        $this->client->storeMetric('active_users_30min', 42, ['test' => true]);

        $this->assertDatabaseHas('analytics_metrics', [
            'tenant_id' => $this->company->id,
            'provider' => 'ga4',
            'metric_name' => 'active_users_30min',
            'metric_value' => 42,
        ]);
    }

    /** @test */
    public function it_retrieves_latest_metric()
    {
        // Store multiple metrics
        $this->client->storeMetric('active_users_30min', 10);
        sleep(1); // Ensure different timestamps
        $this->client->storeMetric('active_users_30min', 20);
        sleep(1);
        $this->client->storeMetric('active_users_30min', 30);

        $latest = $this->client->getLatestMetric('active_users_30min');

        $this->assertEquals(30, $latest);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_metric()
    {
        $result = $this->client->getLatestMetric('nonexistent_metric');

        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_caching_correctly()
    {
        // First call should fetch fresh data
        $result1 = $this->client->getActiveUsersWithCache();
        
        $this->assertIsArray($result1);
        $this->assertArrayHasKey('active_users', $result1);
        $this->assertArrayHasKey('is_cached', $result1);
        $this->assertArrayHasKey('is_mock', $result1);
        $this->assertArrayHasKey('last_updated', $result1);
        $this->assertFalse($result1['is_cached']); // First call is not cached

        // Second call within cache window should return cached data
        $result2 = $this->client->getActiveUsersWithCache();
        
        $this->assertTrue($result2['is_cached']);
        $this->assertEquals($result1['active_users'], $result2['active_users']);
    }

    /** @test */
    public function it_isolates_metrics_by_tenant()
    {
        $company2 = Company::factory()->create();
        $client2 = new Ga4Client($company2->id);

        // Store different metrics for each tenant
        $this->client->storeMetric('active_users_30min', 100);
        $client2->storeMetric('active_users_30min', 200);

        // Verify isolation
        $metric1 = $this->client->getLatestMetric('active_users_30min');
        $metric2 = $client2->getLatestMetric('active_users_30min');

        $this->assertEquals(100, $metric1);
        $this->assertEquals(200, $metric2);
    }

    /** @test */
    public function it_generates_realistic_mock_data_based_on_time()
    {
        config(['services.ga4.mock' => true]);

        // Test multiple calls to ensure variability but reasonable ranges
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->client->getActiveUsersLast30Min();
        }

        // All results should be non-negative integers
        foreach ($results as $result) {
            $this->assertIsInt($result);
            $this->assertGreaterThanOrEqual(0, $result);
            $this->assertLessThan(50, $result); // Reasonable upper bound for mock data
        }

        // Results should have some variation (not all the same)
        $uniqueResults = array_unique($results);
        $this->assertGreaterThan(1, count($uniqueResults), 'Mock data should have some variation');
    }

    /** @test */
    public function it_handles_database_errors_gracefully()
    {
        // Temporarily break the database connection
        DB::shouldReceive('table')
          ->andThrow(new \Exception('Database connection failed'));

        // Should not throw an exception
        $result = $this->client->getLatestMetric('active_users_30min');
        $this->assertNull($result);
    }

    /** @test */
    public function mock_response_includes_correct_headers()
    {
        config(['services.ga4.mock' => true]);

        $result = $this->client->getActiveUsersWithCache();

        $this->assertTrue($result['is_mock']);
    }
}
