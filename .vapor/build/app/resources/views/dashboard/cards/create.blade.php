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
                    <!-- Original Image Author -->
                    <div class="mb-4 text-sm text-gray-600">
                        <span class="font-medium">Original Image by:</span>
                        <span class="ml-1">{{ $image->user->name }}</span>
                    </div>
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
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <p class="font-medium">Errors occurred:</p>
                            <ul class="mt-1 text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cards.store') }}" class="space-y-6" novalidate>
                        @csrf
                        <input type="hidden" name="image_id" value="{{ $image->id }}">
                        <input type="hidden" name="image_url" value="{{ $image->image_url }}">
                    
                        <!-- Card Name -->
                        <div>
                            <x-input-label for="name" value="Card Name" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name') }}" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Mana Cost -->
                        <div>
                            <x-input-label for="mana_cost" value="Mana Cost (e.g., 2RG for 2 colorless, 1 red, 1 green)" />
                            <x-text-input id="mana_cost" name="mana_cost" type="text" class="mt-1 block w-full" required value="{{ old('mana_cost') }}" />
                            <x-input-error :messages="$errors->get('mana_cost')" class="mt-2" />
                        </div>

                        <!-- Card Type -->
                        <div>
                            <x-input-label for="card_type" value="Card Type" />
                            <x-text-input id="card_type" name="card_type" type="text" class="mt-1 block w-full" required placeholder="e.g., Creature - Dragon" value="{{ old('card_type') }}" />
                            <x-input-error :messages="$errors->get('card_type')" class="mt-2" />
                        </div>

                        <!-- Card Text/Abilities -->
                        <div>
                            <x-input-label for="abilities" value="Card Text/Abilities" />
                            <textarea id="abilities" name="abilities" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('abilities') }}</textarea>
                            <x-input-error :messages="$errors->get('abilities')" class="mt-2" />
                        </div>

                        <!-- Flavor Text -->
                        <div>
                            <x-input-label for="flavor_text" value="Flavor Text (optional)" />
                            <textarea id="flavor_text" name="flavor_text" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('flavor_text') }}</textarea>
                            <x-input-error :messages="$errors->get('flavor_text')" class="mt-2" />
                        </div>

                        <!-- Power/Toughness (for creatures) -->
                        <div>
                            <x-input-label for="power_toughness" value="Power/Toughness (for creatures, e.g. 3/4)" />
                            <x-text-input id="power_toughness" name="power_toughness" type="text" class="mt-1 block w-full" value="{{ old('power_toughness') }}" />
                            <x-input-error :messages="$errors->get('power_toughness')" class="mt-2" />
                        </div>

                        <!-- Original Image Info -->
                        <div class="bg-gray-100 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Original Image Author:</span>
                                <span class="ml-1">{{ $image->user->name }}</span>
                            </p>
                            <p class="text-sm text-gray-600">The original author will be credited in the card's metadata.</p>
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
                            <x-primary-button class="!text-white !bg-gray-800 hover:!bg-gray-700">
                                {{ __('Create Card') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
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

                // Update card name
                document.querySelector('.card-name').textContent = name;

                // Update mana cost
                if (!manaCost) {
                    manaContainer.innerHTML = '<div class="mana-symbol text-gray-500">No Mana Cost</div>';
                } else {
                    const symbols = manaCost.split('');
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
                document.querySelector('.card-type div:last-child').textContent = cardType;

                // Update abilities and flavor text
                document.querySelector('.abilities-text').textContent = abilities;
                document.querySelector('.flavor-text').textContent = flavorText || 'No flavor text';

                // Update power/toughness
                document.querySelector('.power-toughness').textContent = powerToughness;
            }
            
            // Initialize form fields and preview
            formFields.forEach(field => {
                field.addEventListener('input', updatePreview);
            });
            
            // Initial preview update
            updatePreview();
            
            // Handle form submission
            let isSubmitting = false;
            form.addEventListener('submit', function() {
                if (isSubmitting) {
                    return false;
                }
                
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating...</span>';
                isSubmitting = true;
            });
        });
    </script>
</x-app-layout>
