<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fuelling extends Model
{
    use HasFactory;
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
    public function mileages()
    {
        return $this->hasMany(Mileage::class);
    }   
}
