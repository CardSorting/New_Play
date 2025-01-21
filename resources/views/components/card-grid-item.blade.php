<div class="card-item transform transition-all duration-500 h-full w-full group"
     x-on:mouseenter="hoveredCard = $el"
     x-on:mouseleave="hoveredCard = null"
     :class="{ 'z-20': hoveredCard === $el }">
    <!-- Enhanced Card Container with Advanced Lighting Effects -->
        <div class="card-container relative w-full h-full transition-all duration-300 ease-out p-4"
         style="aspect-ratio: 2.5/3.5; min-height: 300px;">
        <!-- Multi-layered Shadow System -->
        <div class="absolute inset-8 rounded-2xl transition-all duration-300 ease-out opacity-90
             before:absolute before:inset-0 before:rounded-2xl before:shadow-[0_8px_30px_rgba(0,0,0,0.3),0_0_60px_rgba(0,0,0,0.2)]
             after:absolute after:inset-0 after:rounded-2xl after:shadow-[0_4px_15px_rgba(0,0,0,0.2),0_0_30px_rgba(0,0,0,0.15)]
             group-hover:opacity-100">
        </div>
        
        <!-- Card Base Shadow -->
        <div class="absolute inset-8 rounded-2xl bg-black/5 blur-xl transform translate-y-1"></div>
        
        <!-- Card Edge Shadow -->
        <div class="absolute -inset-1 rounded-2xl opacity-50 group-hover:opacity-70 transition-opacity duration-300">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-black/[0.07] to-black/[0.1]"></div>
        </div>
        
        <!-- Enhanced Ambient Light Effects -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
            <!-- Top Light -->
            <div class="absolute inset-x-0 top-0 h-1/3 bg-gradient-to-b from-white/10 to-transparent"></div>
            <!-- Side Lights -->
            <div class="absolute inset-y-0 left-0 w-1/3 bg-gradient-to-r from-white/5 to-transparent"></div>
            <div class="absolute inset-y-0 right-0 w-1/3 bg-gradient-to-l from-white/5 to-transparent"></div>
            <!-- Bottom Shadow -->
            <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black/10 to-transparent"></div>
        </div>
        
        <!-- Enhanced 3D Space Container with Improved Depth -->
        <div class="relative h-full w-full perspective-[2000px]">
            <!-- 3D Transform Container with Enhanced Animations and Depth -->
            <div class="relative h-full w-full rounded-2xl overflow-hidden transform-gpu preserve-3d
                        transition-all duration-300 ease-out
                        group-hover:scale-[1.02] group-hover:translate-y-[-4px]
                        before:absolute before:inset-0 before:bg-gradient-to-br before:from-white/10 before:to-black/5 before:opacity-0 before:group-hover:opacity-100 before:transition-opacity before:duration-300
                        after:absolute after:inset-0 after:bg-black/5 after:opacity-0 after:group-hover:opacity-100 after:transition-opacity after:duration-300">
                <livewire:card-display :card="$card" :wire:key="'card-'.$card['name']" />
            </div>
        </div>
    </div>
</div>
