<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mileage extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'fuelling_id',
        'mileage',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function fuelling(): BelongsTo
    {
        return $this->belongsTo(Fuelling::class);
    }
}
