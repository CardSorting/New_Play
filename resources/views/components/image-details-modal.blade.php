<div id="detailsModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50"
     role="dialog"
     aria-labelledby="modalTitle"
     aria-modal="true">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 relative shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Image Details</h2>
            <button onclick="ImageDetailsModal.close()" 
                    class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="rounded-lg overflow-hidden shadow-lg">
                    <img id="modalImage" 
                         src="" 
                         alt="Selected image" 
                         class="w-full h-auto rounded-lg">
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Prompt</h3>
                    <p id="modalPrompt" class="text-gray-900 text-lg leading-relaxed"></p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Generation Settings</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-3 rounded shadow-sm">
                            <p class="text-xs font-medium text-gray-500 mb-1">Aspect Ratio</p>
                            <p id="modalAspectRatio" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm">
                            <p class="text-xs font-medium text-gray-500 mb-1">Process Mode</p>
                            <p id="modalProcessMode" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Metadata</h3>
                    <div class="space-y-3">
                        <div class="bg-white p-3 rounded shadow-sm">
                            <p class="text-xs font-medium text-gray-500 mb-1">Task ID</p>
                            <p id="modalTaskId" class="text-sm font-semibold text-gray-900 break-all"></p>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm">
                            <p class="text-xs font-medium text-gray-500 mb-1">Created</p>
                            <p id="modalCreatedAt" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>

                <div id="feedbackHistorySection" class="bg-gray-50 p-4 rounded-lg hidden">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Creation Journey</h3>
                    <div id="modalFeedbackHistory" class="space-y-2 max-h-48 overflow-y-auto">
                        <!-- Feedback history items will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
