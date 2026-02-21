<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'plate',
        'mileage',
    ];

    function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
