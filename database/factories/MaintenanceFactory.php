<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Maintenance;
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
