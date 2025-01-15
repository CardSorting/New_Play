class StatusService {
    constructor() {
        // Wait for DOM to be fully loaded before initializing
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        // Initialize elements only if they exist
        this.modal = document.getElementById('imageModal');
        this.modalContent = this.modal?.querySelector('.bg-white');
        this.modalImage = document.getElementById('modalImage');
        this.modalPrompt = document.getElementById('modalPrompt');
        this.modalAspectRatio = document.getElementById('modalAspectRatio');
        this.modalProcessMode = document.getElementById('modalProcessMode');
        this.modalTaskId = document.getElementById('modalTaskId');
        this.modalCreated = document.getElementById('modalCreated');
        this.progressBar = document.getElementById('refreshProgress');
        this.countdownElement = document.getElementById('refreshCountdown');
        this.newImageButton = document.querySelector('[data-new-image-button]');
        this.statusContent = document.querySelector('[data-status-content]');
        
        this.refreshInterval = 5000; // 5 seconds
        this.refreshTimer = null;
        this.progressTimer = null;
        this.countdownTimer = null;
        this.countdown = 5;
        
        this.initialize();
    }

    initialize() {
        // Only add event listeners if elements exist
        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });
        }

        // Global event listeners
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal?.classList.contains('flex')) {
                this.closeModal();
            }
        });

        // Start auto-refresh if status is pending or processing
        const statusElement = document.querySelector('[data-status]');
        if (statusElement) {
            const status = statusElement.dataset.status;
            if (status === 'pending' || status === 'processing') {
                this.startAutoRefresh();
                if (this.newImageButton) {
                    this.newImageButton.disabled = true;
                    this.newImageButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
        }
    }

    handleImageClick(event, imageUrl, data) {
        event.preventDefault();
        event.stopPropagation();
        this.openModal(imageUrl, data);
        return false;
    }

    openModal(imageUrl, data) {
        if (!this.modal || !this.modalContent) return;

        // Pre-load image
        const img = new Image();
        img.onload = () => {
            if (this.modalImage) this.modalImage.src = imageUrl;
            if (this.modalPrompt) this.modalPrompt.textContent = data.input.prompt || 'Not available';
            if (this.modalAspectRatio) this.modalAspectRatio.textContent = data.input.aspect_ratio || '1:1';
            if (this.modalProcessMode) this.modalProcessMode.textContent = data.input.process_mode || 'relax';
            if (this.modalTaskId) this.modalTaskId.textContent = data.task_id || 'Not available';
            
            if (this.modalCreated) {
                const createdDate = new Date(data.meta.created_at || null);
                this.modalCreated.textContent = createdDate.toLocaleString() || 'Not available';
            }

            // Show modal with animation
            this.modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                this.modal.classList.add('flex');
                if (this.modalContent) {
                    this.modalContent.style.transform = 'scale(1)';
                    this.modalContent.style.opacity = '1';
                }
            });
        };
        img.src = imageUrl;
    }

    closeModal() {
        if (!this.modal || !this.modalContent) return;
        
        // Hide modal with animation
        if (this.modalContent) {
            this.modalContent.style.transform = 'scale(0.95)';
            this.modalContent.style.opacity = '0';
        }
        
        setTimeout(() => {
            this.modal.classList.add('hidden');
            this.modal.classList.remove('flex');
        }, 300);
    }

    startAutoRefresh() {
        // Clear any existing timers
        this.clearTimers();

        // Start countdown
        this.countdown = 5;
        this.updateCountdown();
        this.countdownTimer = setInterval(() => this.updateCountdown(), 1000);

        // Start progress bar animation
        if (this.progressBar) {
            this.progressBar.style.width = '0%';
            requestAnimationFrame(() => {
                this.progressBar.style.width = '100%';
            });
        }

        // Set up the refresh timer
        this.refreshTimer = setTimeout(async () => {
            try {
                const response = await fetch(window.location.href);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const html = await response.text();
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                
                // Update only the main content area to prevent page flicker
                const newContent = newDoc.querySelector('[data-status-content]');
                
                if (this.statusContent && newContent) {
                    // Prepare for transition
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = newContent.innerHTML;
                    
                    // Fade out current content
                    this.statusContent.style.opacity = '0';
                    
                    // After fade out, update content and fade in
                    setTimeout(() => {
                        // Update content
                        this.statusContent.innerHTML = tempDiv.innerHTML;
                        
                        // Trigger any new animations
                        this.statusContent.querySelectorAll('.animate-slide-up').forEach(el => {
                            el.style.animation = 'none';
                            el.offsetHeight; // Trigger reflow
                            el.style.animation = null;
                        });
                        
                        // Fade in new content
                        this.statusContent.style.opacity = '1';
                        
                        // Check if we should continue refreshing
                        const newStatus = newDoc.querySelector('[data-status]')?.dataset.status;
                        if (newStatus === 'pending' || newStatus === 'processing') {
                            this.startAutoRefresh();
                        } else {
                            this.clearTimers();
                            if (this.newImageButton) {
                                this.newImageButton.disabled = false;
                                this.newImageButton.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        }
                    }, 300);
                }
            } catch (error) {
                console.error('Failed to refresh status:', error);
                // Retry on error after a delay
                setTimeout(() => this.startAutoRefresh(), 2000);
            }
        }, this.refreshInterval);
    }

    updateCountdown() {
        if (this.countdownElement) {
            this.countdownElement.textContent = this.countdown;
            if (this.countdown > 0) {
                this.countdown--;
            }
        }
    }

    clearTimers() {
        if (this.refreshTimer) clearTimeout(this.refreshTimer);
        if (this.progressTimer) clearInterval(this.progressTimer);
        if (this.countdownTimer) clearInterval(this.countdownTimer);
    }
}

// Initialize the service only once
let statusService;
if (!window.statusService) {
    statusService = new StatusService();
    window.statusService = statusService;
}