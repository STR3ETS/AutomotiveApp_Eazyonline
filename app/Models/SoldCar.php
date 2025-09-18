<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoldCar extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'license_plate',
        'brand',
        'model',
        'year',
        'mileage',
        'original_price',
        'purchase_price',
        'sale_price',
        'deposit_amount',
        'sold_at',
        'delivery_date',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'notes',
        'images',
        'original_car_id',
        'original_sale_id',
        'original_customer_id',
        'company_id',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'delivery_date' => 'datetime',
        'images' => 'array',
    ];

    /**
     * Get the profit made on this car
     */
    public function getProfitAttribute(): float
    {
        if (!$this->purchase_price) {
            return 0;
        }
        
        return $this->sale_price - $this->purchase_price;
    }

    /**
     * Get the profit margin percentage
     */
    public function getProfitMarginAttribute(): float
    {
        if (!$this->purchase_price || $this->purchase_price <= 0) {
            return 0;
        }
        
        return (($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    /**
     * Check if deposit was paid
     */
    public function hasDepositAttribute(): bool
    {
        return $this->deposit_amount && $this->deposit_amount > 0;
    }

    /**
     * Get remaining amount after deposit
     */
    public function getRemainingAmountAttribute(): float
    {
        if (!$this->deposit_amount) {
            return $this->sale_price;
        }
        
        return $this->sale_price - $this->deposit_amount;
    }

    /**
     * Get days in inventory (from creation to sale)
     */
    public function getDaysInInventoryAttribute(): ?int
    {
        // This would need to be calculated based on original car creation date
        // For now, we'll return null since we don't have that data in this table
        return null;
    }

    /**
     * Scope for cars sold this month
     */
    public function scopeSoldThisMonth($query)
    {
        return $query->whereMonth('sold_at', now()->month)
                    ->whereYear('sold_at', now()->year);
    }

    /**
     * Scope for cars sold this year
     */
    public function scopeSoldThisYear($query)
    {
        return $query->whereYear('sold_at', now()->year);
    }

    /**
     * Scope for cars sold between dates
     */
    public function scopeSoldBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('sold_at', [$startDate, $endDate]);
    }

    /**
     * Get primary image URL if available
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        if (!$this->images || empty($this->images)) {
            return null;
        }
        
        // Return first image as primary
        return $this->images[0] ?? null;
    }
}
