<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToCompany;
use Illuminate\Support\Facades\Storage;

class CarImage extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'car_id',
        'company_id',
        'filename',
        'original_filename',
        'alt_text',
        'category',
        'sort_order',
        'is_primary',
        'file_size',
        'mime_type',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_primary' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected $appends = ['url', 'thumbnail_url'];

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
    public function getUrlAttribute(): string
    {
        return Storage::url('car-images/' . $this->filename);
    }

    public function getThumbnailUrlAttribute(): string
    {
        $pathInfo = pathinfo($this->filename);
        $thumbnailFilename = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        return Storage::url('car-images/thumbnails/' . $thumbnailFilename);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    // Helper methods
    public function makePrimary(): void
    {
        // Remove primary status from other images of the same car
        static::where('car_id', $this->car_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $this->update(['is_primary' => true]);
    }

    public function delete(): bool
    {
        // Delete the actual file
        Storage::delete('car-images/' . $this->filename);
        Storage::delete('car-images/thumbnails/' . pathinfo($this->filename, PATHINFO_FILENAME) . '_thumb.' . pathinfo($this->filename, PATHINFO_EXTENSION));

        return parent::delete();
    }
}
