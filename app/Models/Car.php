<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
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

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function mileage(){
    return $this->hasMany(Mileage::class);
    }

    function fuellings(){
        return $this->hasMany(Fuelling::class);
    }
}
