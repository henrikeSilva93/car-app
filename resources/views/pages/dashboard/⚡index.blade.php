<?php

use Livewire\Component;
use App\Models\Car;
use App\Models\Maintenance;
use App\Models\Fuelling;

new class extends Component
{
    public $cars = [];
    public $select_car = null;
    public $selectedCarDetails = null;
    public $statistics = [
        'total_maintenance_spent' => 0,
        'total_fuel_spent' => 0,
        'total_spent' => 0,
    ];

    public function mount()
    {
        $cars = Car::where('user_id', auth()->id())->get();
        
        $this->cars = $cars->toArray();

        if ($cars->isNotEmpty()) {
            $firstCar = $cars->first();
            $this->select_car = $firstCar ? $firstCar->id : null;
            $this->loadCarData();
        }
    }

    public function selectCar()
    {
        $this->loadCarData();
    }

    private function loadCarData(): void
    {
        if (!$this->select_car) {
            $this->selectedCarDetails = null;
            return;
        }

        $this->selectedCarDetails = Car::find($this->select_car);

        if ($this->selectedCarDetails) {
            $latestMileage = \App\Models\Mileage::where('car_id', $this->select_car)
                ->latest()
                ->first();

            $this->selectedCarDetails->mileage = $latestMileage?->mileage ?? $this->selectedCarDetails->mileage;
        }

        $this->calculateStatistics();
    }

    private function calculateStatistics(): void
    {
        if (!$this->select_car) {
            return;
        }

        $startDate = now()->subDays(30);

        $this->statistics['total_maintenance_spent'] = Maintenance::where('car_id', $this->select_car)
            ->where('created_at', '>=', $startDate)
            ->sum('cost');

        $this->statistics['total_fuel_spent'] = Fuelling::where('car_id', $this->select_car)
            ->where('created_at', '>=', $startDate)
            ->sum('cost');

        $this->statistics['total_spent'] = $this->statistics['total_maintenance_spent'] + $this->statistics['total_fuel_spent'];

        $this->dispatch('stat-updated', $this->statistics);
    }
};
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 p-6 transition-colors duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/50 p-4 transition-colors duration-300">
        <div class="flex gap-6 items-center">
            <!-- Select de Veículos -->
            <div class="flex-shrink-0">
                <select 
                    id="car-select"
                    wire:model="select_car" 
                    wire:change="selectCar" 
                    class="px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition duration-200 bg-white dark:bg-gray-700 dark:text-gray-200 shadow-sm hover:border-blue-400 text-sm">
                    <option value="">-- Selecione --</option>
                    @foreach($cars as $car)
                        <option value="{{ $car['id'] }}" {{ $select_car == $car['id'] ? 'selected' : '' }}>
                            {{ $car['brand'] }} {{ $car['model'] }} ({{ $car['year'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Informações do Veículo Selecionado -->
            @if($selectedCarDetails)
                <div class="flex-1 flex gap-4 items-center">
                    <div class="border-l-4 border-blue-500 dark:border-blue-400 pl-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $selectedCarDetails->brand }} {{ $selectedCarDetails->model }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedCarDetails->year }} • {{ $selectedCarDetails->plate }}</p>
                    </div>
                    <div class="flex gap-4 ml-auto">
                        <div class="text-center">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Quilometragem</p>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($selectedCarDetails->mileage, 0, ',', '.') }} km</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex-1 text-gray-500 dark:text-gray-400 text-sm">
                    Selecione um veículo para ver as informações
                </div>
            @endif
        </div>
    </div>
    <!--sessao do dashboard-->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <livewire:info-widget
        title="Gasto Total"
        value="{{$statistics['total_spent']}}"
        subtitle="últimos 30 dias"
        iconType="cash"
        backgroundColor="bg-green-100"
        textColor="text-green-600"
        borderColor="border-green-500"
        statKey="total_spent"
    />
    <livewire:info-widget
        title="Abastecimento Total"
        value="{{$statistics['total_fuel_spent']}}"
        subtitle="últimos 30 dias"
        iconType="fuel"
        backgroundColor="bg-yellow-100"
        textColor="text-yellow-600"
        borderColor="border-yellow-600"
        statKey="total_fuel_spent"
/>

   <livewire:info-widget
        title="Manutenção Total"
        subtitle="últimos 30 dias"
        value="{{$statistics['total_maintenance_spent']}}"
        iconType="wrench"
        backgroundColor="bg-blue-200"
        textColor="text-blue-600"
        borderColor="border-blue-600"
        statKey="total_maintenance_spent"
    />
  </div>

</div>


