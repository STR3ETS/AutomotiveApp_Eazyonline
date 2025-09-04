<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Repair;
use App\Models\Appointment;
use App\Models\CarStageTransition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function currentStageChecklists()
    {
        return $this->hasMany(Checklist::class)->where('stage_id', $this->stage_id);
    }

    // Get completion percentage for current stage
    public function getStageCompletionAttribute()
    {
        $checklists = $this->checklists()->where('stage_id', $this->stage_id)->get();
        if ($checklists->count() === 0) {
            return 0;
        }
        
        $completed = $checklists->where('is_completed', true)->count();
        return round(($completed / $checklists->count()) * 100);
    }

    // Check if car can move to next stage
    public function canMoveToNextStage()
    {
        $checklists = $this->checklists()->where('stage_id', $this->stage_id)->get();
        if ($checklists->count() === 0) {
            return true; // No checklist means can move
        }
        
        $completed = $checklists->where('is_completed', true)->count();
        return $completed === $checklists->count();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    protected $casts = [
        'sold_at' => 'datetime',
    ];
}
