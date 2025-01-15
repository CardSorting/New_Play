import { loadStripe } from '@stripe/stripe-js';

class StripeService {
    constructor() {
        this.stripe = null;
        this.elements = null;
    }

    async initialize(publishableKey) {
        try {
            console.log('Initializing Stripe with key:', publishableKey);
            if (!publishableKey) {
                throw new Error('Stripe publishable key is required');
            }
            
            if (!this.stripe) {
                this.stripe = await loadStripe(publishableKey);
                if (!this.stripe) {
                    throw new Error('Failed to initialize Stripe');
                }
                console.log('Stripe initialized successfully');
            }
            return this.stripe;
        } catch (error) {
            console.error('Error initializing Stripe:', error);
            throw error;
        }
    }

    async createPaymentIntent(cart) {
        try {
            const response = await fetch('/dashboard/api/payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ cart })
            });

            const data = await response.json();
            
            if (!response.ok) {
                console.error('Payment intent creation failed:', {
                    status: response.status,
                    statusText: response.statusText,
                    data
                });
                throw new Error(data.message || 'Failed to create payment intent');
            }

            return data;
        } catch (error) {
            console.error('Error creating payment intent:', {
                error,
                message: error.message,
                stack: error.stack
            });
            throw error;
        }
    }

    async confirmPayment(paymentIntentId, amount) {
        try {
            const response = await fetch(`/dashboard/api/payment-intent/${paymentIntentId}/confirm`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ amount })
            });

            const data = await response.json();
            
            if (!response.ok) {
                console.error('Payment confirmation failed:', {
                    status: response.status,
                    statusText: response.statusText,
                    data
                });
                throw new Error(data.message || 'Failed to confirm payment');
            }

            console.log('Payment confirmation successful:', data);
            return data;
        } catch (error) {
            console.error('Error confirming payment:', {
                error,
                message: error.message,
                stack: error.stack
            });
            throw error;
        }
    }

    async createPaymentElement(clientSecret) {
        if (!this.stripe) {
            throw new Error('Stripe not initialized');
        }

        const elements = this.stripe.elements({
            clientSecret,
            appearance: {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#0d6efd',
                    colorBackground: '#ffffff',
                    colorText: '#30313d',
                    colorDanger: '#df1b41',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                    spacingUnit: '4px',
                    borderRadius: '4px'
                }
            }
        });

        const paymentElement = elements.create('payment');
        return { elements, paymentElement };
    }
}

export default new StripeService();
