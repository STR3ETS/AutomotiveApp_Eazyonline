<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auto extends Model
{
    use HasFactory;

    protected $table = 'autos';

    protected $fillable = [
        'merk',
        'model',
        'bouwjaar',
        'kilometerstand',
        'kenteken',
        'prijs',
        'status',
        'kleur',
        'transmissie',
        'brandstof',
    ];
}
