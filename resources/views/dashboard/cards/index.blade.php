@extends('layouts.cards')

@section('header')
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Card Collection</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
        @if($cards->isEmpty())
            <x-empty-state type="cards" />
        @else
            <!-- Enhanced Tabs -->
            <div class="bg-white sticky top-0 z-10 rounded-lg shadow-sm overflow-hidden">
                <div class="relative">
                    <!-- Background Effects -->
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-50 via-white to-gray-50 opacity-50"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,0.8),transparent_70%)] opacity-30"></div>
                    
                    <!-- Tab Navigation -->
                    <nav class="relative flex justify-center space-x-4 px-4" aria-label="Tabs">
                        <a href="{{ route('cards.index', ['tab' => 'all', 'view' => $currentView]) }}" 
                           class="tab-link group relative min-w-[120px] py-4 px-6
                                  {{ $currentTab === 'all' ? 'active' : '' }}">
                            <!-- Enhanced Background & Border Effects -->
                            <div class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                            <div class="indicator"></div>
                            
                            <!-- Tab Content -->
                            <div class="relative flex flex-col items-center">
                                <span class="text-sm font-medium {{ $currentTab === 'all' ? 'text-indigo-600' : 'text-gray-600 group-hover:text-gray-800' }}
                                           transition-colors duration-200">
                                    All Cards
                                </span>
                                @if($currentTab === 'all')
                                    <span class="mt-1 text-[10px] text-indigo-400 uppercase tracking-wider">Active</span>
                                @endif
                            </div>
                        </a>

                        <a href="{{ route('cards.index', ['tab' => 'newest', 'view' => $currentView]) }}"
                           class="tab-link group relative min-w-[120px] py-4 px-6
                                  {{ $currentTab === 'newest' ? 'active' : '' }}">
                            <!-- Enhanced Background & Border Effects -->
                            <div class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                            <div class="indicator"></div>
                            
                            <!-- Tab Content -->
                            <div class="relative flex flex-col items-center">
                                <span class="text-sm font-medium {{ $currentTab === 'newest' ? 'text-indigo-600' : 'text-gray-600 group-hover:text-gray-800' }}
                                           transition-colors duration-200">
                                    Newest
                                </span>
                                @if($currentTab === 'newest')
                                    <span class="mt-1 text-[10px] text-indigo-400 uppercase tracking-wider">Active</span>
                                @endif
                            </div>
                        </a>
                    </nav>
                </div>
                
                <!-- Collection Header -->
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $currentTab === 'newest' ? 'Recently Added' : 'Your Collection' }}
                        </h2>
                        <span class="text-sm text-gray-600" id="cardCount">
                            {{ $cards->total() }} cards
                        </span>
                    </div>
                </div>
            </div>

            <!-- View Controls -->
            <div class="flex justify-end mb-4 space-x-2">
                <button class="view-btn p-2 rounded-lg hover:bg-gray-100 transition-colors {{ $currentView === 'grid' ? 'bg-gray-100' : '' }}" 
                        data-view="grid">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button class="view-btn p-2 rounded-lg hover:bg-gray-100 transition-colors {{ $currentView === 'list' ? 'bg-gray-100' : '' }}" 
                        data-view="list">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Card Views Container -->
            <div class="relative bg-gradient-to-br from-gray-50 via-white to-gray-50 p-6 rounded-xl
                        shadow-[inset_0_1px_2px_rgba(0,0,0,0.05)]
                        before:absolute before:inset-0 before:bg-[radial-gradient(circle_at_50%_0,rgba(255,255,255,0.8),transparent_50%)]
                        before:pointer-events-none before:opacity-70
                        after:absolute after:inset-0 after:bg-[radial-gradient(circle_at_50%_100%,rgba(0,0,0,0.05),transparent_50%)]
                        after:pointer-events-none after:opacity-50">
                
                <!-- Grid View -->
                <div class="w-full">
                    <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8 relative opacity-0" 
                         x-data="{ hoveredCard: null }">
                        @foreach($cards as $card)
                            <x-card-grid-item :card="$card" />
                        @endforeach
                    </div>
                </div>

                <!-- List View -->
                <div id="listView" class="hidden max-w-6xl mx-auto opacity-0" style="transition: all 0.5s ease-out">
                    <div class="space-y-4 flex flex-col w-full">
                        @foreach($cards as $card)
                            <x-card-list-item :card="$card" class="w-full" />
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Enhanced Pagination -->
            <div class="mt-8 pb-6">
                <div class="pagination-container">
                    {{ $cards->appends(['tab' => $currentTab, 'view' => $currentView])->links() }}
                </div>
            </div>

            @push('styles')
            <style>
                /* Pagination Styling */
                .pagination-container nav {
                    @apply flex justify-center;
                }

                .pagination-container .relative.z-0 {
                    @apply inline-flex rounded-md shadow-sm -space-x-px overflow-hidden;
                    background: linear-gradient(to bottom, 
                        rgba(255, 255, 255, 0.1),
                        rgba(255, 255, 255, 0.05)
                    );
                }

                .pagination-container .relative.z-0 > span,
                .pagination-container .relative.z-0 > a {
                    @apply relative inline-flex items-center px-4 py-2 text-sm font-medium;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    background: linear-gradient(to bottom,
                        rgba(255, 255, 255, 0.1),
                        rgba(255, 255, 255, 0.05)
                    );
                }

                .pagination-container span[aria-current="page"] > span {
                    @apply z-10 text-indigo-600 relative;
                    background: linear-gradient(to bottom,
                        rgba(99, 102, 241, 0.15),
                        rgba(99, 102, 241, 0.05)
                    );
                    box-shadow: 
                        inset 0 1px 0 rgba(255, 255, 255, 0.2),
                        inset 0 -1px 0 rgba(0, 0, 0, 0.1),
                        0 0 15px rgba(99, 102, 241, 0.1);
                }

                .pagination-container a:hover {
                    @apply text-gray-700 relative;
                    background: linear-gradient(to bottom,
                        rgba(99, 102, 241, 0.05),
                        rgba(99, 102, 241, 0.02)
                    );
                    transform: translateY(-1px);
                }

                .pagination-container span[aria-disabled="true"] > span,
                .pagination-container a {
                    @apply text-gray-500 hover:bg-gray-50 relative;
                }

                /* Metallic border effect */
                .pagination-container .relative.z-0::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(45deg,
                        transparent 0%,
                        rgba(255, 255, 255, 0.1) 45%,
                        rgba(255, 255, 255, 0.2) 50%,
                        rgba(255, 255, 255, 0.1) 55%,
                        transparent 100%
                    );
                    opacity: 0.5;
                    pointer-events: none;
                }

                /* Active page shine animation */
                @keyframes paginationShine {
                    0% { background-position: -200% 0; }
                    100% { background-position: 200% 0; }
                }

                .pagination-container span[aria-current="page"] > span::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(90deg,
                        transparent,
                        rgba(255, 255, 255, 0.2),
                        transparent
                    );
                    background-size: 200% 100%;
                    animation: paginationShine 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
                    pointer-events: none;
                }
            </style>
            @endpush
        @endif
    </div>
</div>

<!-- Modals -->
<x-card-details-modal />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize view switching
        const viewButtons = document.querySelectorAll('.view-btn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        let currentView = 'grid';

        // Function to update URL with current view
        const updateURL = (view) => {
            const url = new URL(window.location);
            url.searchParams.set('view', view);
            window.history.pushState({}, '', url);
        };

        // Function to switch views
        const switchView = (view, animate = true) => {
            currentView = view;
            updateURL(view);
            
            // Update button states
            viewButtons.forEach(btn => {
                const isActive = btn.getAttribute('data-view') === view;
                btn.classList.toggle('bg-gray-100', isActive);
            });

            if (view === 'grid') {
                gridView.style.opacity = '1';
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                
                if (animate) {
                    // Show and animate grid cards with stagger
                    const cardItems = gridView.querySelectorAll('.card-item');
                    cardItems.forEach((card, index) => {
                        setTimeout(() => {
                            card.classList.add('visible');
                        }, index * 100);
                    });
                } else {
                    // Show cards immediately without animation
                    gridView.querySelectorAll('.card-item').forEach(card => {
                        card.classList.add('visible');
                    });
                }

                // Initialize 3D effects
                setTimeout(() => {
                    const cards = document.querySelectorAll('.card-container');
                    cards.forEach(card => {
                        new MTGCard3DTiltEffect(card);
                    });
                }, animate ? 500 : 0);
            } else {
                listView.style.opacity = '1';
                listView.classList.remove('hidden');
                gridView.classList.add('hidden');
                
                const listItems = listView.querySelectorAll('.card-item');
                listItems.forEach(item => {
                    item.style.display = 'flex';
                    if (!animate) item.classList.add('visible');
                });
            }
        };

        // Set up view button listeners
        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const view = btn.getAttribute('data-view');
                switchView(view, true);
            });
        });

        // Update pagination links to maintain view parameter
        document.querySelectorAll('.pagination-container a').forEach(link => {
            const url = new URL(link.href);
            url.searchParams.set('view', currentView);
            link.href = url.toString();
        });

        // Initialize view from URL parameter or default
        const urlParams = new URLSearchParams(window.location.search);
        const initialView = urlParams.get('view') || '{{ $currentView }}';
        setTimeout(() => switchView(initialView, false), 150);
    });
</script>
@endpush

@push('styles')
<style>
    /* Enhanced Tab Styling */
    .tab-link {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        background: linear-gradient(to bottom, 
            rgba(255, 255, 255, 0.1),
            rgba(255, 255, 255, 0.05)
        );
        box-shadow: 
            inset 0 1px 0 rgba(255, 255, 255, 0.1),
            inset 0 -1px 0 rgba(0, 0, 0, 0.05);
    }

    /* Metallic Border Effect */
    .tab-link::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            linear-gradient(45deg,
                transparent 0%,
                rgba(255, 255, 255, 0.1) 45%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0.1) 55%,
                transparent 100%
            );
        opacity: 0;
        transition: all 0.3s ease;
    }

    /* Mana Symbol Pattern */
    .tab-link::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12 2L14.5 9H22L16 13.5L18.5 20.5L12 16L5.5 20.5L8 13.5L2 9H9.5L12 2Z' fill='rgba(99,102,241,0.03)'/%3E%3C/svg%3E");
        background-size: 24px 24px;
        opacity: 0;
        transition: opacity 0.5s ease;
        animation: rotate-slow 60s linear infinite;
    }

    /* Active & Hover States */
    .tab-link:hover::before,
    .tab-link.active::before {
        opacity: 1;
    }

    .tab-link:hover::after,
    .tab-link.active::after {
        opacity: 1;
    }

    /* Bottom Indicator */
    .tab-link .indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(to right,
            transparent,
            rgba(99, 102, 241, 0.2) 20%,
            rgba(99, 102, 241, 0.8) 50%,
            rgba(99, 102, 241, 0.2) 80%,
            transparent
        );
        transform: scaleX(0);
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 0 2px rgba(99, 102, 241, 0.5));
    }

    .tab-link.active .indicator {
        transform: scaleX(1);
    }

    /* Active State Enhancement */
    .tab-link.active {
        background: linear-gradient(to bottom,
            rgba(99, 102, 241, 0.15),
            rgba(99, 102, 241, 0.05)
        );
        box-shadow: 
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            inset 0 -1px 0 rgba(0, 0, 0, 0.1),
            0 0 15px rgba(99, 102, 241, 0.1);
    }

    /* Shine Animation */
    @keyframes tabShine {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .tab-link.active::before {
        background: linear-gradient(90deg,
            transparent,
            rgba(255, 255, 255, 0.3),
            transparent
        );
        background-size: 200% 100%;
        animation: tabShine 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    /* Hover Enhancement */
    .tab-link:hover:not(.active) {
        background: linear-gradient(to bottom,
            rgba(99, 102, 241, 0.05),
            rgba(99, 102, 241, 0.02)
        );
        transform: translateY(-1px);
    }

    /* Card Container */
    .card-container {
        height: 100%;
        transform-style: preserve-3d;
        backface-visibility: hidden;
    }

    /* Card Item */
    .card-item {
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.5s ease-out;
    }

    .card-item.visible {
        transform: translateY(0);
        opacity: 1;
    }

    #listView .card-item {
        width: 100%;
        margin-bottom: 8px;
    }

    /* Grid Layout */
    #gridView {
        display: grid;
        width: 100%;
    }

    #gridView > * {
        width: 100%;
        min-height: 400px;
    }

    @media (max-width: 640px) {
        #gridView {
            grid-template-columns: 1fr;
        }
    }

    @media (min-width: 641px) and (max-width: 1024px) {
        #gridView {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1025px) {
        #gridView {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .card-container {
        aspect-ratio: 2.5/3.5;
    }

    /* Animations */
    @keyframes fadeInScale {
        0% {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    @keyframes rotate-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush
