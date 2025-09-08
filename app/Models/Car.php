<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Repair;
use App\Models\Appointment;
use App\Models\CarStageTransition;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory, BelongsToCompany;

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
        'company_id',
    ];

    // Boot method to ensure status matches stage
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($car) {
            // Als stage_id is gezet, update status naar stage naam
            if ($car->stage_id && $car->isDirty('stage_id')) {
                $stage = CarStage::find($car->stage_id);
                if ($stage) {
                    $car->status = $stage->name;
                }
            }
        });
    }

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

    public function assignments()
    {
        return $this->hasMany(CarAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(CarAssignment::class)->where('status', 'active');
    }

    public function assignedEmployee()
    {
        return $this->hasOneThrough(Employee::class, CarAssignment::class, 'car_id', 'id', 'id', 'employee_id')
            ->where('car_assignments.status', 'active');
    }

    // Helper methods voor assignments
    public function isAssigned(): bool
    {
        return $this->currentAssignment()->exists();
    }

    public function assignTo(Employee $employee, string $notes = null, $estimatedCompletion = null): CarAssignment
    {
        // Controleer of auto al assigned is
        if ($this->isAssigned()) {
            throw new \Exception('Auto is al toegewezen aan een medewerker');
        }

        return CarAssignment::create([
            'car_id' => $this->id,
            'employee_id' => $employee->id,
            'assigned_at' => now(),
            'estimated_completion' => $estimatedCompletion,
            'notes' => $notes,
            'company_id' => $this->company_id,
        ]);
    }

    public function completeAssignment(string $notes = null): void
    {
        $currentAssignment = $this->currentAssignment;
        if ($currentAssignment) {
            $currentAssignment->complete($notes);
        }
    }

    protected $casts = [
        'sold_at' => 'datetime',
    ];
}
