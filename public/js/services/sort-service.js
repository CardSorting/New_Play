class SortService {
    constructor() {
        this.currentSort = {
            field: 'name',
            ascending: true
        };
        this.masonryService = null;
    }

    initialize(masonryService) {
        this.masonryService = masonryService;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Sort select with data attribute
        const sortSelect = document.querySelector('[data-sort-control]');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.currentSort.field = e.target.value;
                this.sortCards();
            });
        }

        // Sort direction button with ID
        const sortDirectionButton = document.getElementById('sortDirection');
        if (sortDirectionButton) {
            sortDirectionButton.addEventListener('click', () => this.toggleSortDirection());
        }
    }

    toggleSortDirection() {
        this.currentSort.ascending = !this.currentSort.ascending;
        this.sortCards();
    }

    getRarityWeight(rarity) {
        const weights = {
            'mythic-rare': 4,
            'rare': 3,
            'uncommon': 2,
            'common': 1
        };
        return weights[rarity] || 0;
    }

    getTypeWeight(type) {
        const weights = {
            'creature': 5,
            'planeswalker': 4,
            'instant': 3,
            'sorcery': 2,
            'artifact': 1,
            'enchantment': 0
        };
        return weights[type] || 0;
    }

    compareValues(a, b) {
        if (this.currentSort.field === 'rarity') {
            a = this.getRarityWeight(a);
            b = this.getRarityWeight(b);
        } else if (this.currentSort.field === 'type') {
            a = this.getTypeWeight(a);
            b = this.getTypeWeight(b);
        }

        if (typeof a === 'number' && typeof b === 'number') {
            return this.currentSort.ascending ? a - b : b - a;
        }

        return this.currentSort.ascending ? 
            String(a).localeCompare(String(b)) : 
            String(b).localeCompare(String(a));
    }

    sortCards() {
        const grid = document.querySelector('.cards-masonry');
        if (!grid) return;

        const items = Array.from(grid.children);
        
        items.sort((a, b) => {
            let aVal = a.dataset[this.currentSort.field];
            let bVal = b.dataset[this.currentSort.field];
            
            return this.compareValues(aVal, bVal);
        });

        // Update DOM and masonry layout
        if (this.masonryService) {
            this.masonryService.updateLayout(items);
        } else {
            items.forEach(item => grid.appendChild(item));
        }

        // Update sort direction indicator
        this.updateSortDirectionIndicator();
    }

    updateSortDirectionIndicator() {
        const button = document.getElementById('sortDirection');
        if (!button) return;

        // Update button appearance
        button.classList.toggle('bg-purple-50', !this.currentSort.ascending);
        
        const svg = button.querySelector('svg');
        if (!svg) return;

        // Update SVG path to indicate sort direction
        const path = svg.querySelector('path');
        if (path) {
            path.setAttribute('d', this.currentSort.ascending
                ? 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4'  // Up/Down arrows
                : 'M7 4v12m0 0l4-4m-4 4l-4-4m14-8V4m0 0l-4 4m4-4l4 4' // Down/Up arrows
            );
        }

        // Update aria-label for accessibility
        button.setAttribute('aria-label', 
            this.currentSort.ascending ? 'Sort Descending' : 'Sort Ascending'
        );
    }

    getCurrentSort() {
        return { ...this.currentSort };
    }

    setSort(field, ascending = true) {
        this.currentSort.field = field;
        this.currentSort.ascending = ascending;
        this.sortCards();
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SortService;
}

// Make available globally
window.SortService = SortService;
