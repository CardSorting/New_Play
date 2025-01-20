<?php

namespace App\Jobs;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $taskId,
        protected string $prompt,
        protected string $aspectRatio,
        protected string $processMode,
        protected int $userId,
        protected array $feedbackHistory = []
    ) {
        // Set the queue name without the suffix - Vapor will append it
        $this->onQueue('images');
    }

    public function handle(): void
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => env('GOAPI_KEY'),
            ])->get("https://api.goapi.ai/api/v1/task/{$this->taskId}");

            if ($response->successful()) {
                $data = $response->json('data');

                if ($data['status'] === 'completed' && isset($data['output']['image_urls'])) {
                    // Check if images for this task already exist
                    $existingImages = Gallery::where('task_id', $data['task_id'])
                        ->where('type', 'image')
                        ->exists();

                    // Only create new gallery entries if they don't already exist
                    if (!$existingImages) {
                        foreach ($data['output']['image_urls'] as $imageUrl) {
                            Gallery::create([
                                'user_id' => $this->userId,
                                'type' => 'image',
                                'image_url' => str_replace('\\', '', $imageUrl),
                                'prompt' => $this->prompt,
                                'aspect_ratio' => $this->aspectRatio,
                                'process_mode' => $this->processMode,
                                'task_id' => $this->taskId,
                                'metadata' => [
                                    'created_at' => $data['meta']['created_at'] ?? now(),
                                    'completed_at' => now(),
                                    'feedback_history' => $this->feedbackHistory
                                ]
                            ]);
                        }
                    }
                } elseif ($data['status'] === 'processing') {
                    // Re-dispatch the job with a delay to check again
                    self::dispatch($this->taskId, $this->prompt, $this->aspectRatio, $this->processMode, $this->userId, $this->feedbackHistory)
                        ->onQueue('images')
                        ->delay(now()->addSeconds(10));
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it to prevent job from failing
            \Log::error('Image generation job failed', [
                'task_id' => $this->taskId,
                'error' => $e->getMessage()
            ]);
        }
    }
}