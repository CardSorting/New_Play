<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="image-container relative h-[45%] w-full cursor-pointer rounded-lg overflow-hidden mx-2 mt-2 mb-2"
         onclick="ImageDetailsModal.show('{{ addslashes($image->prompt) }}', '{{ $image->image_url }}', '{{ $image->aspect_ratio }}', '{{ $image->process_mode }}', '{{ $image->task_id }}', '{{ $image->created_at_for_humans }}', {{ json_encode($image->metadata) }})"
         role="button"
         tabindex="0"
         aria-label="View details for image: {{ $image->prompt }}">
        <img src="{{ $image->image_url }}" 
             alt="{{ $image->prompt }}"
             class="w-full h-full object-cover object-center rounded-lg border border-[#171314]">
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-white/10 rounded-lg">
            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-200">
                <div class="absolute inset-0 flex items-center justify-center p-6">
                    <p class="text-white text-center line-clamp-3 text-shadow-lg font-medium">{{ $image->prompt }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="p-2 bg-gray-50 border-t">
        @php
            $hasCard = \App\Models\Gallery::where('type', 'card')
                ->where('image_url', $image->image_url)
                ->exists() || 
            \App\Models\GlobalCard::where('image_url', $image->image_url)
                ->exists();
        @endphp
        @if($hasCard)
            <div class="flex items-center justify-center px-3 py-1.5 bg-gray-200 text-gray-500 text-sm font-medium rounded cursor-not-allowed"
                 aria-label="Card already created for this image">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Card Created</span>
            </div>
        @else
            <a href="{{ route('cards.create', ['image' => $image->id]) }}"
               class="flex items-center justify-center px-3 py-1.5 bg-purple-500 text-black text-sm font-medium rounded hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
               aria-label="Create card from this image">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Create Card</span>
            </a>
        @endif
    </div>
</div>
