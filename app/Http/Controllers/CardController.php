<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Custom validation rule for unique cards
     */
    public function __construct()
    {
        \Validator::extend('unique_card', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            return !(
                Gallery::where('type', 'card')
                    ->where('image_url', $data['image_url'])
                    ->exists() ||
                \App\Models\GlobalCard::where('image_url', $data['image_url'])
                    ->exists()
            );
        }, 'You have already created a card for this image.');
    }

    /**
     * Display a listing of the cards.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $view = $request->get('view', 'grid');
        $perPage = $view === 'grid' ? 15 : 20; // More cards per page in list view

        $query = Gallery::where('type', 'card')
            ->where('user_id', auth()->id())
            ->with('user');  // Eager load user information

        if ($tab === 'newest') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        $cards = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Show success message if redirected from pack opening
        if ($request->has('opened')) {
            session()->flash('success', 'Pack opened successfully! The cards have been added to your collection.');
        }

        // Get new cards from session if redirected from pack opening
        $newCards = session()->get('new_cards', []);
        
        return view('dashboard.cards.index', [
            'cards' => $cards,
            'currentTab' => $tab,
            'currentView' => $view,
            'newCards' => $newCards
        ]);
    }

    /**
     * Show the form for creating a new card.
     */
    public function create(Gallery $image)
    {
        // Ensure the image is of type 'image'
        if ($image->type !== 'image') {
            abort(404, 'Invalid image type');
        }

        // Check if a card already exists for this image
        $cardExists = Gallery::where('type', 'card')
            ->where('image_url', $image->image_url)
            ->exists() ||
            \App\Models\GlobalCard::where('image_url', $image->image_url)
            ->exists();

        return view('images.create-card', [
            'image' => $image->load('user'),
            'cardExists' => $cardExists
        ]);
    }

    /**
     * Store a newly created card in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Card creation attempt', [
                'request' => $request->all(),
                'user_id' => auth()->id()
            ]);

            // Validate all data upfront
            $validator = Validator::make($request->all(), [
                'image_id' => ['required', 'exists:galleries,id'],
                'name' => ['required', 'string', 'max:255'],
                'mana_cost' => ['required', 'string', 'max:50'],
                'card_type' => ['required', 'string', 'max:255'],
                'abilities' => ['required', 'string'],
                'flavor_text' => ['nullable', 'string'],
                'power_toughness' => ['nullable', 'string', 'max:10'],
                'image_url' => ['required', 'url']
            ]);

            if ($validator->fails()) {
                \Log::error('Card creation validation failed', [
                    'errors' => $validator->errors()->all(),
                    'input' => $request->except(['_token']),
                    'user' => auth()->id(),
                    'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validatedData = $validator->validated();

            // Get the original image
            $image = Gallery::where('id', $validatedData['image_id'])
                ->where('type', 'image')
                ->first();

            if (!$image) {
                \Log::error('Invalid image selected', [
                    'image_id' => $validatedData['image_id'],
                    'user_id' => auth()->id()
                ]);
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Invalid image selected.']);
            }

            // Check if a card already exists for this image in either Gallery or GlobalCard
            $existingGalleryCard = Gallery::where('type', 'card')
                ->where('image_url', $image->image_url)
                ->first();
            
            $existingGlobalCard = \App\Models\GlobalCard::where('image_url', $image->image_url)
                ->first();
                
            if ($existingGalleryCard || $existingGlobalCard) {
                $existingCard = $existingGalleryCard ?? $existingGlobalCard;
                \Log::warning('Attempted to create duplicate card', [
                    'image_id' => $image->id,
                    'image_url' => $image->image_url,
                    'existing_card_id' => $existingCard->id,
                    'existing_card_type' => $existingGalleryCard ? 'gallery' : 'global',
                    'user_id' => auth()->id()
                ]);
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'A card already exists for this image.']);
            }

            // Verify image URL matches
            if ($validatedData['image_url'] !== $image->image_url) {
                \Log::error('Image URL mismatch', [
                    'provided_url' => $validatedData['image_url'],
                    'actual_url' => $image->image_url,
                    'image_id' => $image->id,
                    'user_id' => auth()->id()
                ]);
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Invalid image URL provided.']);
            }

            // Randomly select rarity with weighted probabilities
            $rarities = [
            'Common' => 50,      // 50% chance
            'Uncommon' => 30,    // 30% chance
            'Rare' => 15,        // 15% chance
            'Mythic Rare' => 5   // 5% chance
        ];
        
        $total = array_sum($rarities);
        $roll = rand(1, $total);
        $selectedRarity = 'Common';
        
        foreach ($rarities as $rarity => $weight) {
            if ($roll <= $weight) {
                $selectedRarity = $rarity;
                break;
            }
            $roll -= $weight;
        }

            // Format mana cost into comma-separated list
            $manaCost = implode(',', str_split($request->mana_cost));
            
            \Log::info('Creating card with data', [
                'type' => 'card',
                'name' => $request->name,
                'mana_cost' => $manaCost,
                'card_type' => $request->card_type,
                'abilities' => $request->abilities,
                'flavor_text' => $request->flavor_text,
                'power_toughness' => $request->power_toughness,
                'image_url' => $request->image_url
            ]);

            // Create the card using the original image's data
            $card = auth()->user()->galleries()->create([
                'type' => 'card',
                'name' => $validatedData['name'],
                'mana_cost' => implode(',', str_split($validatedData['mana_cost'])),
                'card_type' => $validatedData['card_type'],
                'abilities' => $validatedData['abilities'],
                'flavor_text' => $validatedData['flavor_text'],
                'power_toughness' => $validatedData['power_toughness'],
                'rarity' => $selectedRarity,
                'image_url' => $image->image_url,
                'metadata' => [
                    'original_image_id' => $image->id,
                    'created_from' => 'image',
                    'created_at' => now()->toISOString(),
                    'original_metadata' => $image->metadata,
                    'original_author' => [
                        'id' => $image->user->id,
                        'name' => $image->user->name
                    ]
                ]
            ]);

            \Log::info('Card created successfully', [
                'card_id' => $card->id,
                'card_data' => $card->toArray(),
                'original_image_id' => $image->id
            ]);

            // Return JSON response with card data for animation
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'card' => $card,
                    'message' => 'Card created successfully!'
                ]);
            }

            return redirect()->route('cards.index')
                ->with('success', 'Card created successfully!')
                ->with('last_card', $card);
        } catch (\Exception $e) {
            \Log::error('Card creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['_token']),
                'user' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again.'
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    /**
     * Display the specified card.
     */
    public function show(Gallery $card)
    {
        return view('dashboard.cards.show', [
            'card' => $card,
        ]);
    }

    /**
     * Show the form for editing the specified card.
     */
    public function edit(Gallery $card)
    {
        return view('dashboard.cards.edit', [
            'card' => $card,
        ]);
    }

    /**
     * Update the specified card in storage.
     */
    public function update(Request $request, Gallery $card)
    {
        // Reuse existing update logic from ImageController
        return app(ImageController::class)->update($request, $card);
    }

    /**
     * Remove the specified card from storage.
     */
    public function destroy(Gallery $card)
    {
        // Reuse existing delete logic from ImageController
        return app(ImageController::class)->destroy($card);
    }
}
