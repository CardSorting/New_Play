@props(['card'])

<x-dashboard-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $card->name }}
                </h2>
                <a href="{{ route('cards.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    Back to Collection
                </a>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card Image Section -->
                <div class="space-y-4">
                    <div class="aspect-w-2 aspect-h-3 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <img src="{{ $card->image_url }}" 
                             alt="{{ $card->name }}" 
                             class="object-cover w-full h-full">
                    </div>
                </div>

                <!-- Card Details Section -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Details</h3>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-300">{{ $card->type }}</dd>
                            
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mana Cost</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-300">{{ $card->mana_cost }}</dd>
                            
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rarity</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-300">{{ $card->rarity }}</dd>
                            
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Set</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-300">{{ $card->set_name }}</dd>
                        </dl>
                    </div>

                    <!-- Card Description -->
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Description</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">
                            {{ $card->description }}
                        </p>
                    </div>

                    <!-- Additional Info -->
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Additional Info</h3>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Power/Toughness</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-300">
                                @if($card->power && $card->toughness)
                                    {{ $card->power }}/{{ $card->toughness }}
                                @else
                                    N/A
                                @endif
                            </dd>
                            
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Loyalty</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-300">
                                {{ $card->loyalty ?? 'N/A' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>