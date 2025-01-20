<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    private const STAGES = [
        'pending' => [
            'message' => 'Initializing image generation...',
            'progress' => 0,
            'substages' => [
                'Analyzing your creative vision',
                'Gathering artistic inspiration',
                'Preparing the digital canvas',
                'Setting up creative elements'
            ],
            'feedback' => [
                'initial' => [
                    'I see what you\'re envisioning. Let\'s bring this to life!',
                    'What an interesting concept. This will be exciting to create!',
                    'I love where this is going. Let\'s make something special!',
                    'This prompt has so much potential. Let\'s explore it together!'
                ],
                'progress' => [
                    'Understanding the subtle nuances of your request...',
                    'Exploring different artistic approaches...',
                    'Considering the perfect composition...',
                    'Planning out the visual elements...'
                ]
            ]
        ],
        'processing' => [
            'message' => 'Crafting your masterpiece...',
            'progress' => 25,
            'substages' => [
                'Sketching initial composition',
                'Developing core elements',
                'Adding artistic flourishes',
                'Refining visual details',
                'Enhancing color harmony',
                'Perfecting final touches'
            ],
            'feedback' => [
                'composition' => [
                    'The composition is taking shape beautifully...',
                    'Finding the perfect balance of elements...',
                    'Building a harmonious arrangement...',
                    'Crafting a compelling visual flow...'
                ],
                'elements' => [
                    'Adding depth and dimension to each element...',
                    'Bringing out the unique characteristics...',
                    'Developing intricate details...',
                    'Weaving in subtle complexities...'
                ],
                'colors' => [
                    'The color palette is coming together wonderfully...',
                    'Blending hues to create the perfect atmosphere...',
                    'Fine-tuning the color balance...',
                    'Adding rich color variations...'
                ],
                'details' => [
                    'Now for those magical finishing touches...',
                    'Adding those special little details...',
                    'Perfecting every subtle nuance...',
                    'Making sure every element shines...'
                ],
                'encouragement' => [
                    'This is turning out even better than expected!',
                    'You\'re going to love where this is heading!',
                    'The vision is really coming together now!',
                    'Each element is falling perfectly into place!'
                ]
            ]
        ],
        'completed' => [
            'message' => 'Your creation is ready!',
            'progress' => 100,
            'substages' => [],
            'feedback' => [
                'standard' => [
                    'Voilà! Your vision has been brought to life with every detail carefully crafted.',
                    'And... done! I think you\'ll love how all the elements came together.',
                    'Perfect! Every aspect has been refined to match your creative vision.',
                    'Finished! The final result captures exactly what you were looking for.'
                ],
                'artistic' => [
                    'A true masterpiece, if I do say so myself! Each element tells part of the story.',
                    'Behold your creation! Every detail has been lovingly crafted to perfection.',
                    'Magnificent! The composition turned out even better than imagined.',
                    'Simply stunning! The perfect balance of creativity and precision.'
                ]
            ]
        ],
        'failed' => [
            'message' => 'Image generation encountered an issue',
            'progress' => 100,
            'substages' => [],
            'feedback' => [
                'gentle' => [
                    'Hmm, we hit a small snag. Let\'s try again with a fresh perspective?',
                    'Not quite what we were aiming for. Shall we give it another shot?',
                    'Sometimes creativity needs a second take. Ready to try again?',
                    'A minor setback, but I know we can get it right!'
                ],
                'technical' => [
                    'The creative process encountered an unexpected challenge. Another attempt might work better.',
                    'A technical hiccup interrupted our artistic flow. Let\'s start fresh!',
                    'The digital canvas needs a reset. Ready for another creative journey?',
                    'Sometimes the artistic process needs a restart. Shall we begin anew?'
                ]
            ]
        ]
    ];

    public function create(): View
    {
        return view('images.create');
    }

    public function gallery(Request $request): View
    {
        $query = auth()->user()->galleries()
            ->where('type', 'image');

        // Apply filter if specified
        if ($request->filter === 'available') {
            // Get images that don't have cards in either Gallery or GlobalCard
            $query->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('galleries as cards')
                    ->where('cards.type', 'card')
                    ->whereRaw('cards.image_url = galleries.image_url');
            })->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('global_cards')
                    ->whereRaw('global_cards.image_url = galleries.image_url');
            });
        } elseif ($request->filter === 'created') {
            // Get images that have cards in either Gallery or GlobalCard
            $query->where(function ($query) {
                $query->whereExists(function ($subquery) {
                    $subquery->select(\DB::raw(1))
                        ->from('galleries as cards')
                        ->where('cards.type', 'card')
                        ->whereRaw('cards.image_url = galleries.image_url');
                })->orWhereExists(function ($subquery) {
                    $subquery->select(\DB::raw(1))
                        ->from('global_cards')
                        ->whereRaw('global_cards.image_url = galleries.image_url');
                });
            });
        }

        $images = $query->orderBy('created_at', 'desc')
            ->paginate(6)
            ->withQueryString();
        
        return view('images.gallery', compact('images'));
    }

    public function generate(Request $request): RedirectResponse
    {
        if (session()->has('image_task')) {
            $taskId = session('image_task.task_id');
            return redirect()
                ->route('images.status', $taskId)
                ->with('error', 'You already have an image generation in progress. Please wait for it to complete.')
                ->setStatusCode(409);
        }

        $request->validate([
            'prompt' => 'required|string|max:1000',
            'aspect_ratio' => 'nullable|string|in:1:1,16:9,9:16,4:3',
            'process_mode' => 'nullable|string|in:relax,fast,turbo'
        ]);

        try {
            // Initialize session with random feedback
            $stage = self::STAGES['pending'];
            $initialFeedback = $stage['feedback']['initial'][array_rand($stage['feedback']['initial'])];
            
            $taskInfo = [
                'status' => 'pending',
                'stage_info' => [
                    'message' => $stage['message'],
                    'progress' => $stage['progress'],
                    'substages' => $stage['substages'],
                    'feedback' => $initialFeedback
                ],
                'current_substage' => 0,
                'started_at' => now(),
                'last_updated' => now(),
                'last_progress_update' => now(),
                'prompt' => $request->prompt,
                'aspect_ratio' => $request->aspect_ratio ?? '1:1',
                'process_mode' => $request->process_mode ?? 'relax',
                'feedback_history' => [$initialFeedback]
            ];

            session(['image_task' => $taskInfo]);

            $response = Http::withHeaders([
                'x-api-key' => env('GOAPI_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.goapi.ai/api/v1/task', [
                'model' => 'midjourney',
                'task_type' => 'imagine',
                'input' => [
                    'prompt' => $request->prompt,
                    'aspect_ratio' => $request->aspect_ratio ?? '1:1',
                    'process_mode' => $request->process_mode ?? 'relax',
                    'skip_prompt_check' => false
                ]
            ]);

            if ($response->successful()) {
                $taskId = $response->json('data.task_id');
                
                // Update session with processing state
                $stage = self::STAGES['processing'];
                $processingFeedback = $stage['feedback']['composition'][array_rand($stage['feedback']['composition'])];
                
                $taskInfo['task_id'] = $taskId;
                $taskInfo['status'] = 'processing';
                $taskInfo['stage_info'] = [
                    'message' => $stage['message'],
                    'progress' => $stage['progress'],
                    'substages' => $stage['substages'],
                    'feedback' => $processingFeedback
                ];
                $taskInfo['feedback_history'][] = $processingFeedback;
                
                session(['image_task' => $taskInfo]);
                
                return redirect()
                    ->route('images.status', $taskId)
                    ->with('success', 'Image generation started successfully');
            }

            session()->forget('image_task');
            $errorMessage = $response->json('message') ?? 'Unknown API error occurred';
            return back()
                ->with('error', 'Failed to start image generation: ' . $errorMessage)
                ->setStatusCode(500);
        } catch (\Exception $e) {
            session()->forget('image_task');
            return back()
                ->with('error', 'Failed to connect to image generation service: ' . $e->getMessage())
                ->setStatusCode(500);
        }
    }

    public function status($taskId): View|RedirectResponse
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => env('GOAPI_KEY'),
            ])->get("https://api.goapi.ai/api/v1/task/{$taskId}");

            if ($response->successful()) {
                $data = $response->json('data');
                $taskInfo = session('image_task', []);
                
                // Ensure we have valid task info
                if (empty($taskInfo)) {
                    // Reconstruct basic task info if session was lost
                    $taskInfo = [
                        'status' => $data['status'],
                        'stage_info' => self::STAGES[$data['status']],
                        'current_substage' => 0,
                        'feedback_history' => []
                    ];
                }
                
                // Update progress based on time elapsed and status
                if ($data['status'] === 'processing' && ($taskInfo['status'] ?? '') === 'processing') {
                    $currentSubstage = $taskInfo['current_substage'];
                    $totalSubstages = count(self::STAGES['processing']['substages']);
                    $timeElapsed = now()->diffInSeconds($taskInfo['last_progress_update'] ?? now()->subSeconds(20));
                    
                    // Advance substage with variable timing (10-20 seconds)
                    if ($timeElapsed >= rand(10, 20) && $currentSubstage < $totalSubstages - 1) {
                        $stage = self::STAGES['processing'];
                        $progress = min(90, 25 + ($currentSubstage + 1) * (65 / $totalSubstages));
                        
                        // Select contextual feedback based on current substage
                        $feedbackType = match(true) {
                            $currentSubstage < 2 => 'composition',
                            $currentSubstage < 3 => 'elements',
                            $currentSubstage < 4 => 'colors',
                            default => 'details'
                        };
                        
                        // Occasionally mix in encouraging feedback
                        $feedback = rand(1, 5) === 1 
                            ? $stage['feedback']['encouragement'][array_rand($stage['feedback']['encouragement'])]
                            : $stage['feedback'][$feedbackType][array_rand($stage['feedback'][$feedbackType])];
                        
                        $taskInfo['current_substage'] = $currentSubstage + 1;
                        $taskInfo['last_progress_update'] = now();
                        $taskInfo['stage_info']['progress'] = $progress;
                        $taskInfo['stage_info']['feedback'] = $feedback;
                        $taskInfo['feedback_history'][] = $feedback;
                        
                        session(['image_task' => $taskInfo]);
                    }
                }
                
                // Handle completion
                if ($data['status'] === 'completed' && isset($data['output']['image_urls'])) {
                    $stage = self::STAGES['completed'];
                    $feedback = rand(1, 3) === 1
                        ? $stage['feedback']['artistic'][array_rand($stage['feedback']['artistic'])]
                        : $stage['feedback']['standard'][array_rand($stage['feedback']['standard'])];
                    
                    $taskInfo['status'] = 'completed';
                    $taskInfo['stage_info'] = [
                        'message' => $stage['message'],
                        'progress' => $stage['progress'],
                        'substages' => $stage['substages'],
                        'feedback' => $feedback
                    ];
                    $taskInfo['feedback_history'][] = $feedback;
                    
                    session(['image_task' => $taskInfo]);
                    
                    // Check if images for this task already exist
                    $existingImages = Gallery::where('task_id', $data['task_id'])
                        ->where('type', 'image')
                        ->exists();
                    
                    // Only create new gallery entries if they don't already exist
                    if (!$existingImages) {
                        foreach ($data['output']['image_urls'] as $imageUrl) {
                            auth()->user()->galleries()->create([
                                'type' => 'image',
                                'image_url' => str_replace('\\', '', $imageUrl),
                                'prompt' => $taskInfo['prompt'] ?? '',
                                'aspect_ratio' => $taskInfo['aspect_ratio'] ?? '1:1',
                                'process_mode' => $taskInfo['process_mode'] ?? 'relax',
                                'task_id' => $data['task_id'],
                                'metadata' => [
                                    'created_at' => $data['meta']['created_at'] ?? now(),
                                    'completed_at' => now(),
                                    'feedback_history' => $taskInfo['feedback_history'] ?? []
                                ]
                            ]);
                        }
                    }
                    
                    session()->forget('image_task');
                } elseif ($data['status'] === 'failed') {
                    $stage = self::STAGES['failed'];
                    $feedback = rand(1, 2) === 1
                        ? $stage['feedback']['gentle'][array_rand($stage['feedback']['gentle'])]
                        : $stage['feedback']['technical'][array_rand($stage['feedback']['technical'])];
                    
                    $taskInfo['status'] = 'failed';
                    $taskInfo['stage_info'] = [
                        'message' => $stage['message'],
                        'progress' => $stage['progress'],
                        'substages' => $stage['substages'],
                        'feedback' => $feedback
                    ];
                    $taskInfo['feedback_history'][] = $feedback;
                    
                    session(['image_task' => $taskInfo]);
                    session()->forget('image_task');
                }
                
                // Get the gallery entry for this task if it exists
                $gallery = Gallery::where('task_id', $data['task_id'])
                    ->where('type', 'image')
                    ->with('user')
                    ->first();

                return view('images.status', [
                    'data' => $data,
                    'taskInfo' => $taskInfo,
                    'gallery' => $gallery
                ]);
            }

            session()->forget('image_task');
            return back()
                ->with('error', 'Failed to fetch task status: ' . ($response->json('message') ?? 'Unknown error'))
                ->setStatusCode(500);
        } catch (\Exception $e) {
            session()->forget('image_task');
            return back()
                ->with('error', 'Failed to connect to image generation service: ' . $e->getMessage())
                ->setStatusCode(500);
        }
    }
}