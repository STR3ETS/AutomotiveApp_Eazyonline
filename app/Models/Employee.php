<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'specializations',
        'active',
        'company_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'specializations' => 'array',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function carAssignments(): HasMany
    {
        return $this->hasMany(CarAssignment::class);
    }

    public function currentAssignments(): HasMany
    {
        return $this->hasMany(CarAssignment::class)->where('status', 'active');
    }

    // Helper methods
    public function getActiveAssignmentsCount(): int
    {
        return $this->currentAssignments()->count();
    }

    public function canTakeNewAssignment(): bool
    {
        // Een medewerker kan maximaal 3 auto's tegelijk hebben
        return $this->getActiveAssignmentsCount() < 3;
    }

    public function getSpecializationsText(): string
    {
        if (empty($this->specializations)) {
            return 'Algemeen';
        }
        
        return implode(', ', $this->specializations);
    }
}
