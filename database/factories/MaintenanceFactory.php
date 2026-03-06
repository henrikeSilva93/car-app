<?php

namespace Database\Factories;

use App\Models\Maintenance;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'description' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 50, 5000),
        ];
    }
}
