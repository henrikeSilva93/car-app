<?php

use Livewire\Component;
use App\Models\Car;
use App\Models\Fuelling;

new class extends Component
{
    public $fuellings = [];

    public $fuelling = [
        'liters' => '',
        'cost' => '',
        'station' => '',
        'date' => '',
        'selected_car' => '',
        'mileage' => '',
    ];

    public $modal_action = 'create';
    public $userCars = [];

    public function mount()
    {
        $this->userCars = Car::where('user_id', 1)->get()->toArray();
        $this->getFuellings();
    }

    public function getFuellings()
    {
        $this->fuellings = Fuelling::join('cars', 'fuellings.car_id', '=', 'cars.id')
            ->whereIn('car_id', array_column($this->userCars, 'id'))
            ->select(['fuellings.*', 'cars.brand', 'cars.model'])
            ->get()
            ->toArray();
    }

    public function createFuelling()
    {
        $this->modal_action = 'edit';

        $fuelling = Fuelling::create([
            'car_id' => $this->fuelling['selected_car'],
            'user_id' => 1,
            'liters' => $this->fuelling['liters'],
            'cost' => $this->fuelling['cost'],
            'station' => $this->fuelling['station'],
            'date' => $this->fuelling['date'] ?: now()->toDateString(),
        ]);

        // Atualiza a quilometragem do veículo
        $car = Car::find($this->fuelling['selected_car']);
        if ($car && $this->fuelling['mileage']) {
            $car->mileage = $this->fuelling['mileage'];
            $car->save();
        }

        Flux::modal('add-fuelling')->show();
        $this->resetForm();
        $this->getFuellings();
        session()->flash('success', 'Abastecimento criado com sucesso.');
    }

    public function editFuelling($id)
    {
        $this->modal_action = 'edit';
        $f = Fuelling::find($id);
        if ($f) {
            $car = Car::find($f->car_id);
            $this->fuelling = [
                'id' => $id,
                'selected_car' => $f->car_id,
                'liters' => $f->liters,
                'cost' => $f->cost,
                'station' => $f->station,
                'date' => $f->date,
                'mileage' => $car ? $car->mileage : '',
            ];
            Flux::modal('add-fuelling')->show();
        }
    }

    public function updateFuelling()
    {
        $f = Fuelling::find($this->fuelling['id']);
        if ($f) {
            $f->update([
                'car_id' => $this->fuelling['selected_car'],
                'liters' => $this->fuelling['liters'],
                'cost' => $this->fuelling['cost'],
                'station' => $this->fuelling['station'],
                'date' => $this->fuelling['date'],
            ]);
            // Atualiza a quilometragem do veículo
            $car = Car::find($this->fuelling['selected_car']);
            if ($car && $this->fuelling['mileage']) {
                $car->mileage = $this->fuelling['mileage'];
                $car->save();
            }
            Flux::modal('add-fuelling')->close();
            $this->resetForm();
            $this->getFuellings();
            session()->flash('success', 'Abastecimento atualizado com sucesso.');
        }
    }

    public function confirmDeleteFuelling($id)
    {
        $this->fuelling['id'] = $id;
        Flux::modal('confirm-delete-fuelling')->show();
    }

    public function deleteFuelling()
    {
        $f = Fuelling::find($this->fuelling['id']);
        if ($f) {
            $f->delete();
            Flux::modal('confirm-delete-fuelling')->close();
            $this->resetForm();
            $this->getFuellings();
            session()->flash('delete', 'Abastecimento deletado com sucesso.');
        }
    }

    public function cancelDelete()
    {
        $this->resetForm();
        Flux::modal('confirm-delete-fuelling')->close();
    }

    public function resetForm()
    {
        $this->fuelling = [
            'liters' => '',
            'cost' => '',
            'station' => '',
            'date' => '',
            'selected_car' => '',
            'mileage' => '',
        ];

         $this->modal_action = 'create';
    }
};

?>

<div class="p-6 sm:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <x-title title="Abastecimento" subtitle="Gerencie os abastecimentos do seu veículo"/>
        <flux:modal.trigger name="add-fuelling">
            <flux:button icon="plus" class="px-6">Cadastrar Abastecimento</flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Modal: add-fuelling -->
<flux:modal name="add-fuelling" class="md:w-96" @close="resetForm">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $modal_action == 'edit' ? 'Editar Abastecimento' : 'Adicionar Abastecimento' }}</flux:heading>
            <flux:text class="mt-2">Preencha os detalhes do abastecimento do seu veículo.</flux:text>
        </div>

        <flux:select wire:model="fuelling.selected_car" label="Veículo" placeholder="Selecione o Veículo">
            @foreach($this->userCars as $car)
                <flux:select.option value="{{ $car['id'] }}">{{ $car['brand'] }} - {{ $car['model'] }}</flux:select.option>
            @endforeach
        
        </flux:select>

        <flux:input label="Litros" type="number" step="0.01" placeholder="Ex: 40.00" wire:model="fuelling.liters"/>

        <flux:input label="Valor (R$)" type="number" step="0.01" placeholder="Ex: 200.00" wire:model="fuelling.cost"/>

        <flux:input label="Posto" placeholder="Ex: Posto XYZ" wire:model="fuelling.station"/>

        <flux:input label="Data" type="date" wire:model="fuelling.date"/>

        <flux:input label="Quilometragem" type="number" step="1" placeholder="Ex: 50000" wire:model="fuelling.mileage"/>

        <div class="flex">
            <flux:spacer />

        @if($modal_action == 'create')
                <flux:button type="submit" wire:click="createFuelling">Salvar</flux:button>
        @elseif($modal_action == 'edit')
                <flux:button type="submit" wire:click="updateFuelling">Atualizar</flux:button>        
        @endif
        </div>
    </div>
</flux:modal>

<flux:modal name="confirm-delete-fuelling" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Confirmar Exclusão</flux:heading>
            <flux:text class="mt-2">Tem certeza que deseja deletar este abastecimento? Esta ação não pode ser desfeita.</flux:text>
        </div>

        <div class="flex gap-3 justify-end">
            <flux:button variant="ghost" wire:click="$call('cancelDelete')">Cancelar</flux:button>
            <flux:button variant="danger" wire:click="deleteFuelling">Deletar</flux:button>
        </div>
    </div>
</flux:modal>

<div class="px-6 sm:px-8 pb-8">
    <div class="overflow-x-auto rounded-xl shadow-lg">
        <table class="min-w-full bg-white">
            <thead class="bg-white border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Carro</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Litros</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Posto</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse ($fuellings as $item)
                <tr class="hover:bg-gray-50 transition-colors duration-150 group">
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{$item['brand'] ?? 'Marca'}} {{$item['model'] ?? 'Modelo'}}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($item['liters'] ?? 0, 2, ',', '.') }} L</td>
                    <td class="px-6 py-4 text-sm text-gray-600"><span class="bg-yellow-100 p-2 rounded-full font-bold">R$ {{ number_format($item['cost'] ?? 0, 2, ',', '.') }}<span></td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{$item['station'] ?? '-'}}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ isset($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-right gap-1">
                            <button title="Editar" class="text-gray-400 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50 transition" wire:click="editFuelling({{ $item['id'] }})">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                            </button>
                            <button title="Deletar" class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition" wire:click="confirmDeleteFuelling({{$item['id']}})">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 0a2 2 0 104 0m0 0a2 2 0 11-4 0"/></svg>
                            <p class="text-gray-500 text-lg font-medium">Nenhum Abastecimento registrado</p>
                            <p class="text-gray-400 text-sm mt-1">Comece adicionando seu primeiro abastecimento</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

