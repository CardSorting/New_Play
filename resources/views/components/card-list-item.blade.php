<div class="card-item bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 flex-1"
     data-rarity="{{ strtolower(str_replace(' ', '-', $card['rarity'])) }}"
     data-type="{{ strtolower(str_replace(' ', '-', $card['card_type'])) }}"
     data-name="{{ strtolower($card['name']) }}">
    <div class="flex items-center p-4 w-full">
        <div class="flex-shrink-0 w-32 h-32 rounded-lg overflow-hidden mr-6">
            <img src="{{ $card['image_url'] }}" 
                 alt="{{ $card['name'] }}"
                 class="w-full h-full object-cover">
        </div>
        <div class="flex-grow">
            <h3 class="text-lg font-semibold text-gray-900">{{ $card['name'] }}</h3>
            <p class="text-sm text-gray-600">{{ $card['card_type'] }}</p>
            <div class="flex items-center mt-2">
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $getRarityClasses() }}">
                    {{ $card['rarity'] }}
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="showCardDetails('{{ $card['name'] }}')"
                    class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        </div>
    </div>
</div>
