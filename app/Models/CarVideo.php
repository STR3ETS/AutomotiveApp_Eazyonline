<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToCompany;

class CarVideo extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'car_id',
        'company_id',
        'video_url',
        'platform',
        'video_title',
        'video_description',
        'category',
        'duration',
        'thumbnail_url',
        'is_featured',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'duration' => 'integer'
    ];

    protected $appends = ['embed_url', 'platform_video_id'];

    // Relationships
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Accessors
    public function getEmbedUrlAttribute(): string
    {
        return match($this->platform) {
            'youtube' => $this->getYouTubeEmbedUrl(),
            'vimeo' => $this->getVimeoEmbedUrl(),
            'direct_url' => $this->video_url,
            default => $this->video_url
        };
    }

    public function getPlatformVideoIdAttribute(): ?string
    {
        return match($this->platform) {
            'youtube' => $this->extractYouTubeVideoId(),
            'vimeo' => $this->extractVimeoVideoId(),
            default => null
        };
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    // Helper methods
    public function makeFeatured(): void
    {
        // Remove featured status from other videos of the same car
        static::where('car_id', $this->car_id)
            ->where('id', '!=', $this->id)
            ->update(['is_featured' => false]);

        // Set this video as featured
        $this->update(['is_featured' => true]);
    }

    public function generateThumbnail(): void
    {
        $thumbnailUrl = match($this->platform) {
            'youtube' => $this->getYouTubeThumbnail(),
            'vimeo' => $this->getVimeoThumbnail(),
            default => null
        };

        if ($thumbnailUrl) {
            $this->update(['thumbnail_url' => $thumbnailUrl]);
        }
    }

    // Platform-specific methods
    private function getYouTubeEmbedUrl(): string
    {
        $videoId = $this->extractYouTubeVideoId();
        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : $this->video_url;
    }

    private function getVimeoEmbedUrl(): string
    {
        $videoId = $this->extractVimeoVideoId();
        return $videoId ? "https://player.vimeo.com/video/{$videoId}" : $this->video_url;
    }

    private function extractYouTubeVideoId(): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    private function extractVimeoVideoId(): ?string
    {
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getYouTubeThumbnail(): ?string
    {
        $videoId = $this->extractYouTubeVideoId();
        return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
    }

    private function getVimeoThumbnail(): ?string
    {
        $videoId = $this->extractVimeoVideoId();
        if (!$videoId) return null;

        // For Vimeo, we'd need to make an API call to get thumbnail
        // For now, return a placeholder or implement API call
        return "https://vumbnail.com/{$videoId}.jpg";
    }

    public function delete(): bool
    {
        // If this was the featured video, make another video featured
        if ($this->is_featured) {
            $nextVideo = static::where('car_id', $this->car_id)
                ->where('id', '!=', $this->id)
                ->orderBy('sort_order')
                ->first();

            if ($nextVideo) {
                $nextVideo->makeFeatured();
            }
        }

        return parent::delete();
    }
}
