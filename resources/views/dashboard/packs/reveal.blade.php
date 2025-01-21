<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Opening Pack: {{ $pack->name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $pack->cards->count() }} cards
            </p>
        </div>
    </x-slot>

    <div class="min-h-screen py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-xl">
                <!-- Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 dark:from-blue-500/10 dark:to-purple-500/10"></div>
                
                <div class="relative p-4 sm:p-6 lg:p-8">
                    <div id="reveal-container" class="flex flex-col items-center space-y-8 sm:space-y-12">
                        <!-- Loading State -->
                        <div id="loading-state" class="hidden w-full text-center py-12">
                            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-indigo-500 transition ease-in-out duration-150 cursor-not-allowed">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Preparing your cards...
                            </div>
                        </div>

                        <!-- Cards Container -->
                        <div id="cards-container" 
                             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 w-full max-w-6xl mx-auto"
                             aria-live="polite">
                            @foreach($pack->cards as $card)
                                <div class="card-wrapper relative aspect-[2.5/3.5] group" 
                                     data-rarity="{{ $card->rarity }}" 
                                     style="display: none;"
                                     role="img"
                                     aria-label="Magic card - {{ $card->name }}">
                                    
                                    <!-- Card Container with 3D effect -->
                                    <div class="card relative w-full h-full transform transition-all duration-700 preserve-3d will-change-transform motion-reduce:transition-none">
                                        <!-- Card Back -->
                                        <div class="card-back absolute inset-0 bg-cover bg-center rounded-xl shadow-2xl backface-hidden ring-1 ring-black/5 dark:ring-white/5"
                                             style="background-image: url('/img/card-back.jpg');">
                                            <!-- Shine Effect -->
                                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        </div>
                                        
                                        <!-- Card Front -->
                                        <div class="card-front absolute inset-0 bg-cover bg-center rounded-xl shadow-2xl transform rotate-y-180 backface-hidden ring-1 ring-black/5 dark:ring-white/5"
                                             style="background-image: url('{{ $card->image_url }}');">
                                            <!-- Shine Effect -->
                                            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            
                                            <!-- Rarity Indicator -->
                                            @if($card->rarity === 'mythic')
                                                <div class="absolute top-2 right-2 w-3 h-3 rounded-full bg-orange-500 animate-pulse"></div>
                                            @elseif($card->rarity === 'rare')
                                                <div class="absolute top-2 right-2 w-3 h-3 rounded-full bg-yellow-400"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Controls -->
                        <div class="flex flex-col items-center space-y-6 mt-8 sm:mt-12">
                            <button id="reveal-btn" 
                                    class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-lg font-medium rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transform transition-all duration-200 hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                    aria-label="Reveal all cards in pack">
                                <span class="relative flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Reveal Cards
                                </span>
                                <!-- Button Shine -->
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-white/0 via-white/20 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shine"></div>
                            </button>
                            
                            <form id="complete-form" action="{{ route('packs.complete-open', $pack) }}" method="POST" class="hidden">
                                @csrf
                                <button type="submit"
                                        class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-500 text-white text-lg font-medium rounded-xl shadow-lg hover:from-green-700 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transform transition-all duration-200 hover:scale-105 active:scale-95"
                                        aria-label="Add revealed cards to your collection">
                                    <span class="relative flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add to Collection
                                    </span>
                                    <!-- Button Shine -->
                                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-white/0 via-white/20 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shine"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <audio id="card-flip-sound" preload="auto">
        <source src="{{ asset('audio/card-flip.mp3') }}" type="audio/mpeg">
    </audio>

    @push('scripts')
    <script src="{{ asset('js/mtg-card-3d-effect.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cardSound = document.getElementById('card-flip-sound');
            const revealBtn = document.getElementById('reveal-btn');
            const completeForm = document.getElementById('complete-form');
            const cardWrappers = document.querySelectorAll('.card-wrapper');
            const loadingState = document.getElementById('loading-state');
            const cardsContainer = document.getElementById('cards-container');
            
            // Check for reduced motion preference
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            // Initialize 3D effect on all cards
            document.querySelectorAll('.card').forEach(card => {
                initializeMTGCard3DEffect(card);
            });

            let isRevealing = false;

            async function revealCards() {
                if (isRevealing) return;
                isRevealing = true;

                try {
                    // Update button state
                    revealBtn.disabled = true;
                    revealBtn.classList.add('cursor-not-allowed', 'opacity-50');

                    // Show loading state
                    loadingState.classList.remove('hidden');
                    cardsContainer.setAttribute('aria-busy', 'true');

                    // Initial shake animation for suspense
                    if (!prefersReducedMotion) {
                        cardsContainer.classList.add('animate-shake');
                        await new Promise(resolve => setTimeout(resolve, 500));
                        cardsContainer.classList.remove('animate-shake');
                    }

                    // Hide reveal button
                    revealBtn.classList.add('hidden');

                    // Reveal each card
                    for (let i = 0; i < cardWrappers.length; i++) {
                        const wrapper = cardWrappers[i];
                        const card = wrapper.querySelector('.card');
                        const rarity = wrapper.dataset.rarity;
                        const cardName = wrapper.getAttribute('aria-label');

                        // Show card
                        wrapper.style.display = 'block';
                        
                        // Announce card reveal for screen readers
                        cardsContainer.setAttribute('aria-label', `Revealing ${cardName}`);
                        
                        // Add entrance animation
                        if (!prefersReducedMotion) {
                            wrapper.classList.add('animate-slide-up');
                            await new Promise(resolve => setTimeout(resolve, 300));
                            
                            // Play flip sound if available
                            if (cardSound.readyState >= 2) {
                                cardSound.currentTime = 0;
                                cardSound.play().catch(() => {}); // Ignore autoplay restrictions
                            }
                        }

                        // Flip card
                        card.classList.add('rotate-y-180');

                        // Add rarity effects
                        if ((rarity === 'rare' || rarity === 'mythic') && !prefersReducedMotion) {
                            wrapper.classList.add('animate-pulse');
                            createParticleEffect(wrapper);
                        }

                        // Wait between cards unless reduced motion is preferred
                        if (!prefersReducedMotion) {
                            await new Promise(resolve => setTimeout(resolve, 800));
                        }
                    }

                    // Hide loading state
                    loadingState.classList.add('hidden');
                    cardsContainer.removeAttribute('aria-busy');
                    cardsContainer.setAttribute('aria-label', 'All cards revealed');

                    // Show complete button with animation
                    completeForm.classList.remove('hidden');
                    if (!prefersReducedMotion) {
                        completeForm.classList.add('animate-slide-up');
                    }
                } catch (error) {
                    console.error('Error during card reveal:', error);
                    // Reset UI state on error
                    revealBtn.disabled = false;
                    revealBtn.classList.remove('cursor-not-allowed', 'opacity-50', 'hidden');
                    loadingState.classList.add('hidden');
                } finally {
                    isRevealing = false;
                }
            }

            // Event Listeners
            revealBtn.addEventListener('click', revealCards);

            function createParticleEffect(element) {
                if (prefersReducedMotion) return;

                const particles = 15;
                const colors = ['#fbbf24', '#f59e0b', '#d97706']; // Yellow variants for variety

                for (let i = 0; i < particles; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'absolute w-2 h-2 rounded-full';
                    particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    
                    const angle = (i / particles) * Math.PI * 2;
                    const velocity = 2 + Math.random() * 2;
                    const startX = element.offsetWidth / 2;
                    const startY = element.offsetHeight / 2;
                    
                    particle.style.left = startX + 'px';
                    particle.style.top = startY + 'px';
                    
                    element.appendChild(particle);
                    
                    const animation = particle.animate([
                        { 
                            transform: 'translate(0, 0) scale(1)', 
                            opacity: 1 
                        },
                        { 
                            transform: `translate(${Math.cos(angle) * 100 * velocity}px, 
                                      ${Math.sin(angle) * 100 * velocity}px) scale(0)`,
                            opacity: 0 
                        }
                    ], {
                        duration: 1000,
                        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                        fill: 'forwards'
                    });
                    
                    animation.onfinish = () => particle.remove();
                }
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .preserve-3d {
            transform-style: preserve-3d;
        }
        .rotate-y-180 {
            transform: rotateY(180deg);
        }
        .backface-hidden {
            backface-visibility: hidden;
        }
        @keyframes shine {
            from {
                transform: translateX(-100%) skewX(-15deg);
            }
            to {
                transform: translateX(200%) skewX(-15deg);
            }
        }
        .animate-shine {
            animation: shine 1.5s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-1deg); }
            75% { transform: rotate(1deg); }
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
        }

        /* Accessibility: Respect reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {
            .animate-shine,
            .animate-float,
            .animate-shake,
            .animate-slide-up {
                animation: none !important;
            }
            .transition-all {
                transition: none !important;
            }
        }

        /* Dark mode enhancements */
        .dark .card-wrapper::before {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(to right, #4f46e5, #9333ea);
            border-radius: inherit;
            z-index: -1;
            opacity: 0.1;
        }
    </style>
    @endpush

    @push('scripts')
</x-dashboard-layout>
