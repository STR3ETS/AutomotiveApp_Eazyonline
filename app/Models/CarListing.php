<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToCompany;
use Carbon\Carbon;

class CarListing extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'car_id',
        'company_id',
        'listing_template_id',
        'platform',
        'external_id',
        'listing_url',
        'status',
        'generated_title',
        'generated_description',
        'used_images',
        'platform_response',
        'platform_config',
        'custom_hashtags',
        'published_at',
        'scheduled_publish_at',
        'expires_at',
        'error_message'
    ];

    protected $casts = [
        'used_images' => 'array',
        'platform_response' => 'array',
        'platform_config' => 'array',
        'published_at' => 'datetime',
        'scheduled_publish_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    // Relationships
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function listingTemplate(): BelongsTo
    {
        return $this->belongsTo(ListingTemplate::class)->withDefault();
    }

    // Status methods
    public function markAsPublished(string $externalId = null, string $listingUrl = null): void
    {
        $this->update([
            'status' => 'published',
            'external_id' => $externalId,
            'listing_url' => $listingUrl,
            'published_at' => now(),
            'error_message' => null
        ]);
    }

    public function markAsError(string $errorMessage): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $errorMessage
        ]);
    }

    public function markAsSold(): void
    {
        $this->update([
            'status' => 'sold'
        ]);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'pending']);
    }

    // Helper methods
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getDaysOnlineAttribute(): ?int
    {
        if (!$this->published_at) return null;
        
        return $this->published_at->diffInDays(now());
    }
}
