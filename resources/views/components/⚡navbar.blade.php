<?php

use Livewire\Component;

new class extends Component
{
    public function logout()
    {
        auth()->logout();
        session()->regenerate();
        return redirect('/auth/login');
    }
};
?>

<nav class="bg-white shadow-md">
    <div class="px-6 mx-auto">
        
        <div class="flex items-center justify-between h-16">
            
            <!-- Logo/Brand -->
            <div class="flex-shrink-0">
                <a href="/" class="text-2xl font-bold text-blue-600">CarApp</a>
            </div>
            <!-- Menu Items -->
            <div class="flex space-x-8">
                @if(auth()->check())
                    <a href="/" 
                       class="text-gray-700 hover:text-blue-600 transition duration-200 {{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Dashboard
                    </a>
                    <a href="/cars" 
                       class="text-gray-700 hover:text-blue-600 transition duration-200 {{ request()->is('cars*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Veículos
                    </a>
                    <a href="/maintenance" 
                       class="text-gray-700 hover:text-blue-600 transition duration-200 {{ request()->is('maintenance*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Manutenção
                    </a>
                    <a href="/fuelling" 
                       class="text-gray-700 hover:text-blue-600 transition duration-200 {{ request()->is('fuelling*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        Abastecimentos
                    </a>
                @endif
            </div>


            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                @if(auth()->check())
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                 
                        
                        <button type="submit" wire:click="logout" class="text-sm text-red-600 hover:underline">Sair</button>
                    
                @else
                    <a href="/login" class="text-sm text-blue-600 hover:underline">Entrar</a>
                    <a href="/register" class="text-sm text-blue-600 hover:underline">Registrar</a>
                @endif
            </div>
        </div>
    </div>
</nav>
