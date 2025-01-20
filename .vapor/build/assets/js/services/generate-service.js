class GenerateService {
    constructor() {
        this.isGenerating = false;
        this.form = document.getElementById('generateForm');
        this.button = document.getElementById('generateButton');
        this.generateIcon = this.button?.querySelector('.generate-icon');
        this.loadingIcon = this.button?.querySelector('.loading-icon');
        this.buttonText = this.button?.querySelector('.button-text');
        
        this.initialize();
    }

    initialize() {
        if (!this.form || !this.button) return;

        // Prevent form submission on Enter key if already generating
        this.form.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && this.isGenerating) {
                e.preventDefault();
            }
        });

        // Handle form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    handleSubmit(e) {
        // Prevent submission if already generating
        if (this.isGenerating) {
            e.preventDefault();
            return;
        }

        this.setGeneratingState(true);
    }

    setGeneratingState(generating) {
        this.isGenerating = generating;
        this.button.disabled = generating;
        
        if (generating) {
            this.generateIcon.classList.add('hidden');
            this.loadingIcon.classList.remove('hidden');
            this.buttonText.textContent = 'Generating...';
            
            // Add a visual indicator to the form
            this.form.classList.add('processing');
        } else {
            this.generateIcon.classList.remove('hidden');
            this.loadingIcon.classList.add('hidden');
            this.buttonText.textContent = 'Generate Image';
            this.form.classList.remove('processing');
        }
    }
}

// Initialize the service
document.addEventListener('DOMContentLoaded', () => {
    window.generateService = new GenerateService();
});