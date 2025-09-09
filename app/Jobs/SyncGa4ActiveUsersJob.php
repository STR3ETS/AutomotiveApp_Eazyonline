<?php

namespace App\Jobs;

use App\Services\Analytics\Ga4Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGa4ActiveUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60; // 1 minute

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $tenantId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting GA4 active users sync job', [
            'tenant_id' => $this->tenantId,
            'attempt' => $this->attempts(),
        ]);

        try {
            $ga4Client = new Ga4Client($this->tenantId);
            
            // Get active users from GA4
            $activeUsers = $ga4Client->getActiveUsersLast30Min();
            
            // Store in analytics metrics table
            $ga4Client->storeMetric('active_users_30min', $activeUsers, [
                'sync_type' => 'scheduled',
                'job_id' => $this->job->uuid ?? null,
            ]);

            Log::info('GA4 active users sync completed', [
                'tenant_id' => $this->tenantId,
                'active_users' => $activeUsers,
                'attempt' => $this->attempts(),
            ]);

        } catch (\Exception $e) {
            Log::error('GA4 active users sync failed', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GA4 active users sync job permanently failed', [
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }

    /**
     * Calculate retry delay
     */
    public function backoff(): array
    {
        return [10, 30, 60]; // 10s, 30s, 1m
    }
}
