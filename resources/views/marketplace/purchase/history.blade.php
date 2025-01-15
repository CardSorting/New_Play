<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-semibold">Purchase History</h2>
                            <p class="text-sm text-gray-500 mt-1">Track your marketplace purchases and spending</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('marketplace.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Browse Marketplace
                            </a>
                        </div>
                    </div>

                    <!-- Purchase Overview -->
                    <div class="mb-8">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                            <h3 class="text-lg font-medium mb-4">Purchase Overview</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <div class="text-sm text-gray-500 mb-1">Total Spent</div>
                                    <div class="text-2xl font-bold text-purple-600">{{ number_format($totalSpent) }} PULSE</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <div class="text-sm text-gray-500 mb-1">Packs Purchased</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $purchasedPacks->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase History -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Purchase History</h3>
                        @if($purchasedPacks->isEmpty())
                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Purchases Yet</h3>
                                <p class="mt-1 text-sm text-gray-500">You haven't purchased any packs from the marketplace.</p>
                                <div class="mt-6">
                                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Browse Marketplace
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pack
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Seller
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Price
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Purchase Date
                                            </th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($purchasedPacks as $pack)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $pack->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $pack->cards_count }} cards
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $pack->user->name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-sm bg-purple-100 text-purple-800 rounded">
                                                        {{ number_format($pack->price) }} PULSE
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pack->updated_at->format('M j, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('packs.show', $pack) }}" class="text-blue-600 hover:text-blue-900">
                                                        View Pack
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
