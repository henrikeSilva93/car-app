<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        <!-- @fluxAppearance -->
    </head>
    <body>
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
