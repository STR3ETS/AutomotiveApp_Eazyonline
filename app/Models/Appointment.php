<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id', 'customer_id', 'customer_name', 'type', 'date', 'time', 'notes'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    // Helper method om klant naam te krijgen (van customer of customer_name veld)
    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->name : $this->attributes['customer_name'];
    }
}