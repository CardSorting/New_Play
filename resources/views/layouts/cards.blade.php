<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Card Fonts -->
    <style>
        @font-face {
            font-family: 'Beleren';
            src: url('/fonts/Beleren-Bold.woff2') format('woff2');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Matrix';
            src: url('/fonts/Matrix-Regular.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'MPlantin';
            src: url('/fonts/MPlantin-Italic.woff2') format('woff2');
            font-weight: normal;
            font-style: italic;
            font-display: swap;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        @include('layouts.navigation')

        <!-- Page Header -->
        @yield('header')

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    
    <!-- Card 3D Effect -->
    <script src="{{ asset('js/mtg-card-3d-effect.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const initializeCardEffects = () => {
                document.querySelectorAll('.mtg-card').forEach(card => {
                    new MTGCard3DTiltEffect(card);
                });
            };

            // Initialize on page load
            initializeCardEffects();

            // Re-initialize when Livewire updates the DOM
            document.addEventListener('livewire:load', initializeCardEffects);
            document.addEventListener('livewire:update', initializeCardEffects);
        });
    </script>
</body>
</html>
