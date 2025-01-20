import { loadStripe } from '@stripe/stripe-js';
import { v4 as uuidv4 } from 'uuid';

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
            const idempotencyKey = uuidv4(); // Generate unique key for this payment attempt
            
            const response = await fetch('/dashboard/api/payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    cart,
                    idempotencyKey
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error('Payment intent creation failed:', {
                    status: response.status,
                    statusText: response.statusText,
                    data: errorData
                });
                throw new Error(errorData.message || 'Failed to create payment intent');
            }

            const data = await response.json();
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

            if (!response.ok) {
                const errorData = await response.json();
                console.error('Payment confirmation failed:', {
                    status: response.status,
                    statusText: response.statusText,
                    data: errorData
                });
                throw new Error(errorData.message || 'Failed to confirm payment');
            }

            const data = await response.json();
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
        try {
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
        } catch (error) {
            console.error('Error creating payment element:', error);
            throw error;
        }
    }

    async handlePaymentResult(result, onSuccess) {
        if (result.error) {
            console.error('Payment failed:', result.error);
            throw result.error;
        }

        const { paymentIntent } = result;
        console.log('Payment result:', paymentIntent);

        if (paymentIntent.status === 'succeeded') {
            console.log('Payment succeeded, confirming with backend...');
            try {
                const confirmResult = await this.confirmPayment(
                    paymentIntent.id,
                    paymentIntent.metadata.amount
                );

                if (confirmResult.status === 'COMPLETED') {
                    console.log('Payment fully processed');
                    if (onSuccess) {
                        onSuccess(confirmResult);
                    }
                    return confirmResult;
                }
            } catch (error) {
                console.error('Error during payment confirmation:', error);
                throw error;
            }
        }

        throw new Error(`Payment not completed. Status: ${paymentIntent.status}`);
    }
}

export default new StripeService();
