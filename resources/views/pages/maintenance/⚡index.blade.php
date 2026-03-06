<?php

use Livewire\Component;
use App\Models\Car;
use App\Models\Maintenance;

new class extends Component
{
    public $maintenances = [];

    public $maintenance = [
        'value' => '',
        'description' => '',
        'selected_car' => '',
    ];

    public $modal_action = 'create';
    public $userCars = [];

    public function mount()
    {
        $this->userCars = Car::where('user_id', auth()->id())->get()->toArray();
        $this->getMaitenances();
    }   

    public function getMaitenances() {
        $this->maintenances = Maintenance::join('cars', 'maintenances.car_id', '=', 'cars.id')
            ->whereIn('car_id', array_column($this->userCars, 'id'))
            ->select(['maintenances.*', 'cars.brand', 'cars.model'])
            ->get()
            ->toArray();
    }
    
    public function createMaintenance()
    {
       $this->modal_action = 'edit';
    
        $maintenance = Maintenance::create([
            'car_id' => $this->maintenance['selected_car'],
            'cost' => $this->maintenance['value'],
            'description' => $this->maintenance['description'],
        ]);

        Flux::modal('add-veiculo')->show();
       $this->resetForm();
       $this->getMaitenances();
       session()->flash('success', 'Manutenção criada com sucesso.');
    }

    public function editMaintenance($id)
    {
        $maintenance = Maintenance::where('id', $id)
            ->whereHas('car', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->first();
            
        if ($maintenance) {
            $this->modal_action = 'edit';
            $this->maintenance = [
                'id' => $id,
                'selected_car' => $maintenance->car_id,
                'value' => $maintenance->cost,
                'description' => $maintenance->description,
            ];
            Flux::modal('add-veiculo')->show();
       
        }
    }

    public function updateMaintenance()
    {
        $maintenance = Maintenance::where('id', $this->maintenance['id'])
            ->whereHas('car', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->first();
            
        if ($maintenance) {
            $maintenance->update([
                'car_id' => $this->maintenance['selected_car'],
                'cost' => $this->maintenance['value'],
                'description' => $this->maintenance['description'],
            ]);
            Flux::modal('add-veiculo')->close();
            $this->resetForm();
            $this->getMaitenances();
            session()->flash('success', 'Manutenção atualizada com sucesso.');
        }
    }

    public function confirmDeleteMaitenance($id)
    {
        $this->maintenance['id'] = $id;
        Flux::modal('confirm-delete')->show();
    }

    public function deleteMaintenance()
    {
        $maintenance = Maintenance::where('id', $this->maintenance['id'])
            ->whereHas('car', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->first();
            
        if ($maintenance) {
            $maintenance->delete();
            Flux::modal('confirm-delete')->close();
            $this->resetForm();
            $this->getMaitenances();
            session()->flash('delete', 'Manutenção deletada com sucesso.');
        }
    }

    public function cancelDelete()
    {
        $this->resetForm();
        Flux::modal('confirm-delete')->close();
    }

    public function resetForm()
    {
        $this->maitenance = [
            'car_id' => '',
            'value' => '',
            'description' => '',
        ];

         $this->modal_action = 'create';
      
    }
};


?>

<div class="p-4 dark:bg-gray-900 min-h-screen transition-colors duration-300">
     <x-alert-component/>
        <div class="p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <x-title title="Manutenções" subtitle="Gerencie suas manutenções dos Seus veículos aqui"/>
            <flux:modal.trigger name="add-veiculo">
                <flux:button icon="plus" class="px-6">Cadastrar Manutenções</flux:button>
            </flux:modal.trigger>
        </div>
    </div>
    <div class="px-6 sm:px-8 pb-8">
        <div class="overflow-x-auto rounded-xl shadow-lg">
            <table class="min-w-full bg-white dark:bg-gray-800 transition-colors duration-300">
                <thead class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Carro</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Data</th>
                         <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($maintenances as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">{{$item['brand']}} {{$item['model']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><span class="bg-green-300 dark:bg-green-700 dark:text-green-100 p-2 rounded-full font-bold">R$ {{number_format($item['cost'], 2, ',', '.')}}<span></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{$item['description']}}</td>
                         <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-right gap-1">
                                <button title="Editar" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition" wire:click="editMaintenance({{ $item['id'] }})">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </button>
                                <button title="Deletar" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition" wire:click="confirmDeleteMaitenance({{$item['id']}})">
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
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Nenhuma Manutenção registrada</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Comece adicionando sua primeira manutenção</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <flux:modal name="add-veiculo" class="md:w-96" @close="resetForm">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $modal_action == 'edit' ? 'Editar Manutenção' : 'Adicionar Manutenção' }}</flux:heading>
            <flux:text class="mt-2">Preencha os detalhes da manutenção do seu veículo.</flux:text>
        </div>
        <flux:select wire:model="maintenance.selected_car" label="Veículo" placeholder="Selecione o Veículo">
            @foreach($this->userCars as $car)
                <flux:select.option value="{{ $car['id'] }}">{{ $car['brand'] }} - {{ $car['model'] }}</flux:select.option>
            @endforeach
        
        </flux:select>
        <flux:textArea label="Descrição" placeholder="Ex: troca de óleo" wire:model="maintenance.description"/>

        <flux:input label="Valor" type="number" step="0.01" placeholder="R$ 150,00" wire:model="maintenance.value"/>

        <div class="flex">
            <flux:spacer />

        @if($modal_action == 'create')
                <flux:button type="submit" wire:click="createMaintenance">Salvar</flux:button>
        @elseif($modal_action == 'edit')
                <flux:button type="submit" wire:click="updateMaintenance">Atualizar</flux:button>        
        @endif
        </div>
    </div>
    </flux:modal>

    <flux:modal name="confirm-delete" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Confirmar Exclusão</flux:heading>
                <flux:text class="mt-2">Tem certeza que deseja deletar esta manutenção? Esta ação não pode ser desfeita.</flux:text>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" wire:click="$call('cancelDelete')">Cancelar</flux:button>
                <flux:button variant="danger" wire:click="deleteMaintenance">Deletar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>


    