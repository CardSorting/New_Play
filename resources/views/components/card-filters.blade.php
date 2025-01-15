<div class="space-y-6">
    <!-- Search Bar -->
    <div>
        <div class="relative">
            <input type="text" 
                   id="cardSearch" 
                   placeholder="Search cards..." 
                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-white/50 backdrop-blur-sm
                          focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Sort Controls -->
    <div class="flex items-center gap-3 bg-white/50 backdrop-blur-sm rounded-lg p-2">
        <select id="sort" 
                class="flex-1 text-sm rounded-md border-gray-300 bg-transparent focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                data-sort-control>
            <option value="name">Sort by Name</option>
            <option value="rarity">Sort by Rarity</option>
            <option value="type">Sort by Type</option>
        </select>
        <button id="sortDirection"
                class="p-2 rounded-md hover:bg-purple-50 transition-colors duration-200">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
        </button>
    </div>

    <!-- Filter Controls -->
    <div class="space-y-4">
        <!-- Rarity Filters -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                Rarity
            </h4>
            <div class="flex flex-col gap-2">
                @foreach($rarityFilters as $key => $filter)
                    <button class="filter-btn w-full px-3 py-2 rounded-lg text-sm font-medium text-left
                                 transition-all duration-200 hover:translate-x-1
                                 {{ $filter['bg'] }} {{ $filter['text'] }} {{ $filter['hover'] }} 
                                 {{ $key === 'all' ? 'active' : '' }}"
                            data-rarity="{{ $key }}">
                        {{ $filter['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Type Filters -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Card Type
            </h4>
            <div class="flex flex-col gap-2">
                @foreach($typeFilters as $key => $filter)
                    <button class="type-filter-btn w-full px-3 py-2 rounded-lg text-sm font-medium text-left
                                 transition-all duration-200 hover:translate-x-1
                                 {{ $filter['bg'] }} {{ $filter['text'] }} {{ $filter['hover'] }} 
                                 {{ $key === 'all' ? 'active' : '' }}"
                            data-type="{{ $key }}">
                        {{ $filter['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
