<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Fuelling;
use App\Models\Mileage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FuellingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_fuelling(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);

        $fuelling = Fuelling::create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'liters' => 45.5,
            'cost' => 280.00,
            'station' => 'Posto Shell',
            'date' => '2026-03-01',
        ]);

        $this->assertDatabaseHas('fuellings', [
            'car_id' => $car->id,
            'liters' => 45.5,
            'station' => 'Posto Shell',
        ]);
    }

    public function test_can_update_fuelling(): void
    {
        $fuelling = Fuelling::factory()->create(['cost' => 100.00]);

        $fuelling->update(['cost' => 250.00]);

        $this->assertEquals(250.00, (float) $fuelling->fresh()->cost);
    }

    public function test_can_delete_fuelling(): void
    {
        $fuelling = Fuelling::factory()->create();

        $id = $fuelling->id;
        $fuelling->delete();

        $this->assertDatabaseMissing('fuellings', ['id' => $id]);
    }

    public function test_fuelling_belongs_to_car(): void
    {
        $car = Car::factory()->create();
        $fuelling = Fuelling::factory()->create(['car_id' => $car->id]);

        $this->assertInstanceOf(Car::class, $fuelling->car);
        $this->assertEquals($car->id, $fuelling->car->id);
    }

    public function test_fuelling_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $fuelling = Fuelling::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $fuelling->user);
        $this->assertEquals($user->id, $fuelling->user->id);
    }

    public function test_fuelling_has_many_mileages(): void
    {
        $car = Car::factory()->create();
        $fuelling = Fuelling::factory()->create(['car_id' => $car->id]);
        Mileage::factory()->count(2)->create([
            'car_id' => $car->id,
            'fuelling_id' => $fuelling->id,
        ]);

        $this->assertCount(2, $fuelling->mileages);
        $this->assertInstanceOf(Mileage::class, $fuelling->mileages->first());
    }

    public function test_fuelling_cost_and_liters_default_to_zero(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);

        $fuelling = Fuelling::create([
            'car_id' => $car->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(0, (float) $fuelling->liters);
        $this->assertEquals(0, (float) $fuelling->cost);
    }
}
