<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Magic Card') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Preview Container -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mt-8">
                        <div id="card-container" class="mtg-card w-[375px] h-[525px] mx-auto relative rounded-[18px] shadow-lg overflow-hidden bg-white">
                            <div class="card-frame h-full flex flex-col bg-[#f8e7c9] border-8 border-[#171314] rounded-lg overflow-hidden relative">
                                <!-- Card Frame Texture -->
                                <div class="absolute inset-0 mix-blend-overlay opacity-30 pointer-events-none" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1IiBoZWlnaHQ9IjUiPgo8cmVjdCB3aWR0aD0iNSIgaGVpZ2h0PSI1IiBmaWxsPSIjZmZmIj48L3JlY3Q+CjxwYXRoIGQ9Ik0wIDVMNSAwWk02IDRMNCA2Wk0tMSAxTDEgLTFaIiBzdHJva2U9IiMyOTI1MjQiIHN0cm9rZS1vcGFjaXR5PSIwLjA1Ij48L3BhdGg+Cjwvc3ZnPg==');"></div>
                                <!-- Header: Card Name and Mana Cost -->
                                <!-- Title Bar with Enhanced Styling -->
                                <div class="card-title-bar relative flex justify-between items-center px-3 py-2 bg-[#171314] text-[#d3ced9]">
                                    <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                                    <h2 class="card-name text-xl font-beleren font-bold text-shadow text-[#d3ced9] relative z-10">Unnamed Card</h2>
                                    <div class="mana-cost flex space-x-1">
                                        <div class="mana-symbol text-gray-500">No Mana Cost</div>
                                    </div>
                                </div>

                                <!-- Art Box -->
                                <div class="relative mx-2 mt-2 mb-2 overflow-hidden group h-[45%]">
                                    <div class="absolute inset-0 border border-[#171314] z-20 pointer-events-none"></div>
                                    <img src="{{ $image->image_url }}" alt="Card Image" class="w-full h-[180px] object-cover object-center">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-white/10"></div>
                                </div>

                                <!-- Type Line with Enhanced Border -->
                                <div class="card-type relative mx-2 mb-2">
                                    <div class="absolute inset-0 bg-[#171314]"></div>
                                    <div class="relative px-3 py-1 text-sm font-matrix bg-[#f8e7c9] text-[#171314] tracking-wide border-t border-b border-[#171314]">
                                        Unknown Type
                                    </div>
                                </div>

                                <!-- Text Box -->
                                <div class="card-text relative mx-2 h-[30%] bg-[#f8e7c9] overflow-hidden border border-[#171314] text-[#171314]">
                                    <div class="absolute inset-0 bg-gradient-to-br from-white/20 via-transparent to-black/5"></div>
                                    <div class="absolute inset-0 border border-white/10"></div>
                                    <div class="relative p-4 flex flex-col h-full">
                                        <div class="flex-grow overflow-y-auto space-y-2 scrollbar-thin scrollbar-thumb-[#171314]/20 scrollbar-track-transparent">
                                            <p class="abilities-text mb-2 font-matrix text-black">No abilities</p>
                                            <p class="flavor-text mt-2 font-mplantin italic text-black">No flavor text</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Line -->
                                <div class="card-footer relative flex justify-between items-center mt-2 mx-2 mb-2 px-3 py-1.5 bg-[#171314] text-[#d3ced9] text-xs font-matrix tracking-wide">
                                    <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                                    <div class="relative flex justify-between items-center w-full z-10">
                                        <span class="rarity-details transition-all duration-500">???</span>
                                        <span class="power-toughness">N/A</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    @if($cardExists)
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <p class="font-medium">Card Already Exists</p>
                            <p class="mt-1 text-sm">You have already created a card for this image. Each image can only be used for one card.</p>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('cards.store') }}" class="space-y-6" data-ajax="true">
                    @csrf
                    <input type="hidden" name="image_id" value="{{ $image->id }}">
                    <input type="hidden" name="image_url" value="{{ $image->image_url }}">
                    
                    <!-- Card Name -->
                    <div>
                        <x-input-label for="name" value="Card Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Mana Cost -->
                    <div>
                        <x-input-label for="mana_cost" value="Mana Cost (e.g., 2RG for 2 colorless, 1 red, 1 green)" />
                        <x-text-input id="mana_cost" name="mana_cost" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('mana_cost')" class="mt-2" />
                    </div>

                    <!-- Card Type -->
                    <div>
                        <x-input-label for="card_type" value="Card Type" />
                        <x-text-input id="card_type" name="card_type" type="text" class="mt-1 block w-full" required placeholder="e.g., Creature - Dragon" />
                        <x-input-error :messages="$errors->get('card_type')" class="mt-2" />
                    </div>

                    <!-- Card Text/Abilities -->
                    <div>
                        <x-input-label for="abilities" value="Card Text/Abilities" />
                        <textarea id="abilities" name="abilities" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                        <x-input-error :messages="$errors->get('abilities')" class="mt-2" />
                    </div>

                    <!-- Flavor Text -->
                    <div>
                        <x-input-label for="flavor_text" value="Flavor Text (optional)" />
                        <textarea id="flavor_text" name="flavor_text" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                        <x-input-error :messages="$errors->get('flavor_text')" class="mt-2" />
                    </div>

                    <!-- Power/Toughness (for creatures) -->
                    <div>
                        <x-input-label for="power_toughness" value="Power/Toughness (for creatures, e.g. 3/4)" />
                        <x-text-input id="power_toughness" name="power_toughness" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('power_toughness')" class="mt-2" />
                    </div>

                    <!-- Rarity Info -->
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Card rarity will be randomly assigned upon creation with the following probabilities:</p>
                        <ul class="mt-2 text-sm text-gray-600 list-disc list-inside">
                            <li>Common: 50%</li>
                            <li>Uncommon: 30%</li>
                            <li>Rare: 15%</li>
                            <li>Mythic Rare: 5%</li>
                        </ul>
                    </div>

                    <div class="flex justify-end mt-6">
                        @if($cardExists)
                            <x-primary-button disabled class="opacity-50 cursor-not-allowed bg-gray-400">
                                {{ __('Card Already Created') }}
                            </x-primary-button>
                        @else
                            <x-primary-button class="!text-white !bg-gray-800 hover:!bg-gray-700">
                                {{ __('Create Card') }}
                            </x-primary-button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Card Fonts */
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

        .font-beleren {
            font-family: 'Beleren', ui-serif, Georgia, Cambria, serif;
        }

        .font-matrix {
            font-family: 'Matrix', ui-serif, Georgia, Cambria, serif;
        }

        .font-mplantin {
            font-family: 'MPlantin', ui-serif, Georgia, Cambria, serif;
        }

        /* Card Frame Elements */
        .card-frame {
            background-color: #f4e6c7;
            border: 12px solid #171314;
            box-shadow: 
                inset 0 0 0 1px rgba(255, 255, 255, 0.1),
                0 0 15px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
        }

        /* Enhanced Card Layout */
        .card-header {
            flex: 0 0 auto;
            height: 12%;
            min-height: 2.5rem;
            border-bottom: 2px solid #171314;
        }

        .card-art {
            flex: 0 0 45%;
            position: relative;
            margin: 0.75rem;
            border: 4px solid #171314;
            border-radius: 0.375rem;
        }

        .card-type {
            flex: 0 0 auto;
            height: 8%;
            min-height: 2rem;
            margin: 0.75rem;
        }

        .card-text {
            flex: 1 1 auto;
            min-height: 25%;
            margin: 0.75rem;
            border: 2px solid #171314;
            border-radius: 0.375rem;
            background-color: #f4e6c7;
        }

        .card-footer {
            flex: 0 0 auto;
            height: 10%;
            min-height: 2rem;
            margin: 0.75rem;
            border-radius: 0.375rem;
            background-color: #171314;
        }

        @keyframes flipInY {
            from {
                transform: perspective(400px) rotateY(90deg);
                animation-timing-function: ease-in;
                opacity: 0;
            }
            40% {
                transform: perspective(400px) rotateY(-20deg);
                animation-timing-function: ease-in;
            }
            60% {
                transform: perspective(400px) rotateY(10deg);
                opacity: 1;
            }
            80% {
                transform: perspective(400px) rotateY(-5deg);
            }
            to {
                transform: perspective(400px);
            }
        }

        @keyframes glowPulse {
            0% { box-shadow: 0 0 5px var(--glow-color); }
            50% { box-shadow: 0 0 20px var(--glow-color); }
            100% { box-shadow: 0 0 5px var(--glow-color); }
        }

        .rarity-reveal {
            animation: flipInY 1s ease-out;
        }

        .rarity-common {
            --glow-color: rgba(255, 255, 255, 0.5);
        }

        .rarity-uncommon {
            --glow-color: rgba(192, 192, 192, 0.7);
            color: #c0c0c0 !important;
        }

        .rarity-rare {
            --glow-color: rgba(255, 215, 0, 0.7);
            color: #ffd700 !important;
        }

        .rarity-mythic {
            --glow-color: rgba(255, 69, 0, 0.7);
            color: #ff4500 !important;
        }

        .glow-effect {
            animation: glowPulse 2s infinite;
        }

        .card-container-reveal {
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const cardContainer = document.getElementById('card-container');
            const formFields = document.querySelectorAll('input, textarea, select');
            
            // Live preview update function
            function updatePreview() {
                const name = document.getElementById('name').value || 'Unnamed Card';
                const manaCost = document.getElementById('mana_cost').value;
                const manaContainer = document.querySelector('.mana-cost');
                const cardType = document.getElementById('card_type').value || 'Unknown Type';
                const abilities = document.getElementById('abilities').value || 'No abilities';
                const flavorText = document.getElementById('flavor_text').value;
                const powerToughness = document.getElementById('power_toughness').value || 'N/A';

                // Update card name with preserved class
                const cardNameEl = document.querySelector('.card-name');
                cardNameEl.textContent = name;
                cardNameEl.className = 'card-name text-xl font-beleren font-bold text-shadow text-[#d3ced9] relative z-10';

                // Update mana cost
                if (!manaCost) {
                    manaContainer.innerHTML = '<div class="mana-symbol text-gray-500">No Mana Cost</div>';
                } else {
                    // Split mana cost string into individual symbols, handling both comma-separated and direct string
                    const symbols = manaCost.includes(',') ? manaCost.split(',') : manaCost.split('');
                    manaContainer.innerHTML = symbols.map(symbol => {
                        const bgColor = {
                            'W': 'bg-yellow-200 text-black',
                            'U': 'bg-blue-500 text-white',
                            'B': 'bg-black text-white',
                            'R': 'bg-red-500 text-white',
                            'G': 'bg-green-500 text-white'
                        }[symbol.toUpperCase()] || 'bg-gray-400 text-black';
                        
                        return `<div class="mana-symbol rounded-full flex justify-center items-center text-sm font-bold w-8 h-8 ${bgColor} border border-[#171314]">${symbol}</div>`;
                    }).join('');
                }

                // Update card type
                const cardTypeContainer = document.querySelector('.card-type');
                const cardTypeInner = cardTypeContainer.querySelector('div:last-child');
                cardTypeInner.textContent = cardType;

                // Update abilities and flavor text with preserved classes
                const abilitiesEl = document.querySelector('.abilities-text');
                abilitiesEl.textContent = abilities;
                abilitiesEl.className = 'abilities-text mb-2 font-matrix text-black';

                const flavorTextEl = document.querySelector('.flavor-text');
                flavorTextEl.textContent = flavorText || 'No flavor text';
                flavorTextEl.className = 'flavor-text mt-2 font-mplantin italic text-black';

                // Update footer (only power/toughness since rarity is random)
                document.querySelector('.rarity-details').textContent = '???';
                document.querySelector('.power-toughness').textContent = powerToughness;
            }
            
            // Initialize form fields and preview
            function initializeForm() {
                // Add event listeners to all form fields for live preview
                formFields.forEach(field => {
                    field.addEventListener('input', updatePreview);
                });
                
                // Initial preview update
                updatePreview();
            }
            
            // Handle both AJAX and regular form submissions
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating...</span>';
                
                // If AJAX is not needed, let the form submit normally
                if (!form.dataset.ajax) {
                    return true;
                }
                
                e.preventDefault();
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: new FormData(form)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.card) {
                        // Trigger animations
                        cardContainer.classList.add('card-container-reveal');
                        
                        setTimeout(() => {
                            const raritySpan = document.querySelector('.rarity-details');
                            raritySpan.textContent = data.card.rarity;
                            raritySpan.classList.add('rarity-reveal');
                            
                            const rarityClass = `rarity-${data.card.rarity.toLowerCase().replace(' ', '-')}`;
                            cardContainer.classList.add(rarityClass, 'glow-effect');
                            
                            setTimeout(() => {
                                window.location.href = data.redirect || '{{ route('cards.index') }}';
                            }, 2000);
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Create Card';
                    
                    // Clear previous errors
                    const existingErrors = form.querySelectorAll('.ajax-error');
                    existingErrors.forEach(el => el.remove());
                    
                    // Store errors in session storage
                    if (error.errors) {
                        sessionStorage.setItem('formErrors', JSON.stringify(error.errors));
                        
                        // Display all validation errors
                        Object.entries(error.errors).forEach(([field, messages]) => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'ajax-error mt-2 text-sm text-red-600';
                                errorDiv.textContent = messages.join(' ');
                                input.parentElement.appendChild(errorDiv);
                                
                                // Add error class to input
                                input.classList.add('border-red-500');
                                input.classList.remove('border-gray-300');
                            }
                        });
                    } else {
                        // Display generic error
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'ajax-error mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                        errorDiv.textContent = error.message || 'An error occurred while creating the card. Please try again.';
                        form.insertBefore(errorDiv, submitButton.parentElement);
                    }
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.ajax-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    // If no errors were found but submission failed, show a generic error
                    if (!form.querySelector('.ajax-error')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'ajax-error mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                        errorDiv.textContent = 'An unexpected error occurred. Please try again.';
                        form.insertBefore(errorDiv, submitButton.parentElement);
                    }
                });
            });

            // Restore form state on page load
            function restoreFormState() {
                const storedErrors = sessionStorage.getItem('formErrors');
                if (storedErrors) {
                    const errors = JSON.parse(storedErrors);
                    Object.entries(errors).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'ajax-error mt-2 text-sm text-red-600';
                            errorDiv.textContent = messages.join(' ');
                            input.parentElement.appendChild(errorDiv);
                            
                            // Add error class to input
                            input.classList.add('border-red-500');
                            input.classList.remove('border-gray-300');
                        }
                    });
                    
                    // Clear stored errors
                    sessionStorage.removeItem('formErrors');
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.ajax-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }

            // Initialize the form
            initializeForm();
            restoreFormState();
        });
    </script>
</x-app-layout>
