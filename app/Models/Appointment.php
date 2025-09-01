<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id', 'customer_name', 'type', 'date', 'time', 'notes'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}