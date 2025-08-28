<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    protected $fillable = [
        'license_plate',
        'brand',
        'model',
        'year',
        'mileage',
        'price',
        'status',
        'stage_id',
    ];

    public function stage()
    {
        return $this->belongsTo(CarStage::class, 'stage_id');
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }
}
