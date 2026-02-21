<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'car_id',
        'description',
        'cost',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
