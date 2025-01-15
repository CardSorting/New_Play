const ImageDetailsModal = {
    modal: null,
    modalImage: null,
    modalPrompt: null,
    modalAspectRatio: null,
    modalProcessMode: null,
    modalTaskId: null,
    modalCreatedAt: null,
    modalFeedbackHistory: null,
    feedbackHistorySection: null,

    initialize() {
        this.modal = document.getElementById('detailsModal');
        this.modalImage = document.getElementById('modalImage');
        this.modalPrompt = document.getElementById('modalPrompt');
        this.modalAspectRatio = document.getElementById('modalAspectRatio');
        this.modalProcessMode = document.getElementById('modalProcessMode');
        this.modalTaskId = document.getElementById('modalTaskId');
        this.modalCreatedAt = document.getElementById('modalCreatedAt');
        this.modalFeedbackHistory = document.getElementById('modalFeedbackHistory');
        this.feedbackHistorySection = document.getElementById('feedbackHistorySection');

        // Close modal when clicking outside
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('flex')) {
                this.close();
            }
        });
    },

    show(prompt, imageUrl, aspectRatio, processMode, taskId, createdAt, metadata) {
        this.modalImage.src = imageUrl;
        this.modalPrompt.textContent = prompt;
        this.modalAspectRatio.textContent = aspectRatio;
        this.modalProcessMode.textContent = processMode;
        this.modalTaskId.textContent = taskId;
        this.modalCreatedAt.textContent = createdAt;

        // Handle feedback history if available
        if (metadata && metadata.feedback_history && metadata.feedback_history.length > 0) {
            this.modalFeedbackHistory.innerHTML = metadata.feedback_history
                .map(feedback => `<p class="text-sm text-gray-600 italic bg-white p-2 rounded">${feedback}</p>`)
                .join('');
            this.feedbackHistorySection.classList.remove('hidden');
        } else {
            this.feedbackHistorySection.classList.add('hidden');
        }

        this.modal.classList.remove('hidden');
        this.modal.classList.add('flex');
        
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
    ImageDetailsModal.initialize();
});