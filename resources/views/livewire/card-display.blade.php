<div class="relative group h-full" wire:key="card-{{ $card['name'] }}">
    <div class="mtg-card h-full bg-white overflow-hidden shadow-xl rounded-lg transform transition-all duration-700 ease-out
                {{ $showFlipAnimation ? 'rotate-y-180 scale-105' : '' }} 
                {{ $this->getRarityClasses() }}"
         data-rarity="{{ $this->getNormalizedRarity() }}"
         data-author="{{ $card['author'] }}"
         style="transform-style: preserve-3d; will-change: transform;">
        
        <!-- Front of Card -->
        <div class="w-full h-full relative rounded-lg overflow-hidden transition-all duration-500
                    {{ $showFlipAnimation ? 'opacity-0 rotate-y-180' : 'opacity-100' }}"
             style="backface-visibility: hidden;">
            
            <div class="card-frame h-full flex flex-col bg-[#f8e7c9] border-[14px] border-[#171314] rounded-lg overflow-hidden relative
                        before:absolute before:inset-0 before:bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.25),transparent_70%)] before:mix-blend-overlay
                        after:absolute after:inset-0 after:bg-[linear-gradient(45deg,transparent,rgba(255,255,255,0.15),transparent)] after:mix-blend-overlay">
                <!-- Enhanced Frame Texture -->
                <div class="absolute inset-0 opacity-20 mix-blend-overlay" 
                     style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB4PSIwIiB5PSIwIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPgogIDxwYXRoIGQ9Ik0gMjAgMTAgQyAyMCAxNS41MjI4IDE1LjUyMjggMjAgMTAgMjAgQyA0LjQ3NzE1IDIwIDAgMTUuNTIyOCAwIDEwIEMgMCA0LjQ3NzE1IDQuNDc3MTUgMCAxMCAwIEMgMTUuNTIyOCAwIDIwIDQuNDc3MTUgMjAgMTAgWiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMjkyNTI0IiBzdHJva2Utb3BhY2l0eT0iMC4xIiBzdHJva2Utd2lkdGg9IjAuNSIvPgo8L3BhdHRlcm4+CjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjcGF0dGVybikiLz4KPC9zdmc+');">
                </div>
                <!-- Card Frame Texture & Effects -->
                <div class="absolute inset-0 mix-blend-overlay opacity-30 pointer-events-none" 
                     style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1IiBoZWlnaHQ9IjUiPgo8cmVjdCB3aWR0aD0iNSIgaGVpZ2h0PSI1IiBmaWxsPSIjZmZmIj48L3JlY3Q+CjxwYXRoIGQ9Ik0wIDVMNSAwWk02IDRMNCA2Wk0tMSAxTDEgLTFaIiBzdHJva2U9IiMyOTI1MjQiIHN0cm9rZS1vcGFjaXR5PSIwLjA1Ij48L3BhdGg+Cjwvc3ZnPg==');">
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 via-transparent to-black/5 mix-blend-overlay pointer-events-none"></div>
                <!-- Enhanced Inner Border -->
                <div class="absolute inset-[3px] border-2 border-[#171314]/20 rounded pointer-events-none
                           before:absolute before:inset-[1px] before:border before:border-white/10 before:rounded
                           after:absolute after:inset-[-1px] after:border after:border-black/20 after:rounded"></div>
                
                <!-- Enhanced Corner Ornaments -->
                <div class="absolute top-0 left-0 w-10 h-10 border-t-2 border-l-2 border-[#171314]/30 rounded-tl-lg
                           before:absolute before:inset-0 before:border-t before:border-l before:border-white/10 before:rounded-tl-lg
                           after:absolute after:top-1 after:left-1 after:w-3 after:h-3 after:border-t after:border-l after:border-[#171314]/20"></div>
                <div class="absolute top-0 right-0 w-10 h-10 border-t-2 border-r-2 border-[#171314]/30 rounded-tr-lg
                           before:absolute before:inset-0 before:border-t before:border-r before:border-white/10 before:rounded-tr-lg
                           after:absolute after:top-1 after:right-1 after:w-3 after:h-3 after:border-t after:border-r after:border-[#171314]/20"></div>
                <div class="absolute bottom-0 left-0 w-10 h-10 border-b-2 border-l-2 border-[#171314]/30 rounded-bl-lg
                           before:absolute before:inset-0 before:border-b before:border-l before:border-white/10 before:rounded-bl-lg
                           after:absolute after:bottom-1 after:left-1 after:w-3 after:h-3 after:border-b after:border-l after:border-[#171314]/20"></div>
                <div class="absolute bottom-0 right-0 w-10 h-10 border-b-2 border-r-2 border-[#171314]/30 rounded-br-lg
                           before:absolute before:inset-0 before:border-b before:border-r before:border-white/10 before:rounded-br-lg
                           after:absolute after:bottom-1 after:right-1 after:w-3 after:h-3 after:border-b after:border-r after:border-[#171314]/20"></div>
                
                <!-- Title Bar -->
                <div class="card-title-bar relative flex justify-between items-center px-4 py-3 bg-[#171314] text-[#d3ced9]">
                    <!-- Title Bar Gradients -->
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_100%,rgba(255,255,255,0.15),transparent_70%)]"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>
                    <!-- Title Bar Texture -->
                    <div class="absolute inset-0 opacity-10 mix-blend-overlay" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmYiIHN0cm9rZS1vcGFjaXR5PSIwLjEiIHN0cm9rZS13aWR0aD0iMC41Ii8+Cjwvc3ZnPg==');"></div>
                    <h2 class="card-name text-base font-bold font-matrix tracking-wide">
                        {{ $card['name'] }}
                    </h2>
                    <div class="mana-cost flex space-x-1">
                        @if(isset($card['mana_cost']))
                            @foreach(explode(',', $card['mana_cost']) as $symbol)
                                <div class="mana-symbol rounded-full flex justify-center items-center text-xs font-bold w-6 h-6 transform transition-transform duration-200 relative
                                    @if(strtolower($symbol) == 'w') bg-gradient-to-br from-white to-[#e6e6e6] text-[#211d15] shadow-inner
                                    @elseif(strtolower($symbol) == 'u') bg-gradient-to-br from-[#0e67ab] to-[#064e87] text-white shadow-inner
                                    @elseif(strtolower($symbol) == 'b') bg-gradient-to-br from-[#2b2824] to-[#171512] text-[#d3d4d5] shadow-inner
                                    @elseif(strtolower($symbol) == 'r') bg-gradient-to-br from-[#d3202a] to-[#aa1017] text-[#f9e6e7] shadow-inner
                                    @elseif(strtolower($symbol) == 'g') bg-gradient-to-br from-[#00733e] to-[#005c32] text-[#c4d3ca] shadow-inner
                                    @else bg-gradient-to-br from-[#beb9b2] to-[#a7a29c] text-[#171512] shadow-inner
                                    @endif
                                    group-hover:scale-110">
                                    <!-- Mana Symbol Inner Glow -->
                                    <div class="absolute inset-0 rounded-full opacity-50
                                        @if(strtolower($symbol) == 'w') bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,0.5),transparent_70%)]
                                        @elseif(strtolower($symbol) == 'u') bg-[radial-gradient(circle_at_50%_0%,rgba(14,103,171,0.5),transparent_70%)]
                                        @elseif(strtolower($symbol) == 'b') bg-[radial-gradient(circle_at_50%_0%,rgba(43,40,36,0.5),transparent_70%)]
                                        @elseif(strtolower($symbol) == 'r') bg-[radial-gradient(circle_at_50%_0%,rgba(211,32,42,0.5),transparent_70%)]
                                        @elseif(strtolower($symbol) == 'g') bg-[radial-gradient(circle_at_50%_0%,rgba(0,115,62,0.5),transparent_70%)]
                                        @else bg-[radial-gradient(circle_at_50%_0%,rgba(190,185,178,0.5),transparent_70%)]
                                        @endif">
                                    </div>
                                    <div class="absolute inset-0 rounded-full bg-white opacity-20 mix-blend-overlay"></div>
                                    {{ $symbol }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Art Box -->
                <div class="relative mx-2 mt-2 mb-2 overflow-hidden group h-[180px]">
                    <!-- Art Frame -->
                    <div class="absolute inset-0 border-2 border-[#171314] z-20 pointer-events-none rounded-sm"></div>
                    <div class="absolute inset-[1px] border border-[#171314]/20 z-20 pointer-events-none rounded-sm"></div>
                    <!-- Art Box Corner Ornaments -->
                    <div class="absolute top-0 left-0 w-4 h-4 border-t border-l border-[#171314]/40 z-30"></div>
                    <div class="absolute top-0 right-0 w-4 h-4 border-t border-r border-[#171314]/40 z-30"></div>
                    <div class="absolute bottom-0 left-0 w-4 h-4 border-b border-l border-[#171314]/40 z-30"></div>
                    <div class="absolute bottom-0 right-0 w-4 h-4 border-b border-r border-[#171314]/40 z-30"></div>
                    <img src="{{ $card['image_url'] }}" 
                         alt="{{ $card['name'] }}" 
                         class="w-full h-full object-cover object-center transform transition-all duration-700 ease-out
                                group-hover:scale-110 group-hover:filter group-hover:brightness-110"
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-white/10 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                    @if($this->isMythicRare() || $this->isRare())
                        <div class="absolute inset-0 bg-[conic-gradient(from_0deg,transparent_0deg,rgba(255,255,255,0.2)_90deg,transparent_180deg)] animate-rotate-slow opacity-0 group-hover:opacity-100"></div>
                    @endif
                </div>

                <!-- Enhanced Type Line -->
                <div class="card-type relative mx-2 mb-2">
                    <div class="absolute inset-0 bg-[#171314]"></div>
                    <div class="relative px-4 py-1.5 text-sm font-matrix bg-[#f8e7c9] text-[#171314] tracking-wide border-t-2 border-b-2 border-[#171314]
                         before:absolute before:inset-0 before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent before:mix-blend-overlay
                         after:absolute after:inset-0 after:bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.1),transparent_70%)] after:mix-blend-overlay">
                         <!-- Type Line Ornaments -->
                         <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-r from-[#171314] to-transparent opacity-20"></div>
                         <div class="absolute right-0 top-0 bottom-0 w-1 bg-gradient-to-l from-[#171314] to-transparent opacity-20"></div>
                        {{ $card['card_type'] }}
                    </div>
                </div>

                <!-- Enhanced Text Box -->
                <div class="card-text relative mx-2 bg-[#f8e7c9] border-2 border-[#171314] text-[#171314] min-h-[120px] flex-grow rounded-sm">
                    <!-- Text Box Effects -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/20 via-transparent to-black/5"></div>
                    <div class="absolute inset-[1px] border border-[#171314]/10 rounded-sm"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,0.2),transparent_70%)] mix-blend-overlay"></div>
                    <!-- Text Box Corner Ornaments -->
                    <div class="absolute top-0 left-0 w-2 h-2 border-t border-l border-[#171314]/20"></div>
                    <div class="absolute top-0 right-0 w-2 h-2 border-t border-r border-[#171314]/20"></div>
                    <div class="absolute bottom-0 left-0 w-2 h-2 border-b border-l border-[#171314]/20"></div>
                    <div class="absolute bottom-0 right-0 w-2 h-2 border-b border-r border-[#171314]/20"></div>
                    <div class="relative p-4">
                        <div class="space-y-2">
                            <p class="abilities-text text-sm font-matrix leading-6 text-[#171314]">{{ $card['abilities'] }}</p>
                            <div class="divider h-px bg-gradient-to-r from-transparent via-[#171314]/20 to-transparent my-2"></div>
                            <p class="flavor-text italic text-sm font-mplantin leading-6 text-[#171314]/90">{{ $card['flavor_text'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Line -->
                <div class="card-footer relative flex justify-between items-center mt-2 mx-2 mb-2 px-4 py-2 bg-[#171314] text-[#d3ced9] text-xs font-matrix tracking-wide">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    <div class="relative flex justify-between items-center w-full z-10">
                        <div class="flex items-center space-x-2">
                            <span class="rarity-symbol text-xs
                                @if($this->isMythicRare()) text-orange-400
                                @elseif($this->isRare()) text-yellow-300
                                @elseif($this->isUncommon()) text-gray-400
                                @else text-gray-600 @endif">
                                @if($this->isMythicRare()) M
                                @elseif($this->isRare()) R
                                @elseif($this->isUncommon()) U
                                @else C @endif
                            </span>
                            <span class="rarity-details font-medium tracking-wide">{{ $card['rarity'] }}</span>
                        </div>
                        @if($card['power_toughness'])
                            <span class="power-toughness font-bold">
                                {{ $card['power_toughness'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Back of Card -->
        <div class="absolute inset-0 w-full h-full rounded-lg overflow-hidden transition-all duration-500
                    {{ $showFlipAnimation ? 'opacity-100 rotate-y-0' : 'opacity-0 rotate-y-180' }}"
             style="backface-visibility: hidden;">
            <div class="card-back h-full bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-900 p-0.5">
                <div class="h-full bg-gradient-to-br from-indigo-800 via-purple-800 to-indigo-800 rounded-lg p-4">
                    <!-- Decorative Pattern -->
                    <div class="relative h-full border-4 border-gold-500 rounded-lg overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,215,0,0.1),transparent)]"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-32 h-32 bg-gradient-to-r from-gold-400 via-gold-200 to-gold-400 rounded-full opacity-20"></div>
                        </div>
                        <!-- Card Info -->
                        <div class="relative h-full flex flex-col items-center justify-center text-center p-6 text-gold-200">
                            <h3 class="text-xl font-bold mb-4">{{ $card['name'] }}</h3>
                            <p class="text-sm mb-4">{{ $card['card_type'] }}</p>
                            <p class="text-xs italic">{{ $card['rarity'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Details Modal -->
    @if($showDetails)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="toggleDetails">
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 transform transition-all duration-300 scale-95 hover:scale-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">{{ $card['name'] }}</h3>
                <button wire:click="toggleDetails" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <img src="{{ $card['image_url'] }}" alt="{{ $card['name'] }}" class="w-full rounded-lg shadow-lg">
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Card Type</h4>
                        <p class="text-gray-900">{{ $card['card_type'] }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Abilities</h4>
                        <p class="text-gray-900">{{ $card['abilities'] }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Flavor Text</h4>
                        <p class="text-gray-900 italic">{{ $card['flavor_text'] }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Author</h4>
                        <p class="text-gray-900">{{ $card['author'] }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Rarity</h4>
                        <p class="text-gray-900">{{ $card['rarity'] }}</p>
                    </div>
                        @if(isset($card['power_toughness']))
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Power/Toughness</h4>
                            <p class="text-gray-900">{{ $card['power_toughness'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
