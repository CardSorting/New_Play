class MasonryService {
    constructor(gridSelector = '.cards-masonry') {
        this.grid = document.querySelector(gridSelector);
        this.instance = null;
        this.options = {
            itemSelector: '.card-item',
            columnWidth: '.grid-sizer',
            percentPosition: true,
            transitionDuration: '0.3s',
            gutter: 0,
            horizontalOrder: true,
            initLayout: true
        };
    }

    initialize() {
        if (!this.grid) return;

        // Wait for images to load
        imagesLoaded(this.grid, () => {
            this.instance = new Masonry(this.grid, this.options);
            
            // Show grid after layout is complete
            this.grid.style.opacity = '1';
            
            // Layout again after a short delay to ensure proper positioning
            setTimeout(() => {
                this.layout();
                // Initialize 3D effects for cards after layout is complete
                this.initializeCardEffects();
            }, 100);
        });
    }

    layout() {
        if (!this.instance) return;
        this.instance.layout();
    }

    reloadItems() {
        if (!this.instance) return;
        this.instance.reloadItems();
    }

    destroy() {
        if (!this.instance) return;
        this.instance.destroy();
    }

    initializeCardEffects() {
        const cards = document.querySelectorAll('.mtg-card');
        cards.forEach(card => {
            if (card.closest('[data-rarity="Rare"]') || card.closest('[data-rarity="Mythic Rare"]')) {
                new MTGCard3DTiltEffect(card);
            }
        });
    }

    updateLayout(items) {
        if (!this.instance) return;
        
        items.forEach(item => this.grid.appendChild(item));
        this.reloadItems();
        this.layout();
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MasonryService;
}

// Make available globally
window.MasonryService = MasonryService;
