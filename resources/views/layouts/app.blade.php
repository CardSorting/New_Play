<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @stack('meta')

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css'])
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <!-- Session Messages -->
                @if(session('success'))
                    <div class="fixed top-4 right-4 z-50">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="fixed top-4 right-4 z-50">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                
                {{ $slot }}
            </main>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Initialize Livewire -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.livewire) {
                    window.livewire.on('cardUpdated', () => {
                        // Reinitialize 3D effects after Livewire updates
                        if (typeof initializeCardEffects === 'function') {
                            initializeCardEffects();
                        }
                    });
                }
            });
        </script>

        <!-- App Scripts -->
        @vite(['resources/js/app.js'])
        
        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>
