<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Fuelling;
use App\Models\Mileage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MileageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_mileage(): void
    {
        $car = Car::factory()->create();
        $fuelling = Fuelling::factory()->create(['car_id' => $car->id]);

        $mileage = Mileage::create([
            'car_id' => $car->id,
            'fuelling_id' => $fuelling->id,
            'mileage' => 55000,
        ]);

        $this->assertDatabaseHas('mileages', [
            'car_id' => $car->id,
            'fuelling_id' => $fuelling->id,
            'mileage' => 55000,
        ]);
    }

    public function test_can_update_mileage(): void
    {
        $mileage = Mileage::factory()->create(['mileage' => 10000]);

        $mileage->update(['mileage' => 15000]);

        $this->assertEquals(15000, $mileage->fresh()->mileage);
    }

    public function test_can_delete_mileage(): void
    {
        $mileage = Mileage::factory()->create();

        $id = $mileage->id;
        $mileage->delete();

        $this->assertDatabaseMissing('mileages', ['id' => $id]);
    }

    public function test_mileage_belongs_to_car(): void
    {
        $car = Car::factory()->create();
        $mileage = Mileage::factory()->create(['car_id' => $car->id]);

        $this->assertInstanceOf(Car::class, $mileage->car);
        $this->assertEquals($car->id, $mileage->car->id);
    }

    public function test_mileage_belongs_to_fuelling(): void
    {
        $fuelling = Fuelling::factory()->create();
        $mileage = Mileage::factory()->create(['fuelling_id' => $fuelling->id]);

        $this->assertInstanceOf(Fuelling::class, $mileage->fuelling);
        $this->assertEquals($fuelling->id, $mileage->fuelling->id);
    }

    public function test_mileage_is_deleted_when_car_is_deleted(): void
    {
        $car = Car::factory()->create();
        $fuelling = Fuelling::factory()->create(['car_id' => $car->id]);
        Mileage::factory()->create([
            'car_id' => $car->id,
            'fuelling_id' => $fuelling->id,
        ]);

        $car->delete();

        $this->assertDatabaseMissing('mileages', ['car_id' => $car->id]);
    }
}
