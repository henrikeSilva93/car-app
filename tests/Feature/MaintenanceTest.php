<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Maintenance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_maintenance(): void
    {
        $car = Car::factory()->create();

        $maintenance = Maintenance::create([
            'car_id' => $car->id,
            'description' => 'Troca de óleo',
            'cost' => 150.00,
        ]);

        $this->assertDatabaseHas('maintenances', [
            'car_id' => $car->id,
            'description' => 'Troca de óleo',
            'cost' => 150.00,
        ]);
    }

    public function test_can_update_maintenance(): void
    {
        $maintenance = Maintenance::factory()->create(['cost' => 100.00]);

        $maintenance->update(['cost' => 300.00, 'description' => 'Troca de pneus']);

        $fresh = $maintenance->fresh();
        $this->assertEquals(300.00, (float) $fresh->cost);
        $this->assertEquals('Troca de pneus', $fresh->description);
    }

    public function test_can_delete_maintenance(): void
    {
        $maintenance = Maintenance::factory()->create();

        $id = $maintenance->id;
        $maintenance->delete();

        $this->assertDatabaseMissing('maintenances', ['id' => $id]);
    }

    public function test_maintenance_belongs_to_car(): void
    {
        $car = Car::factory()->create();
        $maintenance = Maintenance::factory()->create(['car_id' => $car->id]);

        $this->assertInstanceOf(Car::class, $maintenance->car);
        $this->assertEquals($car->id, $maintenance->car->id);
    }

    public function test_can_list_maintenances_for_car(): void
    {
        $car = Car::factory()->create();
        Maintenance::factory()->count(5)->create(['car_id' => $car->id]);

        $maintenances = Maintenance::where('car_id', $car->id)->get();

        $this->assertCount(5, $maintenances);
    }

    public function test_maintenance_cost_is_decimal(): void
    {
        $maintenance = Maintenance::factory()->create(['cost' => 1234.56]);

        $this->assertEquals(1234.56, (float) $maintenance->fresh()->cost);
    }
}
