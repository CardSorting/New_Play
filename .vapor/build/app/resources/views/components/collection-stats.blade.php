<div class="space-y-3">
    @foreach($stats as $key => $stat)
        <div class="bg-gradient-to-r {{ $stat['gradient'] }} p-3 rounded-lg hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $stat['dot'] }}"></div>
                        <p class="text-{{ $stat['color'] }}-600 text-sm font-medium">{{ $stat['label'] }}</p>
                    </div>
                    <h4 class="text-xl font-bold text-{{ $stat['color'] }}-800 mt-1">{{ $stat['count'] }}</h4>
                </div>
                <div class="text-{{ $stat['color'] }}-500 opacity-25">
                    @switch($key)
                        @case('total')
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 7h16v2H4V7zm0 6h16v-2H4v2zm0 4h16v-2H4v2z"/>
                            </svg>
                            @break
                        @case('mythic')
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L1 21h22L12 2zm0 4.5l7.5 13H4.5L12 6.5z"/>
                            </svg>
                            @break
                        @case('rare')
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                            </svg>
                            @break
                        @default
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                            </svg>
                    @endswitch
                </div>
            </div>
        </div>
    @endforeach

    @if($slot->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-gray-200/50">
            {{ $slot }}
        </div>
    @endif
</div>

@once
    @push('styles')
    <style>
        .text-gold-600 { color: #B7791F; }
        .text-gold-800 { color: #975A16; }
        .bg-gold-400 { background-color: #D69E2E; }
    </style>
    @endpush
@endonce
