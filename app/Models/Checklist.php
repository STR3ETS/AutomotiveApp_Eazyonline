<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = ['car_id', 'stage_id', 'task', 'is_completed', 'repair_id'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function stage()
    {
        return $this->belongsTo(CarStage::class, 'stage_id');
    }
    
    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}
