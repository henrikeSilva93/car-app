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

<nav class="bg-white dark:bg-gray-800 shadow-md dark:shadow-gray-900/50 transition-colors duration-300">
    <div class="px-6 mx-auto">
        
        <div class="flex items-center justify-between h-16">
            
            <!-- Logo/Brand -->
            <div class="flex-shrink-0">
                <a href="/" class="text-2xl font-bold text-blue-600 dark:text-blue-400">CarApp</a>
            </div>
            <!-- Menu Items -->
            <div class="flex space-x-8">
                @if(auth()->check())
                    <a href="/" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition duration-200 {{ request()->is('/') ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : '' }}">
                        Dashboard
                    </a>
                    <a href="/cars" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition duration-200 {{ request()->is('cars*') ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : '' }}">
                        Veículos
                    </a>
                    <a href="/maintenance" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition duration-200 {{ request()->is('maintenance*') ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : '' }}">
                        Manutenção
                    </a>
                    <a href="/fuelling" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition duration-200 {{ request()->is('fuelling*') ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : '' }}">
                        Abastecimentos
                    </a>
                @endif
            </div>


            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>

                @if(auth()->check())
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ auth()->user()->name }}</span>
                 
                        
                        <button type="submit" wire:click="logout" class="text-sm text-red-600 dark:text-red-400 hover:underline">Sair</button>
                    
                @else
                    <a href="auth/login" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Entrar</a>
                    <a href="auth/register" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Registrar</a>
                @endif
            </div>
        </div>
    </div>
</nav>
