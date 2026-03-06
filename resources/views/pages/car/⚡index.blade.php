<?php

use Livewire\Component;
use App\Models\Car;

new class extends Component
{
    public $car = ['brand' => '', 'model' => '', 'year' => '', 'plate' => '', 'mileage' => '', 'id' => null];
    public $cars = [];
    public $modal_action = "";
    public $car_to_delete = null;

    public function mount()
    {
        $this->cars = $this->loadCars();
    }

    public function loadCars()
    {
        return Car::where('user_id', auth()->id())->get()->toArray();
        
    }


    public function createCar()
    {
        $validate = $this->validate([
            'car.brand' => 'required|string|max:255',
            'car.model' => 'required|string|max:255',
            'car.year' => 'required|integer|min:1900|max:' . date('Y'),
            'car.plate' => 'required|string|max:10|unique:cars,plate',
            'car.mileage' => 'required|integer|min:0',
        ]);
        Car::Create([
            'user_id' => auth()->id(),
            'brand' => $this->car['brand'],
            'model' => $this->car['model'],
            'year' => $this->car['year'],
            'plate' => $this->car['plate'],
            'mileage' => $this->car['mileage'],
        ]);
        session()->flash('success', 'Veículo adicionado com sucesso!');

        Flux::modal('add-veiculo')->close();
        $this->cars = $this->loadCars();

    }

    function editCar($carId)
    {
        $db_car = Car::where('id', $carId)->where('user_id', auth()->id())->first();
        if ($db_car) {
            $this->modal_action = "edit";
            $this->car = [ 
                'id' => $db_car->id,
                'brand' => $db_car->brand,
                'model' => $db_car->model,
                'year' => $db_car->year,
                'plate' => $db_car->plate,
                'mileage' => $db_car->mileage,
            ];
            
            Flux::modal('add-veiculo')->show();
        }
    }

 
    public function updateCar()
    {
        $validate = $this->validate([
            'car.brand' => 'required|string|max:255',
            'car.model' => 'required|string|max:255',
            'car.year' => 'required|integer|min:1900|max:' . date('Y'),
            'car.plate' => 'required|string|max:10|',
            'car.mileage' => 'required|integer|min:0',
        ]);


        $db_car = Car::where('id', $this->car['id'])->where('user_id', auth()->id())->first();
        if ($db_car) {
            $db_car->update([
                'brand' => $this->car['brand'],
                'model' => $this->car['model'],
                'year' => $this->car['year'],
                'plate' => $this->car['plate'],
                'mileage' => $this->car['mileage'],
            ]);
            session()->flash('success', 'Veículo atualizado com sucesso!');
    
            Flux::modal('add-veiculo')->close();
            $this->cars = $this->loadCars();

        }
    }


    function confirmDeleteCar($carId)
    {
        $this->car_to_delete = $carId;
        Flux::modal('confirm-delete')->show();
    }

    function deleteCar()
    {
        if ($this->car_to_delete) {
            $db_car = Car::where('id', $this->car_to_delete)->where('user_id', auth()->id())->first();
            if ($db_car) {
                $db_car->delete();
                Flux::modal('confirm-delete')->close();
                $this->car_to_delete = null;
                $this->cars = $this->loadCars();
                session()->flash('delete', 'Veículo deletado com sucesso!');
            }
        }
    }

    function cancelDelete()
    {
        $this->car_to_delete = null;
        Flux::modal('confirm-delete')->close();
    }

   

    function resetForm()
    {
        $this->modal_action = "create";
        $this->car = ['brand' => '', 'model' => '', 'year' => '', 'plate' => '', 'mileage' => '', 'id' => null];
    }
  

};
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300">
        <x-alert-component/> 
    <!-- Header Section -->
    <div class="p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <x-title title="Meus Veículos" subtitle="Gerencie seus veículos cadastrados aqui."/>
            <flux:modal.trigger name="add-veiculo">
                <flux:button icon="plus" class="px-6" wire:click="resetForm">Adicionar Veículo</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Table Section -->
    <div class="px-6 sm:px-8 pb-8">
        <div class="overflow-x-auto rounded-xl shadow-lg">
            <table class="min-w-full bg-white dark:bg-gray-800 transition-colors duration-300">
                <!-- Cabeçalho -->
                <thead class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Marca</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Modelo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ano</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Placa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Quilometragem</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <!-- Corpo -->
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($cars as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $item['brand'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item['model'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item['year'] }}</td>
                        <td class="px-6 py-4 text-center text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 group-hover:bg-gray-100 dark:group-hover:bg-gray-700">{{ $item['plate'] }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">{{ $item['mileage'] }} km</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-1">
                                <button title="Editar" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition" wire:click="editCar({{ $item['id'] }})">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </button>
                                <button title="Deletar" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition" wire:click="confirmDeleteCar({{ $item['id'] }})">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 0a2 2 0 104 0m0 0a2 2 0 11-4 0"/></path></svg>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Nenhum veículo cadastrado</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Comece adicionando seu primeiro veículo</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <flux:modal name="add-veiculo" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $modal_action == 'edit' ? 'Editar Veículo' : 'Adicionar Veículo' }}</flux:heading>
            <flux:text class="mt-2">Preencha os detalhes do seu veículo.</flux:text>
        </div>

        <flux:input label="Marca" placeholder="Ex: Toyota" wire:model="car.brand"/>

        <flux:input label="Modelo" placeholder="Ex: Corolla" wire:model="car.model"/>

        <flux:input label="Ano" type="number" min="1900" max="9999" placeholder="2024" wire:model="car.year"/>

        <flux:input label="Placa" placeholder="ABC-1234" wire:model="car.plate"/>

        <flux:input label="Quilometragem" type="number" min="0" placeholder="0" wire:model="car.mileage"/>

        <div class="flex">
            <flux:spacer />

        @if($modal_action == 'create')
                <flux:button type="submit" wire:click="createCar">Salvar</flux:button>
        @elseif($modal_action == 'edit')
                <flux:button type="submit" wire:click="updateCar">Atualizar</flux:button>        
        @endif
        </div>
    </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="confirm-delete" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Confirmar Exclusão</flux:heading>
                <flux:text class="mt-2">Tem certeza que deseja deletar este veículo? Esta ação não pode ser desfeita.</flux:text>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" wire:click="$call('cancelDelete')">Cancelar</flux:button>
                <flux:button variant="danger" wire:click="deleteCar">Deletar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>