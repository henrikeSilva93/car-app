<div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6 border-l-4 {{ $borderColor }}">
    <div class="flex items-start justify-between gap-4">
        <!-- Ícone à Esquerda -->
        <div class="flex-shrink-0 {{ $textColor }}">
            {!! $iconSvg !!}
        </div>

        <!-- Valores à Direita -->
        <div class="flex-1 text-right">
            <p class="text-gray-500 text-xs font-semibold uppercase tracking-widest mb-2">{{ $title }}</p>
            <h3 class="text-4xl font-bold text-gray-900 mb-1">{{ $value }}</h3>
            @if($subtitle)
                <p class="text-sm text-gray-600 mb-2">{{ $subtitle }}</p>
            @endif
            @if($month)
                <p class="text-xs text-gray-400 font-medium">{{ $month }}</p>
            @endif
        </div>
    </div>
</div>
