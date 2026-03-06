<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Fuelling;
use App\Models\Maintenance;
use App\Models\User;
use App\Services\StatisticService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StatisticServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_maintenance_graph_returns_json_response(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        Maintenance::factory()->count(3)->create(['car_id' => $car->id]);
        $service = new StatisticService();
        $response = $service->maintenanceGraph($car->id);

        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertSame('line', $data['type']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('labels', $data['data']);
        $this->assertArrayHasKey('datasets', $data['data']);
    }

    public function test_maintenance_graph_returns_correct_dataset_structure(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'Civic',
        ]);
        $this->actingAs($user);

        Maintenance::factory()->count(2)->create(['car_id' => $car->id]);

        $service = new StatisticService();
        $response = $service->maintenanceGraph($car->id);
        $data = $response->getData(true);

        $this->assertCount(30, $data['data']['labels']);
        $this->assertCount(1, $data['data']['datasets']);
        $this->assertArrayHasKey('label', $data['data']['datasets'][0]);
        $this->assertArrayHasKey('data', $data['data']['datasets'][0]);
        $this->assertCount(30, $data['data']['datasets'][0]['data']);
    }

    public function test_maintenance_graph_ignores_maintenances_older_than_30_days(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        Maintenance::factory()->create([
            'car_id' => $car->id,
            'cost' => 100,
            'created_at' => Carbon::now()->subDays(40),
            'updated_at' => Carbon::now()->subDays(40),
        ]);
        Maintenance::factory()->create([
            'car_id' => $car->id,
            'cost' => 200,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        $service = new StatisticService();
        $response = $service->maintenanceGraph($car->id);
        $data = $response->getData(true);

        $values = array_map('floatval', $data['data']['datasets'][0]['data']);

        $this->assertSame(200.0, array_sum($values));
    }

    public function test_maintenance_graph_returns_unauthorized_for_non_owned_car(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $otherUser->id]);
        $this->actingAs($user);

        $service = new StatisticService();
        $response = $service->maintenanceGraph($car->id);
        $data = $response->getData(true);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertSame('Unauthorized', $data['error']);
    }

    public function test_fuelling_graph_returns_chartjs_payload(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        Fuelling::factory()->create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'cost' => 150,
            'date' => Carbon::now()->subDays(3)->toDateString(),
        ]);

        $service = new StatisticService();
        $response = $service->fuellingGraph($car->id);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('line', $data['type']);
        $this->assertCount(30, $data['data']['labels']);
        $this->assertCount(1, $data['data']['datasets']);
        $this->assertCount(30, $data['data']['datasets'][0]['data']);
    }

    public function test_fuelling_graph_ignores_records_older_than_30_days(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        Fuelling::factory()->create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'cost' => 80,
            'date' => Carbon::now()->subDays(40)->toDateString(),
        ]);
        Fuelling::factory()->create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'cost' => 220,
            'date' => Carbon::now()->subDays(2)->toDateString(),
        ]);

        $service = new StatisticService();
        $response = $service->fuellingGraph($car->id);
        $data = $response->getData(true);

        $values = array_map('floatval', $data['data']['datasets'][0]['data']);
        $this->assertSame(220.0, array_sum($values));
    }

    public function test_total_cost_last_12_months_graph_returns_chartjs_payload(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $anotherCar = Car::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        Maintenance::factory()->create([
            'car_id' => $car->id,
            'cost' => 300,
            'created_at' => Carbon::now()->subMonths(2),
            'updated_at' => Carbon::now()->subMonths(2),
        ]);
        Fuelling::factory()->create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'cost' => 120,
            'date' => Carbon::now()->subMonths(2)->toDateString(),
        ]);
        Maintenance::factory()->create([
            'car_id' => $anotherCar->id,
            'cost' => 999,
            'created_at' => Carbon::now()->subMonths(2),
            'updated_at' => Carbon::now()->subMonths(2),
        ]);

        $service = new StatisticService();
        $response = $service->totalCostLast12MonthsGraph($car->id);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('line', $data['type']);
        $this->assertCount(12, $data['data']['labels']);
        $this->assertCount(1, $data['data']['datasets']);
        $this->assertCount(12, $data['data']['datasets'][0]['data']);
        $values = array_map('floatval', $data['data']['datasets'][0]['data']);
        $this->assertSame(420.0, end($values));
    }

    public function test_total_cost_last_12_months_graph_aggregates_owned_cars_only(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $carA = Car::factory()->create(['user_id' => $user->id]);
        $carB = Car::factory()->create(['user_id' => $user->id]); // should not be included when querying carA
        $otherCar = Car::factory()->create(['user_id' => $otherUser->id]);
        $this->actingAs($user);

        Maintenance::factory()->create([
            'car_id' => $carA->id,
            'cost' => 120,
            'created_at' => Carbon::now()->subMonths(1),
            'updated_at' => Carbon::now()->subMonths(1),
        ]);
        Maintenance::factory()->create([
            'car_id' => $carB->id,
            'cost' => 180,
            'created_at' => Carbon::now()->subMonths(1),
            'updated_at' => Carbon::now()->subMonths(1),
        ]);
        Fuelling::factory()->create([
            'car_id' => $carA->id,
            'user_id' => $user->id,
            'cost' => 90,
            'date' => Carbon::now()->subMonths(1)->toDateString(),
        ]);
        Maintenance::factory()->create([
            'car_id' => $otherCar->id,
            'cost' => 999,
            'created_at' => Carbon::now()->subMonths(1),
            'updated_at' => Carbon::now()->subMonths(1),
        ]);
        Fuelling::factory()->create([
            'car_id' => $otherCar->id,
            'user_id' => $otherUser->id,
            'cost' => 999,
            'date' => Carbon::now()->subMonths(1)->toDateString(),
        ]);
        Maintenance::factory()->create([
            'car_id' => $carA->id,
            'cost' => 50,
            'created_at' => Carbon::now()->subMonths(14),
            'updated_at' => Carbon::now()->subMonths(14),
        ]);
        Fuelling::factory()->create([
            'car_id' => $carA->id,
            'user_id' => $user->id,
            'cost' => 50,
            'date' => Carbon::now()->subMonths(14)->toDateString(),
        ]);

        $service = new StatisticService();
        $response = $service->totalCostLast12MonthsGraph($carA->id);
        $data = $response->getData(true);

        $values = array_map('floatval', $data['data']['datasets'][0]['data']);
        $this->assertSame(210.0, end($values));
    }

    public function test_total_cost_last_12_months_graph_returns_unauthorized_for_non_owned_car(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $otherUser->id]);
        $this->actingAs($user);

        $service = new StatisticService();
        $response = $service->totalCostLast12MonthsGraph($car->id);
        $data = $response->getData(true);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertSame('Unauthorized', $data['error']);
    }

    public function test_total_cost_last_12_months_graph_uses_created_at_when_fuelling_date_is_empty(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        Fuelling::factory()->create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'cost' => 150,
            'date' => '',
            'created_at' => Carbon::now()->subMonths(1),
            'updated_at' => Carbon::now()->subMonths(1),
        ]);

        $service = new StatisticService();
        $response = $service->totalCostLast12MonthsGraph($car->id);
        $data = $response->getData(true);

        $values = array_map('floatval', $data['data']['datasets'][0]['data']);
        $this->assertSame(150.0, end($values));
    }
}
