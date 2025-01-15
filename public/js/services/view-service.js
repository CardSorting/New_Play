class ViewService {
    constructor() {
        this.currentView = 'grid';
        this.masonryService = null;
        this.filterService = null;
    }

    initialize(masonryService, filterService) {
        this.masonryService = masonryService;
        this.filterService = filterService;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.currentTarget.getAttribute('data-view');
                if (view) this.switchView(view);
            });
        });
    }

    switchView(view) {
        if (view !== 'grid' && view !== 'list') return;

        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const viewButtons = document.querySelectorAll('.view-btn');
        
        this.currentView = view;
        
        // Update button states
        viewButtons.forEach(btn => {
            const isActive = btn.getAttribute('data-view') === view;
            btn.classList.toggle('active', isActive);
            btn.classList.toggle('bg-gray-100', isActive);
        });
        
        // First set opacity to 0 for smooth transition if elements exist
        if (gridView) gridView.style.opacity = '0';
        if (listView) listView.style.opacity = '0';

        // Exit early if required elements don't exist
        if (!gridView || !listView) {
            console.warn('Required view elements not found');
            return;
        }

        // Short delay to ensure opacity transition is visible
        setTimeout(() => {
            if (view === 'grid') {
                // Show grid view
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                // Update card item display for grid view
                document.querySelectorAll('.card-item').forEach(item => {
                    item.style.display = 'block';
                });
                // Reinitialize masonry layout
                if (this.masonryService) {
                    this.masonryService.layout();
                }
                // Fade in grid view
                gridView.style.opacity = '1';
            } else {
                // Show list view
                gridView.classList.add('hidden');
                listView.classList.remove('hidden');
                // Update card item display for list view
                document.querySelectorAll('.card-item').forEach(item => {
                    item.style.display = 'flex';
                });
                // Fade in list view
                listView.style.opacity = '1';
            }
        }, 150);

        // Update filter service view state
        if (this.filterService) {
            this.filterService.setView(view);
        }

        // Store preference
        this.saveViewPreference(view);
    }

    saveViewPreference(view) {
        try {
            localStorage.setItem('cardViewPreference', view);
        } catch (e) {
            console.warn('Could not save view preference:', e);
        }
    }

    loadViewPreference() {
        try {
            const savedView = localStorage.getItem('cardViewPreference');
            if (savedView && (savedView === 'grid' || savedView === 'list')) {
                this.switchView(savedView);
            }
        } catch (e) {
            console.warn('Could not load view preference:', e);
        }
    }

    getCurrentView() {
        return this.currentView;
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ViewService;
}

// Make available globally
window.ViewService = ViewService;
