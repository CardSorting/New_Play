import stripeService from '../services/stripe-service';

class StripePayment {
    constructor(container, options) {
        this.container = container;
        this.amount = options.amount;
        this.price = options.price;
        this.stripeKey = options.stripeKey;
        this.paymentElement = null;
        this.elements = null;
        this.stripe = null;
        this.form = null;
        this.submitButton = null;
        this.errorElement = null;
        
        this.initialize();
    }

    async initialize() {
        try {
            // Initialize Stripe
            this.stripe = await stripeService.initialize(this.stripeKey);
            
            // Create container elements
            this.createFormElements();

            // Format cart data correctly
            const cart = [{
                id: 'credits',
                quantity: parseInt(this.amount, 10),
                price: parseFloat(this.price)
            }];
            
            // Create payment intent
            const { clientSecret } = await stripeService.createPaymentIntent(cart);

            // Create and mount payment element
            const { elements, paymentElement } = await stripeService.createPaymentElement(clientSecret);
            this.elements = elements;
            this.paymentElement = paymentElement;
            
            // Mount payment element
            this.paymentElement.mount('#payment-element');
            
            // Setup form submission
            this.setupFormSubmission();
        } catch (error) {
            console.error('Failed to initialize Stripe payment:', error);
            this.showError('Failed to initialize payment form. Please try again.');
        }
    }

    createFormElements() {
        // Create form container
        this.form = document.createElement('form');
        this.form.id = 'payment-form';
        this.form.className = 'space-y-4';

        // Create payment element container
        const paymentContainer = document.createElement('div');
        paymentContainer.id = 'payment-element';
        this.form.appendChild(paymentContainer);

        // Create error message container
        this.errorElement = document.createElement('div');
        this.errorElement.id = 'payment-error';
        this.errorElement.className = 'text-red-600 text-sm hidden';
        this.form.appendChild(this.errorElement);

        // Create submit button
        this.submitButton = document.createElement('button');
        this.submitButton.type = 'submit';
        this.submitButton.className = 'w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50';
        this.submitButton.textContent = `Pay $${this.price}`;
        this.form.appendChild(this.submitButton);

        // Add form to container
        this.container.appendChild(this.form);
    }

    setupFormSubmission() {
        this.form.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            this.setLoading(true);
            this.hideError();

            try {
                const { error, paymentIntent } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    confirmParams: {
                        return_url: window.location.href,
                    },
                    redirect: 'if_required'
                });

                if (error) {
                    this.showError(error.message);
                    return;
                }

                console.log('Payment intent confirmation result:', paymentIntent);

                if (paymentIntent.status === 'succeeded') {
                    console.log('Payment succeeded, confirming with backend...');
                    try {
                        const result = await stripeService.confirmPayment(
                            paymentIntent.id,
                            this.amount
                        );

                        console.log('Backend confirmation result:', result);

                        if (result.status === 'COMPLETED') {
                            console.log('Credits added successfully, reloading page...');
                            window.location.reload();
                        } else if (result.error === 'Authentication required') {
                            console.log('User needs to log in to complete purchase');
                            // Store payment intent ID in session storage
                            sessionStorage.setItem('pendingPaymentIntent', paymentIntent.id);
                            // Redirect to login page with return URL
                            window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                        } else {
                            console.error('Backend confirmation failed:', result);
                            this.showError(result.message || 'Payment completed but credits could not be added.');
                        }
                    } catch (confirmError) {
                        console.error('Error confirming payment with backend:', confirmError);
                        this.showError('Error confirming payment. Please contact support.');
                    }
                } else {
                    console.error('Payment intent not succeeded:', paymentIntent);
                    this.showError(`Payment not completed. Status: ${paymentIntent.status}`);
                }
            } catch (error) {
                console.error('Payment process failed:', error);
                if (error.response) {
                    console.error('Error response:', await error.response.text());
                }
                this.showError('Payment failed. Please try again.');
            } finally {
                this.setLoading(false);
            }
        });
    }

    showError(message) {
        this.errorElement.textContent = message;
        this.errorElement.classList.remove('hidden');
    }

    hideError() {
        this.errorElement.textContent = '';
        this.errorElement.classList.add('hidden');
    }

    setLoading(isLoading) {
        this.submitButton.disabled = isLoading;
        this.submitButton.textContent = isLoading ? 'Processing...' : `Pay $${this.price}`;
    }
}

export default StripePayment;
