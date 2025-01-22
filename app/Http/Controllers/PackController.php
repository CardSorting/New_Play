<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::where('user_id', Auth::id())
            ->whereNull('opened_at')
            ->withCount(['cards' => function($query) {
                $query->where('is_in_pack', true);
            }])
            ->with(['cards' => function($query) {
                $query->where('is_in_pack', true)
                      ->inRandomOrder()
                      ->limit(1);
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
        try {
            DB::beginTransaction();
            
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

            DB::commit();

            logger()->info('Pack created successfully', [
                'pack_id' => $pack->id,
                'user_id' => Auth::id(),
                'card_limit' => $validated['card_limit']
            ]);

            return redirect()->route('packs.index')
                ->with('success', 'Pack created successfully');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            logger()->error('Pack creation validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all(),
                'user_id' => Auth::id()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Pack creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
                'user_id' => Auth::id()
            ]);
            return back()->withInput()->with('error', 'Failed to create pack: ' . $e->getMessage());
        }
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
            ->where('is_in_pack', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.packs.show', compact('pack', 'availableCards'));
    }

    public function addCard(Request $request, Pack $pack)
    {
        try {
            $this->authorize('update', $pack);

            $validated = $request->validate([
                'card_id' => 'required|exists:galleries,id'
            ]);

            if ($pack->is_sealed) {
                throw new \Exception('Cannot modify a sealed pack');
            }

            DB::beginTransaction();

            $card = Gallery::findOrFail($validated['card_id']);
            
            $result = $pack->addCard($card);
            
            if (!$result['success']) {
                throw new \Exception($result['message']);
            }

            DB::commit();

            logger()->info('Card added to pack successfully', [
                'pack_id' => $pack->id,
                'card_id' => $card->id,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', $result['message']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            logger()->error('Card addition validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all(),
                'pack_id' => $pack->id,
                'user_id' => Auth::id()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Failed to add card to pack', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pack_id' => $pack->id,
                'card_id' => $validated['card_id'] ?? null,
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'Failed to add card: ' . $e->getMessage());
        }
    }

    public function open(Pack $pack)
    {
        $this->authorize('open', $pack);

        // Validate pack state
        if (!$pack->is_sealed) {
            logger()->warning('Attempted to open unsealed pack', [
                'pack_id' => $pack->id,
                'user_id' => auth()->id(),
                'pack_state' => [
                    'is_sealed' => $pack->is_sealed,
                    'opened_at' => $pack->opened_at
                ]
            ]);
            return redirect()->route('packs.index')
                ->with('error', 'Pack must be sealed before opening.');
        }

        // Check if pack has already been opened
        if ($pack->opened_at !== null) {
            logger()->warning('Attempted to open already opened pack', [
                'pack_id' => $pack->id,
                'user_id' => auth()->id(),
                'opened_at' => $pack->opened_at
            ]);
            return redirect()->route('packs.index')
                ->with('error', 'This pack has already been opened.');
        }

        try {
            DB::beginTransaction();

            $cards = $pack->cards()->lockForUpdate()->get();
            if ($cards->isEmpty()) {
                throw new \Exception('No cards found in pack.');
            }

            $now = now();
            $userId = auth()->id();

            // Update card ownership and remove from pack
            foreach ($cards as $card) {
                $card->update([
                    'user_id' => $userId,
                    'is_in_pack' => false,
                    'pack_id' => null,
                    'metadata' => array_merge($card->metadata ?? [], [
                        'opened_from_pack' => $pack->id,
                        'opened_at' => $now->toISOString()
                    ])
                ]);
            }

            // Mark pack as opened and unsealed
            if (!$pack->update([
                'opened_at' => $now,
                'is_sealed' => false
            ])) {
                throw new \Exception('Failed to mark pack as opened.');
            }

            // Delete the pack after successful opening
            $pack->delete();

            DB::commit();

            return redirect()->route('cards.index', ['view' => 'grid'])
                ->with('success', 'Pack opened successfully! The cards have been added to your collection.')
                ->with('new_cards', $cards);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Pack opening failed', [
                'error' => $e->getMessage(),
                'pack_id' => $pack->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('packs.index')
                ->with('error', 'Failed to open pack: ' . $e->getMessage());
        }
    }

    public function seal(Pack $pack)
    {
        $this->authorize('seal', $pack);

        $result = $pack->seal();
        
        if (!$result['success']) {
            logger()->warning('Pack sealing failed', [
                'pack_id' => $pack->id,
                'user_id' => auth()->id(),
                'message' => $result['message']
            ]);
        } else {
            logger()->info('Pack sealed successfully', [
                'pack_id' => $pack->id,
                'user_id' => auth()->id()
            ]);
        }
        
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function destroy(Pack $pack)
    {
        $this->authorize('delete', $pack);

        try {
            DB::beginTransaction();

            // Remove all cards from the pack
            $pack->cards()->update([
                'is_in_pack' => false,
                'pack_id' => null
            ]);

            // Delete the pack
            $pack->delete();

            DB::commit();

            return redirect()->route('packs.index')
                ->with('success', 'Pack deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Pack deletion failed', [
                'error' => $e->getMessage(),
                'pack_id' => $pack->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to delete pack: ' . $e->getMessage());
        }
    }
}
