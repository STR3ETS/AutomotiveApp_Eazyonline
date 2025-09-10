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
        'user_id',
        'role',
        'last_login_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'specializations' => 'array',
        'last_login_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function getActiveAssignmentsCount(): int
    {
        return $this->currentAssignments()->count();
    }

    public function canTakeNewAssignment(): bool
    {
        // Een medewerker kan maximaal 3 auto's tegelijk hebben
        return $this->active && $this->getActiveAssignmentsCount() < 3;
    }

    // Position-based permission methods
    public function isOwner(): bool
    {
        return $this->position === 'eigenaar';
    }

    public function isMedewerker(): bool
    {
        return $this->position === 'medewerker';
    }

    public function canManageCompany(): bool
    {
        return $this->isOwner();
    }

    public function canManageEmployees(): bool
    {
        return $this->isOwner();
    }

    public function canViewReports(): bool
    {
        return $this->isOwner();
    }

    public function canManageCars(): bool
    {
        return $this->isOwner();
    }

    public function getSpecializationsText(): string
    {
        if (empty($this->specializations)) {
            return 'Algemeen';
        }

        return implode(', ', $this->specializations);
    }
}
