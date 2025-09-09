<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ApiIntegration extends Model
{
    protected $fillable = [
        'tenant_id',
        'provider',
        'access_token',
        'refresh_token',
        'expires_at',
        'extra',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'extra' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the tenant/company this integration belongs to
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }

    /**
     * Check if the access token is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if the access token will expire soon (within 5 minutes)
     */
    public function expiringSoon(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Check if we have a valid access token
     */
    public function hasValidToken(): bool
    {
        return !empty($this->access_token) && !$this->isExpired();
    }

    /**
     * Check if we can refresh the token
     */
    public function canRefresh(): bool
    {
        return !empty($this->refresh_token);
    }

    /**
     * Update the access token
     */
    public function updateTokens(string $accessToken, ?string $refreshToken = null, ?Carbon $expiresAt = null): void
    {
        $this->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken ?? $this->refresh_token,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Get extra field value with default
     */
    public function getExtra(string $key, $default = null)
    {
        return data_get($this->extra, $key, $default);
    }

    /**
     * Set extra field value
     */
    public function setExtra(string $key, $value): void
    {
        $extra = $this->extra ?? [];
        data_set($extra, $key, $value);
        $this->update(['extra' => $extra]);
    }

    /**
     * Scope to filter by tenant and provider
     */
    public function scopeForTenantProvider($query, ?int $tenantId, string $provider)
    {
        return $query->where('tenant_id', $tenantId)
                    ->where('provider', $provider);
    }
}
