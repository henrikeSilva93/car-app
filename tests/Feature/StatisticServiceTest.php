<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Maintenance;
use App\Models\User;
use App\Services\StatisticService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_maintenance_graph_returns_json_response(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        Maintenance::factory()->count(3)->create(['car_id' => $car->id]);

        $service = new StatisticService();
        $response = $service->MaitenanceGraph($car->id);

        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('labels', $data);
        $this->assertArrayHasKey('datasets', $data);
    }

    public function test_maintenance_graph_returns_correct_dataset_structure(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'Civic',
        ]);
        Maintenance::factory()->count(2)->create(['car_id' => $car->id]);

        $service = new StatisticService();
        $response = $service->MaitenanceGraph($car->id);
        $data = $response->getData(true);

        $this->assertNotEmpty($data['datasets']);
        $this->assertArrayHasKey('label', $data['datasets'][0]);
        $this->assertArrayHasKey('data', $data['datasets'][0]);
    }

    public function test_maintenance_graph_with_no_maintenances(): void
    {
        $service = new StatisticService();
        $response = $service->MaitenanceGraph(999);
        $data = $response->getData(true);

        $this->assertEmpty($data['labels']);
        $this->assertEmpty($data['datasets']);
    }
}
