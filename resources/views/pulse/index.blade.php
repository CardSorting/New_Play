<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold mb-2">Purchase Pulse</h2>
                        <p class="text-gray-600">Get {{ number_format($amount) }} Pulse for ${{ number_format($price, 2) }}</p>
                    </div>

                    <!-- Stripe Payment Container -->
                    <div id="stripe-payment-container" class="max-w-md mx-auto"></div>

                    <!-- Result Message -->
                    <div id="result-message" class="mt-4 text-center text-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <!-- Load Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
    
    <script type="module">
        function initializeStripePayment() {
            try {
                const container = document.getElementById('stripe-payment-container');
                const amount = {{ $amount }};
                const price = {{ $price }};
                const stripeKey = '{{ $stripeKey }}';
                
                console.log('Initializing Stripe payment with:', { amount, price, stripeKey });
                
                if (!window.StripePayment) {
                    console.error('StripePayment class not found. Available globals:', Object.keys(window));
                    return;
                }
                
                new window.StripePayment(container, {
                    amount,
                    price,
                    stripeKey
                });
            } catch (error) {
                console.error('Error initializing Stripe payment:', error);
            }
        }

        // Try to initialize after both DOM and scripts are loaded
        if (document.readyState === 'complete') {
            initializeStripePayment();
        } else {
            window.addEventListener('load', initializeStripePayment);
        }
    </script>
@endpush

</x-app-layout>
