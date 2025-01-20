class GalleryService {
    constructor() {
        this.masonryService = new MasonryService();
        this.filterService = new FilterService();
        this.sortService = new SortService();
        this.viewService = new ViewService();
        this.activeTab = 'images';
    }

    initialize() {
        // Initialize services in the correct order
        this.masonryService.initialize();
        this.filterService.initialize(this.masonryService);
        this.sortService.initialize(this.masonryService);
        this.viewService.initialize(this.masonryService, this.filterService);

        // Set up event listeners
        this.setupEventListeners();

        // Load saved preferences
        this.loadPreferences();

        // Initialize tab state
        this.initializeTabState();

        // Dispatch ready event
        window.dispatchEvent(new CustomEvent('gallery:ready'));
    }

    setupEventListeners() {
        // Handle tab switching
        document.addEventListener('tabChanged', (e) => {
            this.activeTab = e.detail.tab;
            if (this.activeTab === 'cards') {
                // Reinitialize masonry when switching to cards tab
                this.masonryService.layout();
                this.masonryService.initializeCardEffects();
            }
        });

        // Handle browser back/forward
        window.addEventListener('popstate', (event) => {
            if (event.state) {
                if (event.state.tab) {
                    this.switchTab(event.state.tab, false);
                }
                if (event.state.view) {
                    this.viewService.switchView(event.state.view, false);
                }
            }
        });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (this.activeTab === 'cards' && this.viewService.getCurrentView() === 'grid') {
                    this.masonryService.layout();
                }
            }, 250);
        });

        // Handle filter changes
        document.addEventListener('filterChanged', () => {
            if (this.activeTab === 'cards') {
                this.filterService.applyFilters();
            }
        });

        // Handle sort changes
        document.addEventListener('sortChanged', () => {
            if (this.activeTab === 'cards') {
                this.sortService.sortCards();
            }
        });
    }

    loadPreferences() {
        try {
            // Load view preference
            this.viewService.loadViewPreference();

            // Load sort preference
            const savedSort = JSON.parse(localStorage.getItem('cardSortPreference'));
            if (savedSort && savedSort.field) {
                this.sortService.setSort(savedSort.field, savedSort.ascending);
            }

            // Load filter preferences
            const savedFilters = JSON.parse(localStorage.getItem('cardFilterPreferences'));
            if (savedFilters) {
                if (savedFilters.rarity) {
                    this.filterService.filterByRarity(savedFilters.rarity);
                }
                if (savedFilters.type) {
                    this.filterService.filterByType(savedFilters.type);
                }
            }

            // Load tab preference
            const savedTab = localStorage.getItem('activeTab');
            if (savedTab) {
                this.switchTab(savedTab, false);
            }
        } catch (e) {
            console.warn('Could not load preferences:', e);
        }
    }

    initializeTabState() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabFromUrl = urlParams.get('tab');
        if (tabFromUrl) {
            this.switchTab(tabFromUrl, false);
        }
    }

    switchTab(tabName, updateHistory = true) {
        const tabs = ['images', 'cards'];
        if (!tabs.includes(tabName)) return;

        this.activeTab = tabName;
        
        // Update UI
        document.querySelectorAll('[role="tab"]').forEach(tab => {
            const isActive = tab.getAttribute('data-tab') === tabName;
            tab.setAttribute('aria-selected', isActive);
            tab.classList.toggle('border-blue-500', isActive);
            tab.classList.toggle('text-blue-600', isActive);
            tab.classList.toggle('border-transparent', !isActive);
            tab.classList.toggle('text-gray-500', !isActive);
        });

        // Update content visibility
        document.querySelectorAll('[role="tabpanel"]').forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== `${tabName}-tab`);
        });

        // Initialize masonry if switching to cards tab
        if (tabName === 'cards') {
            this.masonryService.layout();
            this.masonryService.initializeCardEffects();
        }

        // Update URL and history if needed
        if (updateHistory) {
            const url = new URL(window.location);
            // Preserve existing query parameters
            const existingParams = new URLSearchParams(window.location.search);
            const newParams = new URLSearchParams();
            
            // Copy all existing parameters except 'tab'
            for (const [key, value] of existingParams.entries()) {
                if (key !== 'tab') {
                    newParams.append(key, value);
                }
            }
            
            // Add the new tab parameter
            newParams.set('tab', tabName);
            
            // Update URL with all parameters
            url.search = newParams.toString();
            window.history.pushState({ tab: tabName }, '', url);
        }

        // Save preference
        try {
            localStorage.setItem('activeTab', tabName);
        } catch (e) {
            console.warn('Could not save tab preference:', e);
        }

        // Dispatch event
        document.dispatchEvent(new CustomEvent('tabChanged', { detail: { tab: tabName } }));
    }

    reset() {
        this.filterService.reset();
        this.sortService.setSort('name', true);
        this.viewService.switchView('grid');
        
        // Clear saved preferences
        try {
            localStorage.removeItem('cardViewPreference');
            localStorage.removeItem('cardSortPreference');
            localStorage.removeItem('cardFilterPreferences');
            localStorage.removeItem('activeTab');
        } catch (e) {
            console.warn('Could not clear preferences:', e);
        }
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GalleryService;
}

// Make available globally
window.GalleryService = GalleryService;

// Initialize gallery service when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.galleryService = new GalleryService();
    window.galleryService.initialize();
});
