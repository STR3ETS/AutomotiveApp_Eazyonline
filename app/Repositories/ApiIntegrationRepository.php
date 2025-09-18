<?php

namespace App\Repositories;

use App\Models\ApiIntegration;
use Carbon\Carbon;

class ApiIntegrationRepository
{
    /**
     * Get integration by tenant and provider
     */
    public function getByTenantAndProvider(?int $tenantId, string $provider): ?ApiIntegration
    {
        return ApiIntegration::forTenantProvider($tenantId, $provider)->first();
    }

    /**
     * Create or update integration
     */
    public function createOrUpdate(?int $tenantId, string $provider, array $data): ApiIntegration
    {
        return ApiIntegration::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'provider' => $provider,
            ],
            $data
        );
    }

    /**
     * Store OAuth tokens for a tenant/provider
     */
    public function storeTokens(
        ?int $tenantId,
        string $provider,
        string $accessToken,
        ?string $refreshToken = null,
        ?Carbon $expiresAt = null,
        array $extra = []
    ): ApiIntegration {
        return $this->createOrUpdate($tenantId, $provider, [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt,
            'extra' => $extra,
        ]);
    }

    /**
     * Get valid access token for tenant/provider
     */
    public function getValidToken(?int $tenantId, string $provider): ?string
    {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        if (!$integration || !$integration->hasValidToken()) {
            return null;
        }

        return $integration->access_token;
    }

    /**
     * Update access token
     */
    public function updateToken(
        ?int $tenantId,
        string $provider,
        string $accessToken,
        ?string $refreshToken = null,
        ?Carbon $expiresAt = null
    ): ?ApiIntegration {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        if (!$integration) {
            return null;
        }

        $integration->updateTokens($accessToken, $refreshToken, $expiresAt);

        return $integration;
    }

    /**
     * Get refresh token
     */
    public function getRefreshToken(?int $tenantId, string $provider): ?string
    {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        return $integration?->refresh_token;
    }

    /**
     * Check if integration exists and has valid token
     */
    public function hasValidIntegration(?int $tenantId, string $provider): bool
    {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        return $integration && $integration->hasValidToken();
    }

    /**
     * Get all expiring tokens (for refreshing)
     */
    public function getExpiringTokens(): \Illuminate\Database\Eloquent\Collection
    {
        return ApiIntegration::whereNotNull('expires_at')
            ->whereNotNull('refresh_token')
            ->where('expires_at', '<=', now()->addMinutes(10))
            ->get();
    }

    /**
     * Delete integration
     */
    public function delete(?int $tenantId, string $provider): bool
    {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        if (!$integration) {
            return false;
        }

        return $integration->delete();
    }

    /**
     * Get extra data for integration
     */
    public function getExtra(?int $tenantId, string $provider, string $key, $default = null)
    {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        return $integration?->getExtra($key, $default) ?? $default;
    }

    /**
     * Set extra data for integration
     */
    public function setExtra(?int $tenantId, string $provider, string $key, $value): bool
    {
        $integration = $this->getByTenantAndProvider($tenantId, $provider);

        if (!$integration) {
            return false;
        }

        $integration->setExtra($key, $value);

        return true;
    }
}
