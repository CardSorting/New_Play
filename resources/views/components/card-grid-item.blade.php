<div class="card-item w-[300px] h-[420px] relative">
    @if($isNew ?? false)
        <!-- New Card Badge -->
        <div class="absolute -top-2 -right-2 z-30 bg-indigo-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
            NEW
        </div>
    @endif
    
    <!-- Simplified Card Container -->
    <div class="relative w-full h-full">
        <livewire:card-display :card="$card" :wire:key="'card-'.$card['name']" />
    </div>
</div>
