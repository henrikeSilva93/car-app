<?php

use Livewire\Component;
use App\Models\Car;
use App\Models\Maintenance;

new class extends Component
{
    public $cars = [];
    public $select_car = null;
    public $selectedCarDetails = null;
    public $statistics = [];

    public function mount()
    {
        $cars = Car::where('user_id',1)->get()->toArray();
        $this->cars = $cars;
       if( count($cars) > 0 ){
            $this->select_car = $cars[0]['id'];
            $this->selectedCarDetails = Car::where('id', $this->select_car)->first();
        }

        $this->getStatistics();
    }

    public function selectCar()
    {
        $this->selectedCarDetails = Car::where('id', $this->select_car)->first();
        $this->getStatistics();
        // emitir estatísticas para widgets Livewire
        $this->dispatch('stat-updated', $this->statistics);
     
    }

    public function getStatistics()
    {
        $this->statistics['total_maintenance_spent'] = Maintenance::where('car_id', $this->select_car)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('cost');

        $this->statistics['total_fuel_spent'] = \App\Models\Fuelling::where('car_id', $this->select_car)
            ->where('date', '>=', now()->subDays(30))
            ->sum('cost');

        $this->statistics['total_spent'] = $this->statistics['total_maintenance_spent'] + $this->statistics['total_fuel_spent'];
        // emitir inicial também
        $this->dispatch('stat-updated', stats: $this->statistics);
    }
};
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
    <div class="bg-white rounded-lg shadow-lg p-4">
        <div class="flex gap-6 items-center">
            <!-- Select de Veículos -->
            <div class="flex-shrink-0">
                <select 
                    id="car-select"
                    wire:model="select_car" 
                    wire:change="selectCar" 
                    class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 bg-white shadow-sm hover:border-blue-400 text-sm">
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
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ $selectedCarDetails->brand }} {{ $selectedCarDetails->model }}</h2>
                        <p class="text-xs text-gray-500">{{ $selectedCarDetails->year }} • {{ $selectedCarDetails->plate }}</p>
                    </div>
                    <div class="flex gap-4 ml-auto">
                        <div class="text-center">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Quilometragem</p>
                            <p class="text-lg font-bold text-green-600">{{ number_format($selectedCarDetails->mileage, 0, ',', '.') }} km</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex-1 text-gray-500 text-sm">
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
  <div>
    
  </div>
</div>