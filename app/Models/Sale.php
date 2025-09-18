<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'car_id',
        'customer_id',
        'sale_price',
        'deposit_amount',
        'payment_status',
        'status',
        'contract_signed_at',
        'delivery_date',
        'delivery_time',
        'company_id',
        'notes',
        'sold_at',
    ];

    protected $casts = [
        'contract_signed_at' => 'datetime',
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i',
        'sold_at' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(DeliveryChecklistItem::class);
    }
}