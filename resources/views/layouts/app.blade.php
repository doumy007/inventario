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
    <div x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen" class="min-h-screen bg-gray-100">
        <livewire:layout.navigation />

        @auth
        {{-- Backdrop --}}
        <div x-show="sidebarOpen" x-cloak
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden">
        </div>

        {{-- Mobile sidebar toggle --}}
        <button @click="sidebarOpen = true"
            class="fixed bottom-4 right-4 z-50 lg:hidden flex items-center justify-center w-12 h-12 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        {{-- Mobile sidebar --}}
        <aside x-show="sidebarOpen" x-cloak
            @click.away="sidebarOpen = false"
            x-transition:enter="transition-transform ease-in-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-xl lg:hidden overflow-y-auto">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <span class="text-lg font-bold text-gray-800">Menú</span>
                <button @click="sidebarOpen = false" class="p-1 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <livewire:layout.sidebar />
        </aside>
        @endauth

        <div class="flex">
            @auth
            {{-- Desktop sidebar --}}
            <aside class="w-64 bg-white border-r border-gray-200 min-h-screen hidden lg:block shrink-0">
                <livewire:layout.sidebar />
            </aside>
            @endauth

            <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
                @if (isset($header))
                    <header class="bg-white shadow-sm rounded-lg mb-4 sm:mb-6 p-4">
                        {{ $header }}
                    </header>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
