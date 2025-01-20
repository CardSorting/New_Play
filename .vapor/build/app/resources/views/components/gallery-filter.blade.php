<div class="mb-6">
    <div class="flex items-center space-x-4">
        <button onclick="window.location.href='{{ route('images.gallery') }}'" 
                class="px-4 py-2 text-sm font-medium rounded-md {{ !request('filter') ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All Images
        </button>
        <button onclick="window.location.href='{{ route('images.gallery', ['filter' => 'available']) }}'"
                class="px-4 py-2 text-sm font-medium rounded-md {{ request('filter') === 'available' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Available for Cards
        </button>
        <button onclick="window.location.href='{{ route('images.gallery', ['filter' => 'created']) }}'"
                class="px-4 py-2 text-sm font-medium rounded-md {{ request('filter') === 'created' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Cards Created
        </button>
    </div>
</div>
