<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToCompany;

class ListingTemplate extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'platform',
        'title_template',
        'description_template',
        'image_settings',
        'branding_settings',
        'platform_specific',
        'is_active'
    ];

    protected $casts = [
        'image_settings' => 'array',
        'branding_settings' => 'array',
        'platform_specific' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Helper methods
    public function generateTitle(Car $car): string
    {
        return $this->replacePlaceholders($this->title_template, $car);
    }

    public function generateDescription(Car $car): string
    {
        return $this->replacePlaceholders($this->description_template, $car);
    }

    private function replacePlaceholders(string $template, Car $car): string
    {
        $placeholders = [
            '{brand}' => $car->brand,
            '{model}' => $car->model,
            '{year}' => $car->year,
            '{mileage}' => number_format($car->mileage, 0, ',', '.'),
            '{price}' => number_format($car->price, 0, ',', '.'),
            '{license_plate}' => $car->license_plate,
            '{company_name}' => $car->company->name ?? '',
            '{company_phone}' => $car->company->phone ?? '',
            '{company_email}' => $car->company->email ?? '',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    // Scopes
    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
