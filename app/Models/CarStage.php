<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarStage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order'];

    public function cars()
    {
        return $this->hasMany(Car::class, 'stage_id');
    }
}
