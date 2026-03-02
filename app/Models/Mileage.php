<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mileage extends Model
{
    protected $fillable = [
        'car_id',
        'mileage',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
