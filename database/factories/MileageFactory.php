<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Fuelling;
use App\Models\Mileage;
use Illuminate\Database\Eloquent\Factories\Factory;

class MileageFactory extends Factory
{
    protected $model = Mileage::class;

    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'fuelling_id' => Fuelling::factory(),
            'mileage' => fake()->numberBetween(1000, 200000),
        ];
    }
}
