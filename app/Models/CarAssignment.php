<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarAssignment extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'car_id',
        'employee_id',
        'assigned_at',
        'completed_at',
        'status',
        'notes',
        'estimated_completion',
        'company_id',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_completion' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function complete(string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'completed_at' => now(),
            'notes' => $reason ? $this->notes . ' | Geannuleerd: ' . $reason : $this->notes,
        ]);
    }

    public function getDurationInDays(): ?int
    {
        if (!$this->assigned_at) {
            return null;
        }

        $endDate = $this->completed_at ?? now();
        return $this->assigned_at->diffInDays($endDate);
    }

    public function isOverdue(): bool
    {
        if (!$this->estimated_completion || $this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        return now()->isAfter($this->estimated_completion);
    }
}
