<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuelling extends Model
{
    protected $fillable = [
        'car_id',
        'user_id',
        'liters',
        'cost',
        'station',
        'date',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
