<?php
namespace App\Services;
use App\Models\Car;
use App\Models\Maintenance;
use App\Models\Fuelling;
use App\Models\Mileage;
use Illuminate\Support\Facades\Auth;

 class StatisticService
{
    public function MaitenanceGraph($car_id)
    {
      $car = Car::where('id', $car_id)
        ->where('user_id', auth()->id())
        ->first();

      if (!$car) {
        return response()->json(['error' => 'Unauthorized'], 403);
      }

      $veicles_maitenances = Car::join('maintenances', 'cars.id', '=', 'maintenances.car_id')
        ->where('cars.user_id', auth()->id())
        ->select('cars.id', 'cars.brand', 'cars.model', 'maintenances.description as maintenance_description', 'maintenances.cost as maintenance_cost', 'maintenances.created_at as date')
        ->get()
        ->collect();
     
      $labels = $veicles_maitenances->map(fn($v) => $v->date)->toArray();
    
      $datasets = $veicles_maitenances->groupBy('cars.id')->map(function ($group) {
        $car = $group->first();
        return [
          'label' => "{$car->brand} {$car->model}",
          'data' => $group->pluck('maintenance_cost')->toArray(),
        ];
      })->values()->toArray();

      return response()->json([
        'labels' => $labels,
        'datasets' => $datasets
      ]);
      
    }


}
