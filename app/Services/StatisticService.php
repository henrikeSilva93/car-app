<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Car;
use App\Models\Fuelling;
use App\Models\Maintenance;
use Carbon\Carbon;
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

        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $maintenanceTotalsByDate = Maintenance::query()
            ->where('car_id', $car->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(cost) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $values = [];
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate->copy()->startOfDay())) {
            $dateKey = $cursor->toDateString();
            $labels[] = $dateKey;
            $values[] = (float) ($maintenanceTotalsByDate[$dateKey] ?? 0);
            $cursor->addDay();
        }

        return response()->json([
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => "Custo de manutencao - {$car->brand} {$car->model}",
                    'data' => $values,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'tension' => 0.3,
                    'fill' => true,
                ]],
            ],
            'meta' => [
                'car_id' => $car->id,
                'period' => [
                    'days' => 30,
                    'start' => $startDate->toDateString(),
                    'end' => Carbon::now()->toDateString(),
                ],
            ],
        ]);
    }

    public function fuellingGraph(int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$car) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $startDateString = $startDate->toDateString();
        $endDateString = $endDate->toDateString();
        $fuellingDateExpression = "DATE(COALESCE(NULLIF(date, ''), created_at))";

        $fuellingTotalsByDate = Fuelling::query()
            ->where('car_id', $car->id)
            ->whereRaw("{$fuellingDateExpression} BETWEEN ? AND ?", [$startDateString, $endDateString])
            ->selectRaw("{$fuellingDateExpression} as date, SUM(cost) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $values = [];
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate->copy()->startOfDay())) {
            $dateKey = $cursor->toDateString();
            $labels[] = $dateKey;
            $values[] = (float) ($fuellingTotalsByDate[$dateKey] ?? 0);
            $cursor->addDay();
        }

        return response()->json([
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => "Custo de abastecimento - {$car->brand} {$car->model}",
                    'data' => $values,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'tension' => 0.3,
                    'fill' => true,
                ]],
            ],
            'meta' => [
                'car_id' => $car->id,
                'period' => [
                    'days' => 30,
                    'start' => $startDate->toDateString(),
                    'end' => Carbon::now()->toDateString(),
                ],
            ],
        ]);
    }

    public function totalCostLast12MonthsGraph(int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$car) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $startMonth = Carbon::now()->subMonths(11)->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        $startDateString = $startMonth->toDateString();
        $endDateString = $endMonth->toDateString();
        $fuellingDateExpression = "DATE(COALESCE(NULLIF(date, ''), created_at))";

        $maintenanceTotalsByMonth = Maintenance::query()
            ->where('car_id', $car->id)
            ->whereBetween('created_at', [$startMonth, $endMonth])
            ->get(['cost', 'created_at'])
            ->groupBy(fn ($maintenance) => Carbon::parse($maintenance->created_at)->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('cost'));

        $fuellingTotalsByMonth = Fuelling::query()
            ->where('car_id', $car->id)
            ->whereRaw("{$fuellingDateExpression} BETWEEN ? AND ?", [$startDateString, $endDateString])
            ->get(['cost', 'date', 'created_at'])
            ->groupBy(fn ($fuelling) => Carbon::parse($fuelling->date ?? $fuelling->created_at)->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('cost'));

        $labels = [];
        $values = [];
        $cursor = $startMonth->copy();
        $runningTotal = 0.0;

        while ($cursor->lte($endMonth->copy()->startOfMonth())) {
            $monthKey = $cursor->format('Y-m');
            $labels[] = $monthKey;
            $maintenanceValue = (float) ($maintenanceTotalsByMonth[$monthKey] ?? 0);
            $fuellingValue = (float) ($fuellingTotalsByMonth[$monthKey] ?? 0);
            $runningTotal += $maintenanceValue + $fuellingValue;
            $values[] = round($runningTotal, 2);
            $cursor->addMonth();
        }

        return response()->json([
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => "Custo total acumulado (manutencao + abastecimento) - {$car->brand} {$car->model}",
                    'data' => $values,
                    'borderColor' => 'rgb(14, 116, 144)',
                    'backgroundColor' => 'rgba(14, 116, 144, 0.2)',
                    'tension' => 0.3,
                    'fill' => true,
                ]],
            ],
            'meta' => [
                'car_id' => $car->id,
                'period' => [
                    'months' => 12,
                    'start' => $startMonth->toDateString(),
                    'end' => Carbon::now()->toDateString(),
                ],
            ],
        ]);
    }
}
