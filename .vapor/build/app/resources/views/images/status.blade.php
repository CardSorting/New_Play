<x-app-layout>
    @include('images.status-content', ['data' => $data, 'taskInfo' => $taskInfo])

    @push('head')
    <script>
        // Create a self-executing function to avoid global scope pollution
        (function() {
            // Wait for Alpine to be initialized
            function waitForAlpine(callback) {
                if (window.Alpine) {
                    callback();
                } else {
                    document.addEventListener('alpine:init', callback);
                }
            }

            // Wait for DOM to be ready
            function initializeStatus() {
                waitForAlpine(() => {
                    try {
                        // Initialize state
                        const state = {
                            refreshTimer: null,
                            progressTimer: null,
                            countdownTimer: null,
                            countdown: 5,
                            refreshInterval: 5000
                        };

                        // Get elements
                        const elements = {
                            modal: document.getElementById('imageModal'),
                            modalContent: document.getElementById('imageModal')?.querySelector('.bg-white'),
                            modalImage: document.getElementById('modalImage'),
                            modalPrompt: document.getElementById('modalPrompt'),
                            modalAspectRatio: document.getElementById('modalAspectRatio'),
                            modalProcessMode: document.getElementById('modalProcessMode'),
                            modalTaskId: document.getElementById('modalTaskId'),
                            modalCreated: document.getElementById('modalCreated'),
                            progressBar: document.getElementById('refreshProgress'),
                            countdownElement: document.getElementById('refreshCountdown'),
                            newImageButton: document.querySelector('[data-new-image-button]'),
                            statusContent: document.querySelector('[data-status-content]')
                        };

                        // Initialize status updates if needed
                        const statusElement = document.querySelector('[data-status]');
                        if (statusElement) {
                            const status = statusElement.dataset.status;
                            if (status === 'pending' || status === 'processing') {
                                startAutoRefresh();
                                if (elements.newImageButton) {
                                    elements.newImageButton.disabled = true;
                                    elements.newImageButton.classList.add('opacity-50', 'cursor-not-allowed');
                                }
                            }
                        }

                        // Add modal event listeners if modal exists
                        if (elements.modal) {
                            elements.modal.addEventListener('click', (e) => {
                                if (e.target === elements.modal) {
                                    closeModal();
                                }
                            });

                            // Add keyboard event listener for modal
                            document.addEventListener('keydown', (e) => {
                                if (e.key === 'Escape' && elements.modal.classList.contains('flex')) {
                                    closeModal();
                                }
                            });
                        }

                        // Expose necessary functions to window
                        window.handleImageClick = function(event, imageUrl, data) {
                            event.preventDefault();
                            openModal(imageUrl, data);
                        };

                        window.closeModal = closeModal;

                        function openModal(imageUrl, data) {
                            if (!elements.modal || !elements.modalContent) return;

                            const img = new Image();
                            img.onload = () => {
                                if (elements.modalImage) elements.modalImage.src = imageUrl;
                                if (elements.modalPrompt) elements.modalPrompt.textContent = data.input.prompt || 'Not available';
                                if (elements.modalAspectRatio) elements.modalAspectRatio.textContent = data.input.aspect_ratio || '1:1';
                                if (elements.modalProcessMode) elements.modalProcessMode.textContent = data.input.process_mode || 'relax';
                                if (elements.modalTaskId) elements.modalTaskId.textContent = data.task_id || 'Not available';
                                
                                if (elements.modalCreated) {
                                    const createdDate = new Date(data.meta.created_at || null);
                                    elements.modalCreated.textContent = createdDate.toLocaleString() || 'Not available';
                                }

                                elements.modal.classList.remove('hidden');
                                requestAnimationFrame(() => {
                                    elements.modal.classList.add('flex');
                                    elements.modalContent.style.transform = 'scale(1)';
                                    elements.modalContent.style.opacity = '1';
                                });
                            };
                            img.src = imageUrl;
                        }

                        function closeModal() {
                            if (!elements.modal || !elements.modalContent) return;
                            
                            elements.modalContent.style.transform = 'scale(0.95)';
                            elements.modalContent.style.opacity = '0';
                            
                            setTimeout(() => {
                                elements.modal.classList.add('hidden');
                                elements.modal.classList.remove('flex');
                            }, 300);
                        }

                        function startAutoRefresh() {
                            clearTimers();

                            state.countdown = 5;
                            updateCountdown();
                            state.countdownTimer = setInterval(updateCountdown, 1000);

                            if (elements.progressBar) {
                                elements.progressBar.style.width = '0%';
                                requestAnimationFrame(() => {
                                    elements.progressBar.style.width = '100%';
                                });
                            }

                            state.refreshTimer = setTimeout(async () => {
                                try {
                                    const response = await fetch(window.location.href);
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    const html = await response.text();
                                    const parser = new DOMParser();
                                    const newDoc = parser.parseFromString(html, 'text/html');
                                    
                                    const newContent = newDoc.querySelector('[data-status-content]');
                                    
                                    if (elements.statusContent && newContent) {
                                        elements.statusContent.style.opacity = '0';
                                        
                                        setTimeout(() => {
                                            elements.statusContent.innerHTML = newContent.innerHTML;
                                            
                                            elements.statusContent.querySelectorAll('.animate-slide-up').forEach(el => {
                                                el.style.animation = 'none';
                                                el.offsetHeight;
                                                el.style.animation = null;
                                            });
                                            
                                            elements.statusContent.style.opacity = '1';
                                            
                                            const newStatus = newDoc.querySelector('[data-status]')?.dataset.status;
                                            if (newStatus === 'pending' || newStatus === 'processing') {
                                                startAutoRefresh();
                                            } else {
                                                clearTimers();
                                                if (elements.newImageButton) {
                                                    elements.newImageButton.disabled = false;
                                                    elements.newImageButton.classList.remove('opacity-50', 'cursor-not-allowed');
                                                }
                                                // If completed, reload the page to ensure proper state
                                                if (newStatus === 'completed') {
                                                    window.location.reload();
                                                }
                                            }
                                        }, 300);
                                    }
                                } catch (error) {
                                    console.error('Failed to refresh status:', error);
                                    setTimeout(() => startAutoRefresh(), 2000);
                                }
                            }, state.refreshInterval);
                        }

                        function updateCountdown() {
                            if (elements.countdownElement) {
                                elements.countdownElement.textContent = state.countdown;
                                if (state.countdown > 0) {
                                    state.countdown--;
                                }
                            }
                        }

                        function clearTimers() {
                            if (state.refreshTimer) clearTimeout(state.refreshTimer);
                            if (state.progressTimer) clearInterval(state.progressTimer);
                            if (state.countdownTimer) clearInterval(state.countdownTimer);
                        }
                    } catch (error) {
                        console.error('Status initialization error:', error);
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeStatus);
            } else {
                initializeStatus();
            }
        })();
    </script>
    @endpush
</x-app-layout>
