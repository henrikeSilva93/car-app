<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Fuelling;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuellingFactory extends Factory
{
    protected $model = Fuelling::class;

    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'user_id' => User::factory(),
            'liters' => fake()->randomFloat(2, 10, 60),
            'cost' => fake()->randomFloat(2, 50, 400),
            'station' => fake()->company(),
            'date' => fake()->date(),
        ];
    }
}
