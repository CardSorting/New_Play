<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12">
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-{{ $getBackgroundColor() }}-50 rounded-lg mb-6">
            <svg class="w-10 h-10 text-{{ $getBackgroundColor() }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $title }}</h3>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">{{ $message }}</p>
        @if($actionRoute && $actionText)
            <a href="{{ route($actionRoute) }}" 
               class="inline-flex items-center px-6 py-3 bg-{{ $getBackgroundColor() }}-500 text-white font-medium rounded-lg hover:bg-{{ $getBackgroundColor() }}-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $getBackgroundColor() }}-500">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ $actionText }}
            </a>
        @endif
    </div>
</div>
