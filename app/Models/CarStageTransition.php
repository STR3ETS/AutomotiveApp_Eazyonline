<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarStageTransition extends Model
{
    protected $fillable = [
        'car_id', 'from_stage_id', 'to_stage_id', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(CarStage::class, 'from_stage_id');
    }

    public function toStage(): BelongsTo
    {
        return $this->belongsTo(CarStage::class, 'to_stage_id');
    }
}