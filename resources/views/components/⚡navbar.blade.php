<?php

use Livewire\Component;

new class extends Component
{
    //
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
            </div>


            <!-- User Menu (Optional) -->
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">{{ auth()->user()?->name ?? 'Guest' }}</span>
            </div>
        </div>
    </div>
</nav>
