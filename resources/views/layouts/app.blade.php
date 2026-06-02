<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inventario') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <livewire:layout.navigation />
        <div class="flex">
            @auth
            <aside class="w-64 bg-white border-r border-gray-200 min-h-screen hidden lg:block">
                <livewire:layout.sidebar />
            </aside>
            @endauth
            <main class="flex-1 p-6">
                @if (isset($header))
                    <header class="bg-white shadow-sm rounded-lg mb-6 p-4">
                        {{ $header }}
                    </header>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
