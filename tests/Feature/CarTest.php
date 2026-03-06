<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_car(): void
    {
        $user = User::factory()->create();

        $car = Car::create([
            'user_id' => $user->id,
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 24,
            'plate' => 'ABC-1234',
            'mileage' => 50000,
        ]);

        $this->assertDatabaseHas('cars', [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'plate' => 'ABC-1234',
        ]);
    }

    public function test_can_update_car(): void
    {
        $car = Car::factory()->create(['mileage' => 10000]);

        $car->update(['mileage' => 20000]);

        $this->assertEquals(20000, $car->fresh()->mileage);
    }

    public function test_can_delete_car(): void
    {
        $car = Car::factory()->create();

        $carId = $car->id;
        $car->delete();

        $this->assertDatabaseMissing('cars', ['id' => $carId]);
    }

    public function test_car_has_many_maintenances(): void
    {
        $car = Car::factory()->create();
        Maintenance::factory()->count(3)->create(['car_id' => $car->id]);

        $this->assertCount(3, $car->maintenances);
        $this->assertInstanceOf(Maintenance::class, $car->maintenances->first());
    }

    public function test_car_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $car->user_id);
    }

    public function test_car_plate_is_unique(): void
    {
        Car::factory()->create(['plate' => 'XYZ-9999']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Car::factory()->create(['plate' => 'XYZ-9999']);
    }

    public function test_car_factory_creates_valid_record(): void
    {
        $car = Car::factory()->create();

        $this->assertNotNull($car->id);
        $this->assertNotNull($car->brand);
        $this->assertNotNull($car->model);
        $this->assertNotNull($car->plate);
    }
}
