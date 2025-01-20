<div>
    <!-- Tabs Navigation -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <nav class="flex" aria-label="Gallery Sections">
            <button onclick="switchTab('images')" 
                    id="images-tab-button"
                    class="flex-1 inline-flex items-center justify-center py-4 px-6 border-b-2 text-sm font-medium focus:outline-none {{ $getTabClasses('images') }}" 
                    data-tab="images"
                    role="tab"
                    aria-selected="{{ $isActive('images') ? 'true' : 'false' }}"
                    aria-controls="images-tab">
                <svg class="mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Generated Images
                <span class="ml-3 bg-gray-100 py-0.5 px-2.5 rounded-full text-xs font-medium text-gray-600 md:inline-block">
                    {{ $imagesCount }}
                </span>
            </button>
            <button onclick="switchTab('cards')" 
                    id="cards-tab-button"
                    class="flex-1 inline-flex items-center justify-center py-4 px-6 border-b-2 text-sm font-medium focus:outline-none {{ $getTabClasses('cards') }}" 
                    data-tab="cards"
                    role="tab"
                    aria-selected="{{ $isActive('cards') ? 'true' : 'false' }}"
                    aria-controls="cards-tab">
                <svg class="mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Created Cards
                <span class="ml-3 bg-gray-100 py-0.5 px-2.5 rounded-full text-xs font-medium text-gray-600 md:inline-block">
                    {{ $cardsCount }}
                </span>
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-8">
        <div id="images-tab" 
             class="tab-content {{ !$isActive('images') ? 'hidden' : '' }}"
             role="tabpanel"
             aria-labelledby="images-tab-button"
             tabindex="0">
            {{ $images }}
        </div>

        <div id="cards-tab" 
             class="tab-content {{ !$isActive('cards') ? 'hidden' : '' }}"
             role="tabpanel"
             aria-labelledby="cards-tab-button"
             tabindex="0">
            {{ $cards }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabName, updateHistory = true) {
        // Update button states
        document.querySelectorAll('[role="tab"]').forEach(tab => {
            const isActive = tab.getAttribute('data-tab') === tabName;
            tab.setAttribute('aria-selected', isActive);
            tab.classList.toggle('border-blue-500', isActive);
            tab.classList.toggle('text-blue-600', isActive);
            tab.classList.toggle('border-transparent', !isActive);
            tab.classList.toggle('text-gray-500', !isActive);
        });

        // Update content visibility
        const activeContent = document.getElementById(`${tabName}-tab`);
        const inactiveContents = Array.from(document.querySelectorAll('.tab-content'))
            .filter(content => content !== activeContent);

        inactiveContents.forEach(content => {
            content.classList.add('hidden');
        });
        activeContent.classList.remove('hidden');
        
        // Reinitialize card effects when switching to cards tab
        if (tabName === 'cards') {
            window.initializeCardEffects?.();
        }

        // Update URL and history if needed
        if (updateHistory) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({tab: tabName}, '', url);
        }

        // Dispatch event
        document.dispatchEvent(new CustomEvent('tabChanged', { detail: { tab: tabName } }));
    }

    // Initialize tab from URL parameter
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'images';
        switchTab(activeTab, false);

        // Handle browser back/forward
        window.addEventListener('popstate', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'images';
            switchTab(activeTab, false);
        });
    });
</script>
@endpush
