<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryChecklistItem extends Model
{
    use BelongsToCompany;
    
    protected $fillable = ['sale_id','task','is_completed','company_id'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}