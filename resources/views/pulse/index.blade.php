<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div 
                    x-data="pulseComponent({
                        initialBalance: {{ $creditBalance }},
                        nextClaimTime: '{{ $nextClaimTime }}',
                        canClaim: {{ $canClaim ? 'true' : 'false' }},
                        amount: {{ $amount }}
                    })"
                    x-init="init"
                    class="p-6 text-gray-900"
                >
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold mb-2">Daily Pulse</h2>
                        <p class="text-gray-600 mb-4">Claim <span x-text="amount.toLocaleString()"></span> Pulse for free every day!</p>
                        
                        <div class="mb-6">
                            <p class="text-lg font-medium">Current Balance: <span x-text="balance.toLocaleString()"></span> Pulse</p>
                        </div>

                        <template x-if="canClaim">
                            <button
                                @click="claimPulse"
                                :disabled="loading || claimed"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-show="loading" class="inline-block mr-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                Claim Daily Pulse
                            </button>
                        </template>

                        <template x-if="!canClaim">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-600">Next claim available at:</p>
                                <p class="text-lg font-medium" x-text="nextClaimTime"></p>
                            </div>
                        </template>

                        <!-- Status and Messages -->
                        <div class="mt-4">
                            <template x-if="claimed">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-600">Next claim available at:</p>
                                    <p class="text-lg font-medium" x-text="nextClaimTime"></p>
                                </div>
                            </template>
                            <p x-show="message" x-text="message" class="text-green-600 mt-2"></p>
                            <p x-show="errorMessage" x-text="errorMessage" class="text-red-600 mt-2"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pulseComponent(config) {
            return {
                loading: false,
                claimed: false,
                message: '',
                errorMessage: '',
                amount: config.amount,
                balance: config.initialBalance,
                canClaim: config.canClaim,
                nextClaimTime: config.nextClaimTime,

                init() {
                    console.log('Initializing pulse component');
                },

                async claimPulse() {
                    if (this.loading || this.claimed) return;
                    
                    this.loading = true;
                    this.message = '';
                    this.errorMessage = '';
                    
                    try {
                        const response = await fetch('{{ route('pulse.claim') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.message = 'Successfully claimed daily pulse!';
                            this.balance = data.new_balance;
                            this.claimed = true;
                            this.canClaim = false;
                            
                            // Update next claim time
                            const nextDate = new Date();
                            nextDate.setHours(nextDate.getHours() + 24);
                            this.nextClaimTime = nextDate.toISOString().slice(0, 19).replace('T', ' ');
                        } else {
                            this.errorMessage = data.error || 'Failed to claim pulse';
                            if (data.next_claim) {
                                this.nextClaimTime = data.next_claim;
                            }
                        }
                    } catch (error) {
                        console.error('Error claiming pulse:', error);
                        this.errorMessage = 'An error occurred while claiming pulse';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
