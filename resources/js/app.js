import './bootstrap';
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

// Export Stripe components
window.StripePayment = StripePayment;
