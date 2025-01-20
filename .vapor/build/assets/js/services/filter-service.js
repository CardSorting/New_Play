class FilterService {
    constructor() {
        this.currentRarityFilter = 'all';
        this.currentTypeFilter = 'all';
        this.searchQuery = '';
        this.masonryService = null;
        this.currentView = 'grid';
    }

    initialize(masonryService) {
        this.masonryService = masonryService;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Search input
        const searchInput = document.getElementById('cardSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        }

        // Rarity filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const rarity = e.target.getAttribute('data-rarity');
                if (rarity) this.filterByRarity(rarity);
            });
        });

        // Type filter buttons
        document.querySelectorAll('.type-filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const type = e.target.getAttribute('data-type');
                if (type) this.filterByType(type);
            });
        });
    }

    handleSearch(query) {
        this.searchQuery = query.toLowerCase();
        this.applyFilters();
    }

    filterByRarity(rarity) {
        this.currentRarityFilter = rarity;
        this.updateFilterButtons('filter-btn', rarity);
        this.applyFilters();
    }

    filterByType(type) {
        this.currentTypeFilter = type;
        this.updateFilterButtons('type-filter-btn', type);
        this.applyFilters();
    }

    updateFilterButtons(buttonClass, value) {
        document.querySelectorAll('.' + buttonClass).forEach(btn => {
            btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        
        const activeBtn = document.querySelector(`[data-${buttonClass === 'filter-btn' ? 'rarity' : 'type'}="${value}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
        }
    }

    applyFilters() {
        document.querySelectorAll('.card-item').forEach(card => {
            const name = card.dataset.name;
            const type = card.dataset.type;
            const rarity = card.dataset.rarity;

            const matchesSearch = !this.searchQuery || 
                name.includes(this.searchQuery) || 
                type.includes(this.searchQuery);

            const matchesRarity = this.currentRarityFilter === 'all' || 
                rarity === this.currentRarityFilter;

            const matchesType = this.currentTypeFilter === 'all' || 
                type.includes(this.currentTypeFilter);

            const shouldShow = matchesSearch && matchesRarity && matchesType;
            
            card.style.display = shouldShow ? 
                (this.currentView === 'grid' ? 'block' : 'flex') : 
                'none';
        });

        // Update masonry layout if in grid view
        if (this.currentView === 'grid' && this.masonryService) {
            this.masonryService.layout();
        }
    }

    setView(view) {
        this.currentView = view;
        this.applyFilters();
    }

    reset() {
        this.currentRarityFilter = 'all';
        this.currentTypeFilter = 'all';
        this.searchQuery = '';
        this.applyFilters();
        
        // Reset UI
        document.querySelectorAll('.filter-btn, .type-filter-btn').forEach(btn => {
            if (btn.getAttribute('data-rarity') === 'all' || btn.getAttribute('data-type') === 'all') {
                btn.classList.add('active', 'bg-blue-100', 'text-blue-800');
                btn.classList.remove('bg-gray-100', 'text-gray-700');
            } else {
                btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            }
        });

        const searchInput = document.getElementById('cardSearch');
        if (searchInput) searchInput.value = '';
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FilterService;
}

// Make available globally
window.FilterService = FilterService;
