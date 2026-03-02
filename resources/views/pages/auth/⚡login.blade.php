<?php

use Livewire\Component;

new class extends Component
{
    public $email = '';
    public $password = '';

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if(Auth::attempt($credentials)) {
            session()->regenerate();
            return redirect()->intended(route('home'));
        }else {
            session()->flash('error', 'Credenciais inválidas. Tente novamente.');
        }
    }
};
?>

<div>
         <x-alert-component/>
        <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg dark:shadow-gray-900/50 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-gray-100">Entrar</h2>
            <form wire:submit.prevent="login">
                <div class="space-y-4">
                    <flux:input label="E-mail" type="email" placeholder="seu@email.com" wire:model="email" required />
                    <flux:input label="Senha" type="password" placeholder="••••••••" wire:model="password" required />
                </div>
                <div class="mt-6 flex flex-col gap-2">
                    <flux:button type="submit" class="w-full" wire:click="login">Entrar</flux:button>
                    <a href="/register" class="text-sm text-blue-600 dark:text-blue-400 hover:underline text-center">Criar conta</a>
                </div>
            </form>
        </div>
    </div>

</div>