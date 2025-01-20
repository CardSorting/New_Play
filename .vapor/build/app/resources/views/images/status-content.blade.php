<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Image Generation Status') }}
        </h2>
        <div class="flex space-x-4">
            <a href="{{ route('images.gallery') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-200">
                View Gallery
            </a>
            <a 
                href="{{ route('images.create') }}" 
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200"
                data-new-image-button
            >
                Generate New Image
            </a>
        </div>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div data-status-content>
                <div class="mb-6">
                    <span class="font-semibold">Status:</span>
                    <span 
                        class="ml-2 px-3 py-1 rounded-full text-sm
                        @if($data['status'] === 'completed') bg-green-100 text-green-800
                        @elseif($data['status'] === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif"
                        data-status="{{ $data['status'] }}"
                    >
                        {{ ucfirst($data['status']) }}
                    </span>
                </div>

                @if($data['status'] === 'completed' && isset($data['output']['image_urls']))
                    <div class="space-y-6">
                        <!-- Feedback Message -->
                        <div class="text-center mb-8">
                            <p class="text-xl text-gray-700 font-medium animate-fade-in">
                                {{ $taskInfo['stage_info']['feedback'] }}
                            </p>
                        </div>
                        
                        <!-- Image Grid -->
                        <div class="grid grid-cols-2 gap-6">
                            @foreach($data['output']['image_urls'] as $index => $imageUrl)
                                <div class="relative group bg-white rounded-lg shadow-lg overflow-hidden animate-slide-up" style="animation-delay: {{ $index * 150 }}ms">
                                    <div 
                                        class="aspect-square relative cursor-pointer transform transition-transform duration-300 hover:scale-[1.02]"
                                        onclick="handleImageClick(event, '{{ $imageUrl }}', {{ json_encode($data) }}, {{ isset($gallery) ? json_encode(['author' => $gallery->user->name]) : 'null' }})"
                                    >
                                        <!-- Background Overlay -->
                                        <div class="absolute inset-0 bg-cover bg-center z-10"
                                             style="background-image: url('{{ $imageUrl }}');">
                                        </div>
                                        
                                        <!-- Actual image -->
                                        <img 
                                            src="{{ $imageUrl }}" 
                                            alt="Generated image {{ $index + 1 }}" 
                                            class="opacity-0 w-full h-full object-cover"
                                        >
                                        
                                        <!-- Info Overlay -->
                                        <div class="absolute inset-0 z-20 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                            <p class="text-white text-sm px-4 py-2 text-center">{{ $data['input']['prompt'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Feedback History -->
                        @if(isset($taskInfo['feedback_history']) && count($taskInfo['feedback_history']) > 0)
                            <div class="mt-12 pt-8 border-t border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Creation Journey</h3>
                                <div class="space-y-3">
                                    @foreach($taskInfo['feedback_history'] as $index => $feedback)
                                        <div class="animate-fade-in" style="animation-delay: {{ ($index + 1) * 100 }}ms">
                                            <p class="text-gray-600 italic">{{ $feedback }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($data['status'] === 'failed')
                    <div class="text-center space-y-6">
                        <div class="text-red-600 mb-4">
                            Error: {{ $data['error']['message'] ?? 'An unknown error occurred' }}
                        </div>
                        <p class="text-xl text-gray-700">{{ $taskInfo['stage_info']['feedback'] }}</p>
                        <a 
                            href="{{ route('images.create') }}"
                            class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                        >
                            Try Again
                        </a>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center p-12">
                        <!-- Progress Circle -->
                        <div class="relative">
                            <svg class="w-32 h-32" viewBox="0 0 100 100">
                                <!-- Background circle -->
                                <circle 
                                    class="text-gray-200" 
                                    stroke-width="8" 
                                    stroke="currentColor" 
                                    fill="transparent" 
                                    r="42" 
                                    cx="50" 
                                    cy="50"
                                />
                                <!-- Progress circle -->
                                <circle 
                                    class="text-blue-500 transition-all duration-1000 ease-in-out" 
                                    stroke-width="8" 
                                    stroke="currentColor" 
                                    fill="transparent" 
                                    r="42" 
                                    cx="50" 
                                    cy="50"
                                    stroke-dasharray="264"
                                    stroke-dashoffset="{{ 264 - ($taskInfo['stage_info']['progress'] / 100 * 264) }}"
                                    transform="rotate(-90 50 50)"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-semibold">{{ $taskInfo['stage_info']['progress'] }}%</span>
                            </div>
                        </div>

                        <!-- Status Message -->
                        <div class="mt-8 text-center space-y-4">
                            <p class="text-xl text-gray-700 font-medium animate-pulse">
                                {{ $taskInfo['stage_info']['message'] }}
                            </p>
                            <p class="text-gray-600 italic transition-opacity duration-300">
                                {{ $taskInfo['stage_info']['feedback'] }}
                            </p>
                        </div>

                        <!-- Substages Progress -->
                        <div class="w-full max-w-md mt-8">
                            @foreach($taskInfo['stage_info']['substages'] as $index => $substage)
                                <div class="flex items-center mb-4 transition-all duration-300 {{ $index <= $taskInfo['current_substage'] ? 'opacity-100' : 'opacity-50' }}">
                                    <div class="w-8 h-8 flex-shrink-0 mr-4">
                                        @if($index < $taskInfo['current_substage'])
                                            <!-- Completed -->
                                            <svg class="w-8 h-8 text-green-500 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($index === $taskInfo['current_substage'])
                                            <!-- Current -->
                                            <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                        @else
                                            <!-- Pending -->
                                            <div class="w-8 h-8 border-4 border-gray-200 rounded-full transition-colors duration-300"></div>
                                        @endif
                                    </div>
                                    <span class="text-base {{ $index <= $taskInfo['current_substage'] ? 'text-gray-900 font-medium' : 'text-gray-500' }}">
                                        {{ $substage }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Auto-refresh Progress -->
                        <div class="w-64 h-1 bg-gray-200 rounded-full mt-12 overflow-hidden">
                            <div 
                                id="refreshProgress"
                                class="h-full bg-blue-500 w-0 transition-all duration-[5000ms] ease-linear"
                            ></div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            Next update in <span id="refreshCountdown" class="font-medium">5</span> seconds
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Image Details</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <img id="modalImage" src="" alt="Selected image" class="w-full h-auto rounded-lg shadow-md">
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="font-semibold mb-2">Prompt</h3>
                    <p id="modalPrompt" class="text-gray-600"></p>
                </div>
                <div>
                    <h3 class="font-semibold mb-2">Settings</h3>
                    <div class="space-y-3">
                        <p><span class="font-medium">Aspect Ratio:</span> <span id="modalAspectRatio" class="text-gray-600"></span></p>
                        <p><span class="font-medium">Process Mode:</span> <span id="modalProcessMode" class="text-gray-600"></span></p>
                        <p><span class="font-medium">Task ID:</span> <span id="modalTaskId" class="text-gray-600"></span></p>
                        <p><span class="font-medium">Created:</span> <span id="modalCreated" class="text-gray-600"></span></p>
                        <p><span class="font-medium">Author:</span> <span id="modalAuthor" class="text-gray-600"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .animate-slide-up {
        animation: slide-up 0.5s ease-out forwards;
    }

    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }

    [data-status-content] {
        transition: opacity 300ms ease-in-out;
    }

    #imageModal .bg-white {
        transition: all 300ms ease-in-out;
    }

    #imageModal.flex .bg-white {
        transform: scale(1);
        opacity: 1;
    }
</style>

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
                        modalAuthor: document.getElementById('modalAuthor'),
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
                    window.handleImageClick = function(event, imageUrl, data, galleryInfo) {
                        event.preventDefault();
                        openModal(imageUrl, data, galleryInfo);
                    };

                    window.closeModal = closeModal;

                    function openModal(imageUrl, data, galleryInfo) {
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
                            if (elements.modalAuthor) {
                                elements.modalAuthor.textContent = galleryInfo?.author || 'Not available';
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
