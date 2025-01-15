<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Your Card Packs</h2>
                        <a href="{{ route('packs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Pack
                        </a>
                    </div>

                    @if($packs->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">You haven't created any card packs yet.</p>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Create a pack to start collecting cards!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($packs as $pack)
                                <div class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-medium">{{ $pack->name }}</h3>
                                        <span class="px-2 py-1 text-xs {{ $pack->is_sealed ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded">
                                            {{ $pack->is_sealed ? 'Sealed' : 'Open' }}
                                        </span>
                                    </div>
                                    
                                    @if($pack->description)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $pack->description }}</p>
                                    @endif

                                    @if($pack->is_sealed && $pack->cards->isNotEmpty())
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
                                                        <div class="text-gold/70 text-xs font-medium uppercase tracking-widest mb-4">
                                                            Premium Collection
                                                        </div>
                                                        <a href="{{ route('packs.open', ['pack' => $pack->id]) }}" 
                                                           class="inline-block px-6 py-2 bg-gold/20 hover:bg-gold/30 text-gold border border-gold/50 rounded-md transition-colors duration-200 text-sm font-medium uppercase tracking-wider">
                                                            Open Pack
                                                        </a>
                                                    </div>
                                                    <!-- Corner decorations -->
                                                    <div class="absolute top-6 left-6 w-3 h-3 border-t-2 border-l-2 border-gold opacity-50"></div>
                                                    <div class="absolute top-6 right-6 w-3 h-3 border-t-2 border-r-2 border-gold opacity-50"></div>
                                                    <div class="absolute bottom-6 left-6 w-3 h-3 border-b-2 border-l-2 border-gold opacity-50"></div>
                                                    <div class="absolute bottom-6 right-6 w-3 h-3 border-b-2 border-r-2 border-gold opacity-50"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ $pack->cards_count }} / {{ $pack->card_limit }} cards</span>
                                        @if(!$pack->is_sealed)
                                            <a href="{{ route('packs.show', $pack) }}" class="text-blue-500 hover:text-blue-700">
                                                View Details →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
