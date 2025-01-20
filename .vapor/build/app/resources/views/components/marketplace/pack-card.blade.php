@props([
    'pack',
    'mode' => 'browse', // browse, listed, purchased, unlisted
    'showActions' => true
])

<div class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-medium">{{ $pack->name }}</h3>
        <div class="flex flex-col items-end">
            @if($pack->is_listed)
                <span class="px-2 py-1 text-sm bg-purple-100 text-purple-800 rounded mb-1">
                    {{ number_format($pack->price) }} PULSE
                </span>
            @endif
            <span class="text-xs text-gray-500">
                by {{ $pack->user->name }}
            </span>
        </div>
    </div>
    
    @if($pack->description)
        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $pack->description }}</p>
    @endif

    <div class="mb-4">
        <div class="pack-card relative aspect-[7/5] rounded-lg overflow-hidden group cursor-pointer">
            <!-- Preview card -->
            <div class="absolute inset-0">
                <img src="{{ $pack->cards->first()->image_url }}" 
                     alt="Pack preview" 
                     class="w-full h-full object-cover opacity-50 mix-blend-luminosity">
            </div>
            <!-- Base gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/80 via-purple-700/85 to-purple-900/95 mix-blend-multiply"></div>
            <!-- Foil pattern -->
            <div class="absolute inset-0 foil-pattern opacity-30"></div>
            <!-- Glow effect -->
            <div class="absolute inset-0 bg-gradient-to-t from-purple-500/20 to-transparent"></div>
            <!-- Shimmer effect -->
            <div class="shimmer"></div>
            <!-- Pack design elements -->
            <div class="absolute inset-0 flex flex-col items-center justify-center p-4">
                <!-- Outer border -->
                <div class="absolute inset-2 border-2 border-gold opacity-30 rounded-lg"></div>
                <!-- Inner border -->
                <div class="absolute inset-4 border border-gold opacity-20 rounded"></div>
                <!-- Pack content -->
                <div class="relative text-center z-10 transform transition-transform duration-500 group-hover:scale-105">
                    <div class="text-gold font-bold text-2xl mb-3 font-beleren tracking-wider">
                        SEALED PACK
                    </div>
                    <div class="text-gold/90 font-medium text-lg mb-4 font-beleren">
                        {{ $pack->cards_count }} CARDS
                    </div>
                    <div class="text-gold/70 text-xs font-medium uppercase tracking-widest">
                        Premium Collection
                    </div>
                </div>
                <!-- Corner decorations -->
                <div class="absolute top-6 left-6 w-3 h-3 border-t-2 border-l-2 border-gold opacity-50"></div>
                <div class="absolute top-6 right-6 w-3 h-3 border-t-2 border-r-2 border-gold opacity-50"></div>
                <div class="absolute bottom-6 left-6 w-3 h-3 border-b-2 border-l-2 border-gold opacity-50"></div>
                <div class="absolute bottom-6 right-6 w-3 h-3 border-b-2 border-r-2 border-gold opacity-50"></div>
            </div>
        </div>
    </div>

    @if($showActions)
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $pack->cards_count }} cards
            </span>
            
            @if($mode === 'browse' && $pack->user_id !== Auth::id())
                <form action="{{ route('marketplace.purchase', $pack) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                            onclick="return confirm('Are you sure you want to purchase this pack for {{ number_format($pack->price) }} PULSE?')">
                        Purchase Pack
                    </button>
                </form>
            @elseif($mode === 'listed' && $pack->user_id === Auth::id())
                <form action="{{ route('marketplace.seller.unlist', $pack) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Remove Listing
                    </button>
                </form>
            @elseif($mode === 'purchased')
                <a href="{{ route('packs.show', $pack) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    View Pack
                </a>
            @elseif($mode === 'unlisted')
                {{ $actions ?? '' }}
            @endif
        </div>
    @endif
</div>
