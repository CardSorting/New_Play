<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\Gallery;
use App\Models\GlobalCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PackController extends Controller
{

    public function index()
    {
        $packs = Pack::where('user_id', Auth::id())
            ->whereNull('opened_at')
            ->withCount('cards')
            ->with(['cards' => function($query) {
                $query->inRandomOrder()->limit(1);
            }])
            ->get();
            
        return view('dashboard.packs.index', compact('packs'));
    }

    public function create()
    {
        return view('dashboard.packs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'card_limit' => 'required|integer|min:1|max:100'
        ]);

        $pack = Pack::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'card_limit' => $validated['card_limit'],
            'is_sealed' => false
        ]);

        return redirect()->route('packs.index')
            ->with('success', 'Pack created successfully');
    }

    public function show(Pack $pack)
    {
        try {
            $this->authorize('view', $pack);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($pack->is_sealed) {
                return redirect()->route('packs.index')
                    ->with('error', 'This pack is sealed and cannot be viewed.');
            }
            throw $e;
        }
        
        $pack->load('cards');
        $availableCards = Gallery::where('user_id', Auth::id())
            ->where('type', 'card')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.packs.show', compact('pack', 'availableCards'));
    }

    public function addCard(Request $request, Pack $pack)
    {
        $this->authorize('update', $pack);

        $validated = $request->validate([
            'card_id' => 'required|exists:galleries,id'
        ]);

        if ($pack->is_sealed) {
            return back()->with('error', 'Cannot modify a sealed pack');
        }

        if ($pack->cards()->count() >= $pack->card_limit) {
            return back()->with('error', 'Pack has reached its card limit');
        }

        try {
            DB::beginTransaction();

            $gallery = Gallery::findOrFail($validated['card_id']);
            
            // Create global card from gallery card
            GlobalCard::createFromGallery($gallery, $pack->id);
            
            // Remove card from user's gallery
            $gallery->delete();

            DB::commit();

            return back()->with('success', 'Card added to pack successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add card to pack');
        }
    }

    public function open(Pack $pack)
    {
        if (!$pack->is_sealed) {
            return redirect()->route('cards.index', ['view' => 'grid'])
                ->with('error', 'This pack must be sealed before it can be opened.');
        }

        try {
            DB::beginTransaction();

            // Load cards with lock
            $cards = $pack->cards()->lockForUpdate()->get();
            
            // Prepare gallery entries
            $galleryEntries = [];
            $now = now();
            $userId = auth()->id();
            
            foreach ($cards as $card) {
                $galleryEntries[] = [
                    'user_id' => $userId,
                    'type' => 'card',
                    'name' => $card->name,
                    'image_url' => $card->image_url,
                    'rarity' => $card->rarity,
                    'card_type' => $card->card_type,
                    'mana_cost' => $card->mana_cost,
                    'power_toughness' => $card->power_toughness,
                    'abilities' => $card->abilities,
                    'flavor_text' => $card->flavor_text,
                    'metadata' => json_encode([
                        'opened_from_pack' => $pack->id,
                        'opened_at' => $now->toISOString()
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            // Bulk insert gallery entries
            Gallery::insert($galleryEntries);

            // Bulk delete global cards
            GlobalCard::whereIn('id', $cards->pluck('id'))->delete();

            // Mark pack as opened
            $pack->update(['opened_at' => $now]);

            DB::commit();

            return redirect('/dashboard/cards?view=grid')
                ->with('success', 'Pack opened successfully! The cards have been added to your collection.')
                ->with('new_cards', $galleryEntries);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Pack opening failed', [
                'error' => $e->getMessage(),
                'pack_id' => $pack->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('packs.index')
                ->with('error', 'Failed to open pack. Please try again.');
        }
    }

    public function seal(Pack $pack)
    {
        $this->authorize('update', $pack);

        if ($pack->cards()->count() < $pack->card_limit) {
            return back()->with('error', 'Pack must be full before sealing');
        }

        if (!$pack->seal()) {
            return back()->with('error', 'Unable to seal pack. Please ensure it is not already sealed and has enough cards.');
        }

        return back()->with('success', 'Pack has been sealed and can now be listed on the marketplace');
    }

}
