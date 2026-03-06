<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Car;
use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;

class StatisticService
{
    public function maintenanceGraph(int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$car) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $vehicleMaintenances = Car::join('maintenances', 'cars.id', '=', 'maintenances.car_id')
            ->where('cars.user_id', auth()->id())
            ->select(
                'cars.id',
                'cars.brand',
                'cars.model',
                'maintenances.description as maintenance_description',
                'maintenances.cost as maintenance_cost',
                'maintenances.created_at as date'
            )
            ->get()
            ->collect();

        $labels = $vehicleMaintenances->map(fn ($v) => $v->date)->toArray();

        $datasets = $vehicleMaintenances->groupBy('cars.id')->map(function ($group) {
            $carData = $group->first();

            return [
                'label' => "{$carData->brand} {$carData->model}",
                'data' => $group->pluck('maintenance_cost')->toArray(),
            ];
        })->values()->toArray();

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
    }
}
