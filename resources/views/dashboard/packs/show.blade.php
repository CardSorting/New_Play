<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-xl font-semibold">{{ $pack->name }}</h2>
                            @if($pack->description)
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $pack->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 text-sm {{ $pack->is_sealed ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded-full">
                                {{ $pack->is_sealed ? 'Sealed' : 'Open' }}
                            </span>
                            @if(!$pack->is_sealed && $pack->cards->count() === $pack->card_limit)
                                <form method="POST" action="{{ route('packs.seal', $pack) }}">
                                    @csrf
                                    <x-primary-button>Seal Pack</x-primary-button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium">Pack Progress</span>
                                <span class="text-sm">{{ $pack->cards->count() }} / {{ $pack->card_limit }} cards</span>
                            </div>
                            <div class="mt-2 h-2 bg-gray-200 dark:bg-gray-600 rounded-full">
                                <div class="h-2 bg-blue-500 rounded-full" style="width: {{ ($pack->cards->count() / $pack->card_limit) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>

                    @if(!$pack->is_sealed && $pack->cards->count() < $pack->card_limit)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium mb-4">Add Cards to Pack</h3>
                            @if($availableCards->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400">You don't have any cards available to add to this pack.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($availableCards as $card)
                                        <div class="border dark:border-gray-700 rounded-lg p-4 transform-gpu card-container">
                                            <div class="card">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <h4 class="font-medium">{{ $card->name }}</h4>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $card->card_type }}</p>
                                                            <span class="text-xs px-2 py-1 rounded {{ 
                                                                $card->rarity === 'Mythic Rare' ? 'bg-red-100 text-red-800' :
                                                                ($card->rarity === 'Rare' ? 'bg-yellow-100 text-yellow-800' :
                                                                ($card->rarity === 'Uncommon' ? 'bg-gray-100 text-gray-800' :
                                                                'bg-green-100 text-green-800'))
                                                            }}">
                                                                {{ $card->rarity }}
                                                            </span>
                                                        </div>
                                                        <div class="mt-1">
                                                            <p class="text-sm text-blue-600">{{ $card->mana_cost }}</p>
                                                        </div>
                                                    </div>
                                                    <form method="POST" action="{{ route('packs.add-card', $pack) }}">
                                                        @csrf
                                                        <input type="hidden" name="card_id" value="{{ $card->id }}">
                                                        <button type="submit" class="text-blue-500 hover:text-blue-700">
                                                            Add to Pack
                                                        </button>
                                                    </form>
                                                </div>
                                                @if($card->image_url)
                                                    <div class="card-face">
                                                        <img src="{{ $card->image_url }}" alt="{{ $card->name }}" class="w-full h-40 object-cover rounded mt-2">
                                                    </div>
                                                @endif
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $card->abilities }}</p>
                                                    @if($card->power_toughness)
                                                        <p class="text-sm font-semibold mt-1">{{ $card->power_toughness }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <div>
                        <h3 class="text-lg font-medium mb-4">Pack Contents</h3>
                        @if($pack->cards->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400">This pack is currently empty.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($pack->cards as $card)
                                    <div class="border dark:border-gray-700 rounded-lg p-4 transform-gpu card-container">
                                        <div class="card">
                                            <div class="mb-2">
                                                <h4 class="font-medium">{{ $card->name }}</h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $card->card_type }}</p>
                                                    <span class="text-xs px-2 py-1 rounded {{ 
                                                        $card->rarity === 'Mythic Rare' ? 'bg-red-100 text-red-800' :
                                                        ($card->rarity === 'Rare' ? 'bg-yellow-100 text-yellow-800' :
                                                        ($card->rarity === 'Uncommon' ? 'bg-gray-100 text-gray-800' :
                                                        'bg-green-100 text-green-800'))
                                                    }}">
                                                        {{ $card->rarity }}
                                                    </span>
                                                </div>
                                                <div class="mt-1">
                                                    <p class="text-sm text-blue-600">{{ $card->mana_cost }}</p>
                                                </div>
                                            </div>
                                            @if($card->image_url)
                                                <div class="card-face">
                                                    <img src="{{ $card->image_url }}" alt="{{ $card->name }}" class="w-full h-40 object-cover rounded">
                                                </div>
                                            @endif
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $card->abilities }}</p>
                                                @if($card->power_toughness)
                                                    <p class="text-sm font-semibold mt-1">{{ $card->power_toughness }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="/js/mtg-card-3d-effect.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                initializeCardEffects();
            });
        </script>
    @endpush
</x-app-layout>
