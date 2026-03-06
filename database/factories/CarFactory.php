<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'brand' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Fiat']),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(0, 255),
            'plate' => strtoupper(fake()->unique()->bothify('???-####')),
            'mileage' => fake()->numberBetween(0, 200000),
        ];
    }
}
