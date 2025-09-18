<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',           // optioneel, fallback
        'active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login_at'     => 'datetime',
            'active'            => 'boolean',
        ];
    }

    // ─── Relations ─────────────────────────────────────────────
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    // ─── Role helpers ─────────────────────────────────────────
    public function isEigenaar(): bool
    {
        return $this->employee?->position === 'eigenaar';
    }

    public function isMedewerker(): bool
    {
        return $this->employee?->position === 'medewerker';
    }

    public function getDisplayRoleAttribute(): string
    {
        // Altijd position als hoofdbron, anders fallback naar users.role
        return $this->employee?->position ?? $this->role ?? 'medewerker';
    }

    public function hasRole(string $role): bool
    {
        return $this->getDisplayRoleAttribute() === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->getDisplayRoleAttribute(), $roles, true);
    }

    // ─── Permissions (gebaseerd op role/position) ──────────────
    public function canManageCompany(): bool
    {
        return $this->isEigenaar();
    }

    public function canManageEmployees(): bool
    {
        return $this->isEigenaar();
    }

    public function canManageCars(): bool
    {
        return $this->isEigenaar();
    }

    public function canViewReports(): bool
    {
        return $this->isEigenaar();
    }

    public function canAssignCars(): bool
    {
        return $this->isEigenaar();
    }

    public function canOnlyViewOwnWork(): bool
    {
        return $this->isMedewerker();
    }
}
