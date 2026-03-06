<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mileages(): HasMany
    {
        return $this->hasMany(Mileage::class);
    }
}
