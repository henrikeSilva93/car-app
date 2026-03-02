<?php

use Livewire\Component;
use Livewire\Attributes\On; 
new class extends Component
{
    public $title = 'Informação';
    public $value = '0';
    public $subtitle = '';
    public $iconType = 'chart'; // chart, cash, wrench, car, fuel
    public $backgroundColor = 'bg-blue-50';
    public $textColor = 'text-blue-600';
    public $borderColor = 'border-blue-500';
    public $month = '';
    public $statKey = '';

    public function mount(
        $title = 'Informação',
        $value = '0',
        $subtitle = '',
        $iconType = 'chart',
        $backgroundColor = 'bg-blue-50',
        $textColor = 'text-blue-600',
        $borderColor = 'border-blue-500',
        $month = '',
        $statKey = ''
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->subtitle = $subtitle;
        $this->iconType = $iconType;
        $this->backgroundColor = $backgroundColor;
        $this->textColor = $textColor;
        $this->borderColor = $borderColor;
        $this->month = $month ?: now()->format('F Y');
        $this->statKey = $statKey;
    }

   #[On('stat-updated')] 
    public function statiticsUpdate($stats) {
       
        $this->value = $stats[$this->statKey] ?? 0;
        
    }

    public function getIcon()
    {
        $icons = [
            'chart' => '<svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
            'cash' => '<svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'wrench' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
  <path d="M14.7 2.3c-1.2 1.2-1.5 3-1 4.5L6 14.5 2.3 18.2c-.4.4-.4 1 0 1.4l2.1 2.1c.4.4 1 .4 1.4 0l3.7-3.7 7.7-7.7c1.5.5 3.3.2 4.5-1 .9-.9 1.3-2.1 1.3-3.3l-3.5 1.5-2-2 1.5-3.5c-1.2 0-2.4.4-3.3 1.3z"/>
</svg>

',
            'car' => '<svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2v4m6-6c1.657 0 3 .895 3 2v4m0 0a1 1 0 001 1h1a1 1 0 001-1m-6 0a1 1 0 001 1h1a1 1 0 001-1m0-6V5a2 2 0 012-2h.5A2.5 2.5 0 0116 4.5M9 9a2 2 0 00-2-2h-.5A2.5 2.5 0 006 9m0 0v3a1 1 0 001 1h1a1 1 0 001-1V9m0 0h2m0 0v3a1 1 0 001 1h1a1 1 0 001-1V9"></path></svg>',
            'fuel' => '<svg class="w-16" class="w-16" stroke="currentColor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  <rect x="20" y="20" width="40" height="60" rx="5" ry="5" fill="currentColor"/>
  <rect x="25" y="25" width="30" height="15" fill="#fff"/>
  <path d="M45 55a5 5 0 1 1-10 0c0-3 5-10 5-10s5 7 5 10z" fill="#fff"/>
  <path d="M60 30c5 0 10 5 10 10v30c0 5-5 10-10 10" stroke="currentColor" stroke-width="4" fill="none"/>
  <path d="M70 40l5-5" stroke="currentColor" stroke-width="4"/>
</svg>
',
            'maintenance' => '<svg class="w-16 h-16" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M21 7l-4 4-2-2 4-4 2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><path d="M3 21l7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><path d="M7 17l-2 2 4-1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>',
        ];

      
        return $icons[$this->iconType] ?? $icons['chart'];
    }
};
?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg dark:shadow-gray-900/50 transition-all duration-300 p-6 border-l-4 {{ $borderColor }}">
    <div class="flex items-start justify-between gap-4">
        <!-- Ícone Heroicons à Esquerda -->
        <div class="flex-shrink-0 {{ $textColor }} {{ $backgroundColor }} p-3 rounded-lg">
            {!! $this->getIcon() !!}
        </div>

        <!-- Valores à Direita -->
        <div class="flex-1 text-right">
            <p class="text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-widest mb-2">{{ $title }}</p>
            <h3 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-1">R$ {{ number_format($value, 2, ',', '.') }}</h3>
            @if($subtitle)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $subtitle }}</p>
            @endif
            @if($month)
                <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ $month }}</p>
            @endif
        </div>
    </div>
</div>
