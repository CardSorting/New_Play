<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    Available Packs
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Browse and purchase sealed packs from other collectors.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Pack Grid with Pagination -->
                    @include('marketplace.browse.components.pack-grid', ['availablePacks' => $availablePacks])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
