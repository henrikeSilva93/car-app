<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mileage extends Model
{
    use HasFactory;
    protected $fillable = [
        'car_id',
        'fuelling_id',
        'mileage',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    public function fuelling()
    {
        return $this->belongsTo(Fuelling::class);
    }
}
