<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-semibold">Seller Dashboard</h2>
                            <p class="text-sm text-gray-500 mt-1">Manage your marketplace listings</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('marketplace.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Browse Marketplace
                            </a>
                            <a href="{{ route('marketplace.seller.sales') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                View Sales History
                            </a>
                        </div>
                    </div>

                    <!-- Sales Overview -->
                    <div class="mb-8">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                            <h3 class="text-lg font-medium mb-4">Sales Overview</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <div class="text-sm text-gray-500 mb-1">Total Sales</div>
                                    <div class="text-2xl font-bold text-purple-600">{{ number_format($totalSales) }} PULSE</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <div class="text-sm text-gray-500 mb-1">Active Listings</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $listedPacks->count() }}</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                    <div class="text-sm text-gray-500 mb-1">Recent Sales</div>
                                    <div class="text-2xl font-bold text-green-600">{{ $recentSales->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Active Listings -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Active Listings</h3>
                        @if($listedPacks->isEmpty())
                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Active Listings</h3>
                                <p class="mt-1 text-sm text-gray-500">You don't have any packs listed in the marketplace.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($listedPacks as $pack)
                                    <x-marketplace.pack-card :pack="$pack" mode="listed" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Available Packs -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Available to List</h3>
                        @if($availablePacks->isEmpty())
                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Available Packs</h3>
                                <p class="mt-1 text-sm text-gray-500">Create and seal some packs to list them in the marketplace.</p>
                                <div class="mt-6">
                                    <a href="{{ route('packs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Create New Pack
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($availablePacks as $pack)
                                    <div x-data="{ showListingForm: false, price: '', submitting: false }" class="relative">
                                        <x-marketplace.pack-card :pack="$pack" mode="unlisted">
                                            <x-slot name="actions">
                                                <button @click="showListingForm = true" 
                                                        class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                    List Pack
                                                </button>
                                            </x-slot>
                                        </x-marketplace.pack-card>

                                        <!-- Listing Form Modal -->
                                        <div x-show="showListingForm" 
                                             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                                             x-cloak>
                                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                                                <div class="mt-3">
                                                    <h3 class="text-lg font-medium mb-4">List Pack for Sale</h3>
                                                    <form action="{{ route('marketplace.seller.list', $pack) }}" 
                                                          method="POST" 
                                                          @submit.prevent="submitting = true; $el.submit()">
                                                        @csrf
                                                        <div class="mb-4">
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                Price (PULSE)
                                                            </label>
                                                            <div class="relative">
                                                                <input type="number" 
                                                                       name="price" 
                                                                       x-model="price"
                                                                       min="1"
                                                                       max="1000000"
                                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                       required>
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-end space-x-3">
                                                            <button type="button"
                                                                    @click="showListingForm = false"
                                                                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                                                Cancel
                                                            </button>
                                                            <button type="submit"
                                                                    :disabled="!price || submitting"
                                                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50">
                                                                <span x-show="!submitting">List Pack</span>
                                                                <span x-show="submitting">Listing...</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
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
</x-app-layout>
