<div class="relative group h-[450px] w-[350px]" wire:key="card-{{ $card['name'] }}">
    <div class="mtg-card h-[450px] w-[350px] bg-white overflow-hidden shadow-md rounded-lg transform transition-all duration-300 ease-out
                {{ $showFlipAnimation ? 'rotate-y-180' : '' }}" 
         style="transform-style: preserve-3d;">
        
        <!-- Front of Card -->
        <div class="w-full h-full relative rounded-lg overflow-hidden transition-all duration-300
                    {{ $showFlipAnimation ? 'opacity-0 rotate-y-180' : 'opacity-100' }}"
             style="backface-visibility: hidden;">
            
            <div class="card-frame h-full flex flex-col bg-[#f8e7c9] border-[10px] border-[#171314] rounded-lg overflow-hidden">
                <!-- Simplified Frame Effects -->
                <div class="absolute inset-0 opacity-20 mix-blend-overlay" 
                     style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB4PSIwIiB5PSIwIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPgogIDxwYXRoIGQ9Ik0gMjAgMTAgQyAyMCAxNS41MjI4IDE1LjUyMjggMjAgMTAgMjAgQyA0LjQ3NzE1IDIwIDAgMTUuNTIyOCAwIDEwIEMgMCA0LjQ3NzE1IDQuNDc3MTUgMCAxMCAwIEMgMTUuNTIyOCAwIDIwIDQuNDc3MTUgMjAgMTAgWiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMjkyNTI0IiBzdHJva2Utb3BhY2l0eT0iMC4xIiBzdHJva2Utd2lkdGg9IjAuNSIvPgo8L3BhdHRlcm4+CjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjcGF0dGVybikiLz4KPC9zdmc+');">
                </div>

                <!-- Title Bar -->
                <div class="card-title-bar relative flex justify-between items-center px-4 py-3 bg-[#171314] text-[#d3ced9]">
                    <h2 class="card-name text-base font-bold font-matrix tracking-wide">
                        {{ $card['name'] }}
                    </h2>
                    <div class="mana-cost flex space-x-1">
                        @if(isset($card['mana_cost']))
                            @foreach(explode(',', $card['mana_cost']) as $symbol)
                                <div class="mana-symbol rounded-full flex justify-center items-center text-xs font-bold w-6 h-6
                                    @if(strtolower($symbol) == 'w') bg-gradient-to-br from-white to-[#e6e6e6] text-[#211d15]
                                    @elseif(strtolower($symbol) == 'u') bg-gradient-to-br from-[#0e67ab] to-[#064e87] text-white
                                    @elseif(strtolower($symbol) == 'b') bg-gradient-to-br from-[#2b2824] to-[#171512] text-[#d3d4d5]
                                    @elseif(strtolower($symbol) == 'r') bg-gradient-to-br from-[#d3202a] to-[#aa1017] text-[#f9e6e7]
                                    @elseif(strtolower($symbol) == 'g') bg-gradient-to-br from-[#00733e] to-[#005c32] text-[#c4d3ca]
                                    @else bg-gradient-to-br from-[#beb9b2] to-[#a7a29c] text-[#171512]
                                    @endif">
                                    {{ $symbol }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Art Box -->
                <div class="relative mx-2 mt-2 mb-2 overflow-hidden h-[180px]">
                    <img src="{{ $card['image_url'] }}" 
                         alt="{{ $card['name'] }}" 
                         class="w-full h-full object-cover object-center"
                         loading="lazy">
                </div>

                <!-- Type Line -->
                <div class="card-type relative mx-2 mb-2">
                    <div class="relative px-4 py-1.5 text-sm font-matrix bg-[#f8e7c9] text-[#171314] tracking-wide border-t-2 border-b-2 border-[#171314]">
                        {{ $card['card_type'] }}
                    </div>
                </div>

                <!-- Text Box -->
                <div class="card-text relative mx-2 bg-[#f8e7c9] border-2 border-[#171314] text-[#171314] min-h-[120px] rounded-sm">
                    <div class="p-4 space-y-2">
                        <p class="abilities-text text-sm font-matrix leading-6">{{ $card['abilities'] }}</p>
                        <div class="divider h-px bg-gradient-to-r from-transparent via-[#171314]/20 to-transparent my-2"></div>
                        <p class="flavor-text italic text-sm font-mplantin leading-6 text-[#171314]/90">{{ $card['flavor_text'] }}</p>
                    </div>
                </div>

                <!-- Info Line -->
                <div class="card-footer relative grid grid-cols-2 mt-2 mx-2 mb-2 px-4 py-2 bg-[#171314] text-[#d3ced9] text-xs min-w-full">
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
                        <span class="rarity-details truncate">{{ $card['rarity'] }}</span>
                    </div>
                    @if($card['power_toughness'])
                        <span class="power-toughness font-bold text-right">
                            {{ $card['power_toughness'] }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back of Card -->
        <div class="absolute inset-0 w-full h-full rounded-lg overflow-hidden transition-all duration-300
                    {{ $showFlipAnimation ? 'opacity-100 rotate-y-0' : 'opacity-0 rotate-y-180' }}"
             style="backface-visibility: hidden;">
            <div class="h-full bg-indigo-900 p-4 rounded-lg">
                <div class="h-full flex flex-col items-center justify-center text-center text-white">
                    <h3 class="text-xl font-bold mb-4">{{ $card['name'] }}</h3>
                    <p class="text-sm mb-4">{{ $card['card_type'] }}</p>
                    <p class="text-xs italic">{{ $card['rarity'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
