<div id="cardDetailsModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50"
     role="dialog"
     aria-labelledby="cardModalTitle"
     aria-modal="true">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 relative shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 id="cardModalTitle" class="text-2xl font-bold text-gray-900">Card Details</h2>
            <button onclick="CardDetailsModal.close()" 
                    class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Card Preview -->
            <div class="rounded-lg overflow-hidden">
                <div id="modalCardDisplay" class="w-full aspect-[2.5/3.5] relative">
                    <!-- Card will be rendered here -->
                </div>
            </div>
            <!-- Card Details -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Card Name</h3>
                    <p id="modalCardName" class="text-lg font-semibold text-gray-900"></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Type</h3>
                    <p id="modalCardType" class="text-gray-900"></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Rarity</h3>
                    <span id="modalCardRarity" class="px-3 py-1 text-sm font-medium rounded-full"></span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Author</h3>
                    <p id="modalCardAuthor" class="text-gray-900"></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Abilities</h3>
                    <p id="modalCardAbilities" class="text-gray-900 whitespace-pre-line"></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Stats</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 mb-1">Power</p>
                            <p id="modalCardPower" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 mb-1">Toughness</p>
                            <p id="modalCardToughness" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const CardDetailsModal = {
        modal: null,
        cardDisplay: null,
        elements: {},

        initialize() {
            this.modal = document.getElementById('cardDetailsModal');
            this.cardDisplay = document.getElementById('modalCardDisplay');
            
            // Cache DOM elements
            ['Name', 'Type', 'Rarity', 'Author', 'Abilities', 'Power', 'Toughness'].forEach(field => {
                this.elements[field.toLowerCase()] = document.getElementById(`modalCard${field}`);
            });

            // Close modal when clicking outside
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.close();
                }
            });

            // Close modal with escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.close();
                }
            });
        },

        show(card) {
            // Update card display
            this.cardDisplay.innerHTML = '';
            const cardElement = document.querySelector(`[data-name="${card.name.toLowerCase()}"] .mtg-card`).cloneNode(true);
            this.cardDisplay.appendChild(cardElement);

            // Initialize 3D effect if applicable
            if (card.rarity === 'rare' || card.rarity === 'mythic-rare') {
                new MTGCard3DTiltEffect(cardElement);
            }

            // Update details
            this.elements.name.textContent = card.name;
            this.elements.type.textContent = card.type;
            this.elements.author.textContent = card.author || 'Not available';
            
            // Update rarity with appropriate styling
            const rarityClasses = {
                'mythic-rare': 'bg-orange-100 text-orange-800',
                'rare': 'bg-yellow-100 text-yellow-800',
                'uncommon': 'bg-gray-100 text-gray-800',
                'common': 'bg-gray-100 text-gray-600'
            };
            this.elements.rarity.className = `px-3 py-1 text-sm font-medium rounded-full ${rarityClasses[card.rarity]}`;
            this.elements.rarity.textContent = card.rarity.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

            // Update abilities and stats
            this.elements.abilities.textContent = card.abilities || 'None';
            this.elements.power.textContent = card.power || 'N/A';
            this.elements.toughness.textContent = card.toughness || 'N/A';

            // Show modal
            this.modal.classList.remove('hidden');
            this.modal.classList.add('flex');
            
            // Focus first focusable element
            const focusableElements = this.modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstFocusableElement = focusableElements[0];
            firstFocusableElement.focus();
        },

        close() {
            this.modal.classList.add('hidden');
            this.modal.classList.remove('flex');
        }
    };

    // Initialize modal when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        CardDetailsModal.initialize();
    });

    // Make showCardDetails function globally available
    window.showCardDetails = (cardName) => {
        const cardElement = document.querySelector(`[data-name="${cardName.toLowerCase()}"]`);
        if (!cardElement) return;

        const card = {
            name: cardName,
            type: cardElement.dataset.type,
            rarity: cardElement.dataset.rarity,
            author: cardElement.dataset.author,
            abilities: cardElement.querySelector('.abilities-text')?.textContent,
            power: cardElement.querySelector('.power')?.textContent,
            toughness: cardElement.querySelector('.toughness')?.textContent
        };

        CardDetailsModal.show(card);
    };
</script>
@endpush
