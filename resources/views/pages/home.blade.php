<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create and trade unique AI-generated trading cards on {{ config('app.name', 'VaporPlay') }}. Join our community of digital collectors and artists.">
    <meta name="keywords" content="AI trading cards, digital collectibles, NFT, digital art, creative marketplace">

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="{{ config('app.name', 'VaporPlay') }} - AI Trading Card Creation Platform">
    <meta property="og:description" content="Create, collect, and trade unique AI-generated trading cards in our digital marketplace.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <title>{{ config('app.name', 'VaporPlay') }} - AI Trading Card Creation Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @layer utilities {
            .bg-grid {
                background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
                background-size: 16px 16px;
            }
            .text-balance {
                text-wrap: balance;
            }
            .parallax {
                transform: translateZ(0);
                will-change: transform;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-20px); }
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes scale {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            .animate-scale {
                animation: scale 8s ease-in-out infinite;
            }
            .snap-x {
                scroll-snap-type: x mandatory;
            }
            .snap-center {
                scroll-snap-align: center;
            }
        }
    </style>
</head>
<body 
    x-data="{ 
        scrolled: false,
        darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches
    }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.pageYOffset > 20 })"
    class="antialiased selection:bg-indigo-500 selection:text-white min-h-screen bg-[#FAFAFA] dark:bg-gray-900 dark:text-white"
>
    <!-- Background Pattern -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-grid opacity-50 dark:opacity-[0.02]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/80 to-white dark:via-gray-900/80 dark:to-gray-900"></div>
    </div>

    <!-- Navigation -->
    <nav 
        x-bind:class="{ 'bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl shadow-sm': scrolled, 'bg-transparent': !scrolled }"
        class="sticky top-0 z-50 w-full transition-all duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-2 group">
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 transition-all duration-500 group-hover:scale-110 group-hover:rotate-[360deg]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                            </svg>
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 transition-all duration-300">
                                {{ config('app.name', 'VaporPlay') }}
                            </span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('features') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:border-gray-500 transition-all duration-300">
                            Features
                        </a>
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:border-gray-500 transition-all duration-300">
                            Pricing
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    @auth
                        <a href="{{ route('images.gallery') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 dark:from-indigo-500 dark:to-purple-500 dark:hover:from-indigo-600 dark:hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 shadow-lg shadow-indigo-500/25 dark:shadow-indigo-800/30 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/40 dark:hover:shadow-indigo-800/40">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-full text-gray-700 dark:text-gray-200 bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl hover:bg-white dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-all duration-300">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 dark:from-indigo-500 dark:to-purple-500 dark:hover:from-indigo-600 dark:hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 shadow-lg shadow-indigo-500/25 dark:shadow-indigo-800/30 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/40 dark:hover:shadow-indigo-800/40">
                            Start Creating
                        </a>
                    @endauth
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button 
                        type="button" 
                        x-data="{ open: false }"
                        @click="open = !open"
                        x-on:click="$refs.mobileMenu.classList.toggle('hidden')"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition duration-300"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="hidden sm:hidden" x-ref="mobileMenu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('features') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 dark:hover:border-gray-500 transition-all duration-300">Features</a>
                <a href="{{ route('pricing') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 dark:hover:border-gray-500 transition-all duration-300">Pricing</a>
                @auth
                    <a href="{{ route('images.gallery') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium text-indigo-700 bg-indigo-50 dark:bg-gray-800 dark:text-indigo-400 transition-all duration-300">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 dark:hover:border-gray-500 transition-all duration-300">Log in</a>
                    <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium text-indigo-700 bg-indigo-50 dark:bg-gray-800 dark:text-indigo-400 transition-all duration-300">Start Creating</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-b from-indigo-50 to-white dark:from-gray-900 dark:to-gray-800"></div>
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-r from-indigo-400/30 to-purple-400/30 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-full blur-3xl opacity-50 animate-scale"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-r from-purple-400/30 to-pink-400/30 dark:from-purple-900/30 dark:to-pink-900/30 rounded-full blur-3xl opacity-50 animate-float"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left lg:flex lg:items-center">
                    <div>
                        <div class="inline-flex items-center space-x-2">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 shadow-lg shadow-indigo-500/25 dark:shadow-indigo-800/30">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 ring-1 ring-indigo-200 dark:ring-indigo-800 transition-all duration-300 hover:bg-indigo-200 dark:hover:bg-indigo-800">
                                <span class="animate-pulse mr-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 dark:bg-indigo-400"></span>
                                Beta Access Now Open
                            </span>
                        </div>

                        <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight text-balance">
                            <span class="block text-gray-900 dark:text-white font-medium">Create Unique</span>
                            <span class="mt-1 sm:mt-2 block">
                                <span class="inline bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400 bg-clip-text text-transparent font-bold">
                                    AI Trading Cards
                                </span>
                            </span>
                        </h1>

                        <p class="mt-6 text-lg leading-7 text-gray-600 dark:text-gray-300 text-balance">
                            Transform your imagination into collectible digital cards using advanced AI technology. Create, collect, and trade unique cards in our thriving marketplace.
                        </p>

                        <div class="mt-10 flex flex-col sm:flex-row sm:items-center gap-4">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 dark:from-indigo-500 dark:to-purple-500 dark:hover:from-indigo-600 dark:hover:to-purple-600 shadow-lg shadow-indigo-500/25 dark:shadow-indigo-800/30 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/40 dark:hover:shadow-indigo-800/40 hover:-translate-y-0.5">
                                Start Creating
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="{{ route('features') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-gray-700 dark:text-gray-200 bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl hover:bg-white dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:-translate-y-0.5">
                                Learn More
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-16 sm:mt-24 lg:mt-0 lg:col-span-6">
                    <div class="relative mx-auto w-full max-w-lg">
                        <!-- Card Stack -->
                        <div class="perspective-1000 hover:translate-y-1 transition-transform duration-300">
                            <div class="relative transform-style-3d animate-float">
                                <!-- Card 1 (Back) -->
                                <div class="absolute inset-0 w-full h-[420px] bg-gradient-to-br from-purple-600 to-pink-600 dark:from-purple-500 dark:to-pink-500 rounded-2xl shadow-2xl transform translate-z-20 rotate-6 opacity-75"></div>
                                
                                <!-- Card 2 (Middle) -->
                                <div class="absolute inset-0 w-full h-[420px] bg-gradient-to-br from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 rounded-2xl shadow-2xl transform translate-z-10 rotate-3 opacity-85"></div>
                                
                                <!-- Card 3 (Front) -->
                                <div class="relative w-full h-[420px] bg-gradient-to-br from-indigo-500 to-purple-500 dark:from-indigo-400 dark:to-purple-400 rounded-2xl shadow-2xl overflow-hidden hover:rotate-[-2deg] transition-transform duration-300">
                                    <!-- Card Content -->
                                    <div class="absolute inset-0">
                                        <!-- Shine Effect -->
                                        <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-50"></div>
                                        
                                        <!-- Card Frame -->
                                        <div class="absolute inset-2 rounded-xl border border-white/20 bg-black/20 backdrop-blur-sm">
                                            <!-- Card Art -->
                                            <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-400 dark:from-indigo-600 dark:to-purple-600 rounded-t-xl relative overflow-hidden">
                                                <div class="absolute inset-0 bg-[radial-gradient(circle_400px_at_50%_-100px,#fff2,transparent)]"></div>
                                            </div>
                                            
                                            <!-- Card Info -->
                                            <div class="p-4 text-white">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h3 class="text-lg font-bold">Mystic Creation</h3>
                                                    <div class="flex items-center space-x-1">
                                                        <span class="w-2 h-2 rounded-full bg-white/50"></span>
                                                        <span class="w-2 h-2 rounded-full bg-white/50"></span>
                                                        <span class="w-2 h-2 rounded-full bg-white/50"></span>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-white/80">Harness the power of AI to create unique, collectible digital cards that tell your story.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="relative py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Features</h2>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Everything you need to create</p>
                <p class="mt-6 text-lg leading-7 text-gray-600 dark:text-gray-300">
                    Powerful tools and features designed to help you create, collect, and trade unique AI-generated cards.
                </p>
            </div>

            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="group relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 group-hover:from-indigo-500 group-hover:to-purple-500 dark:group-hover:from-indigo-400 dark:group-hover:to-purple-400 shadow-lg shadow-indigo-500/25 dark:shadow-indigo-800/30 transition-all duration-300 group-hover:scale-110">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            AI-Powered Creation
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">
                            Transform your ideas into stunning trading cards with our advanced AI technology. Create unique artwork, card frames, and effects with ease.
                        </dd>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-500 dark:to-pink-500 group-hover:from-purple-500 group-hover:to-pink-500 dark:group-hover:from-purple-400 dark:group-hover:to-pink-400 shadow-lg shadow-purple-500/25 dark:shadow-purple-800/30 transition-all duration-300">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                            </div>
                            Digital Marketplace
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">
                            Buy, sell, and trade your unique cards in our secure marketplace. Set your own prices and build your collection.
                        </dd>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 dark:from-pink-500 dark:to-rose-500 group-hover:from-pink-500 group-hover:to-rose-500 dark:group-hover:from-pink-400 dark:group-hover:to-rose-400 shadow-lg shadow-pink-500/25 dark:shadow-pink-800/30 transition-all duration-300">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            Vibrant Community
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">
                            Connect with fellow creators and collectors. Share your creations, trade cards, and build lasting connections.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Community Section -->
    <div class="relative py-24 sm:py-32 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-50/80 to-white dark:from-gray-900/80 dark:to-gray-800/80"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_800px_at_50%_0%,#fff1,transparent)] dark:bg-[radial-gradient(circle_800px_at_50%_0%,#fff1,transparent)]"></div>
        </div>

        <div class="relative">
            <div class="lg:mx-auto lg:grid lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-2 lg:gap-24 lg:px-8">
                <div class="mx-auto max-w-xl px-6 lg:mx-0 lg:max-w-none lg:py-16 lg:px-0">
                    <div>
                        <div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="mt-6">
                            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Join Our Growing Community</h2>
                            <p class="mt-4 text-lg leading-7 text-gray-600 dark:text-gray-300">
                                Connect with creators and collectors who share your passion for digital art and trading cards. Learn from experts, share your creations, and build lasting connections.
                            </p>
                            <div class="mt-6 space-y-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 text-base text-gray-600 dark:text-gray-300">Active community of creators and collectors</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 text-base text-gray-600 dark:text-gray-300">Regular trading events and card showcases</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 text-base text-gray-600 dark:text-gray-300">Expert workshops and tutorials</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12 sm:mt-16 lg:mt-0">
                    <div class="relative pl-4 -mr-40 sm:pl-6 md:-mr-16 lg:relative lg:m-0 lg:h-full lg:px-0">
                        <div class="relative h-[500px] overflow-hidden rounded-2xl shadow-xl ring-1 ring-black/5">
                            <div class="absolute inset-0">
                                <div class="h-full w-full bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-600 dark:to-purple-700">
                                    <!-- Abstract Pattern -->
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_500px_at_50%_200px,#fff2,transparent)]"></div>
                                    <div class="absolute inset-0">
                                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/10 backdrop-blur-lg rounded-full"></div>
                                        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-purple-400/20 backdrop-blur-xl rounded-full animate-float"></div>
                                        <div class="absolute bottom-1/4 right-1/4 w-40 h-40 bg-indigo-400/20 backdrop-blur-xl rounded-full animate-float" style="animation-delay: -2s"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative isolate">
        <div class="absolute inset-0 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800"></div>
        <div class="relative">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:flex lg:items-center lg:gap-x-10 lg:px-8 lg:py-40">
                <div class="mx-auto max-w-2xl lg:mx-0 lg:flex-auto">
                    <div class="flex">
                        <div class="relative flex items-center gap-x-4 rounded-full px-4 py-1 text-sm leading-6 text-gray-600 dark:text-gray-300 ring-1 ring-gray-900/10 dark:ring-gray-700 hover:ring-gray-900/20 dark:hover:ring-gray-600">
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">What's New</span>
                            <span class="h-4 w-px bg-gray-900/10 dark:bg-gray-700"></span>
                            <a href="#" class="flex items-center gap-x-1">
                                Explore latest features
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <h2 class="mt-10 max-w-lg text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
                        Ready to start your journey?
                    </h2>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Join thousands of creators and collectors in our growing community. Start creating your unique AI-powered trading cards today.
                    </p>
                    <div class="mt-10 flex items-center gap-x-6">
                        <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center rounded-full py-3 px-6 text-base font-semibold text-white focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 dark:from-indigo-500 dark:to-purple-500 dark:hover:from-indigo-600 dark:hover:to-purple-600 shadow-lg shadow-indigo-500/25 dark:shadow-indigo-800/30 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/40 dark:hover:shadow-indigo-800/40 hover:-translate-y-0.5">
                            Get started
                            <svg class="ml-2.5 -mr-0.5 h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="{{ route('features') }}" class="text-base font-semibold leading-7 text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">
                            Learn more <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
                <div class="mt-16 sm:mt-24 lg:mt-0 lg:flex-shrink-0 lg:flex-grow">
                    <div class="relative mx-auto w-80 h-80">
                        <!-- Decorative Elements -->
                        <div class="absolute -inset-4">
                            <div class="w-full h-full mx-auto rotate-[30deg] transform-gpu blur-3xl" aria-hidden="true">
                                <div class="aspect-[1155/678] w-full h-full bg-gradient-to-br from-indigo-500/50 to-purple-500/50 dark:from-indigo-800/50 dark:to-purple-800/50 opacity-30"></div>
                            </div>
                        </div>
                        <!-- 3D Card Stack -->
                        <div class="relative perspective-1000">
                            <div class="w-full h-full transform-style-3d rotate-y-12 rotate-x-12">
                                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 shadow-xl transform translate-z-20 rotate-6"></div>
                                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 shadow-xl transform translate-z-10 rotate-3"></div>
                                <div class="relative rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-xl overflow-hidden">
                                    <div class="absolute inset-0 bg-grid opacity-10"></div>
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_400px_at_50%_-100px,#fff2,transparent)]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative mt-12 sm:mt-16">
        <div class="mx-auto max-w-7xl overflow-hidden px-6 py-12 sm:py-16 lg:px-8">
            <nav class="-mb-6 columns-2 sm:flex sm:justify-center sm:space-x-12" aria-label="Footer">
                <div class="pb-6">
                    <a href="#" class="text-sm leading-6 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-300">About</a>
                </div>
                <div class="pb-6">
                    <a href="#" class="text-sm leading-6 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-300">Blog</a>
                </div>
                <div class="pb-6">
                    <a href="#" class="text-sm leading-6 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-300">Terms</a>
                </div>
                <div class="pb-6">
                    <a href="#" class="text-sm leading-6 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-300">Privacy</a>
                </div>
            </nav>
            <div class="mt-10 flex justify-center space-x-10">
                <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-300">
                    <span class="sr-only">Twitter</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                    </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-300">
                    <span class="sr-only">GitHub</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
            <p class="mt-10 text-center text-xs leading-5 text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'VaporPlay') }}. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
