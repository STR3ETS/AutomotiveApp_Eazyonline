<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repair extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'car_id',
        'description',
        'status',
        'cost_estimate',
        'company_id',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }
    
    public function checklist()
    {
        return $this->hasOne(Checklist::class);
    }

    // Handige accessor: totale onderdelenkosten
    public function getPartsTotalAttribute(): float
    {
        return (float) $this->parts()->sum('price');
    }
}