<?php

use Livewire\Component;

new class extends Component
{   
    public $title = '';
    public $subtitle = '';

    public function mount($title, $subtitle)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
    }
    
};
?>


<div>
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100">{{ $title }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $subtitle }}</p>
</div>
