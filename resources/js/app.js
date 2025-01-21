import './bootstrap';
// Import and expose 3D card effect initialization first
import { initializeMTGCard3DEffect } from './effects/mtg-card-3d-effect';
window.initializeMTGCard3DEffect = initializeMTGCard3DEffect;

import Alpine from 'alpinejs';
import StripePayment from './components/stripe-payment';

// Initialize Alpine.js
if (!window.Alpine) {
    window.Alpine = Alpine;
    
    // Start Alpine after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Alpine.start());
    } else {
        Alpine.start();
    }
}

// Export components
window.StripePayment = StripePayment;
window.initializeMTGCard3DEffect = initializeMTGCard3DEffect;
