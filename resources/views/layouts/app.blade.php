<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        <!-- @fluxAppearance -->
    </head>
    <body class="bg-white dark:bg-gray-900 transition-colors duration-300">
        <!-- Navbar -->
        <livewire:navbar />

        {{ $slot }}

        @livewireScripts
        @fluxScripts
          @if(auth()->check())
          <livewire:chat-bot/>
        @endif
    </body>
</html>
