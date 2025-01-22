<div class="card-item transform transition-all duration-300 h-full w-full group relative"
     x-on:mouseenter="hoveredCard = $el"
     x-on:mouseleave="hoveredCard = null"
     :class="{ 'z-20': hoveredCard === $el }">
    @if($isNew ?? false)
        <!-- New Card Badge -->
        <div class="absolute -top-2 -right-2 z-30 bg-indigo-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg animate-pulse">
            NEW
        </div>
        <!-- Glow Effect -->
        <div class="absolute inset-0 rounded-2xl bg-indigo-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    @endif
    <!-- Card Container -->
    <div class="card-container relative w-full h-full transition-all duration-300 ease-out"
         style="aspect-ratio: 2.5/3.5;">
        <!-- Simple Shadow System -->
        <div class="absolute inset-4 rounded-2xl transition-all duration-300 ease-out
             before:absolute before:inset-0 before:rounded-2xl before:shadow-[0_4px_15px_rgba(0,0,0,0.2)]
             group-hover:opacity-100">
        </div>
        
        <!-- Card Base -->
        <div class="absolute inset-4 rounded-2xl bg-black/5"></div>
        
        <!-- 3D Space Container -->
        <div class="relative h-full w-full perspective-[2000px]">
            <!-- 3D Transform Container -->
            <div class="relative h-full w-full rounded-2xl overflow-hidden transform-gpu preserve-3d
                        transition-all duration-300 ease-out
                        group-hover:translate-y-[-2px]">
                <livewire:card-display :card="$card" :wire:key="'card-'.$card['name']" />
            </div>
        </div>
    </div>
</div>
