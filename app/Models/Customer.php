<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToCompany;
    
    protected $fillable = ['name','email','phone','address','company_id'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}