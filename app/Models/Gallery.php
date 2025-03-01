<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pack_id',
        'is_in_pack',
        'original_owner_id',
        'type',
        'name',
        'image_url',
        'prompt',
        'aspect_ratio',
        'process_mode',
        'task_id',
        'metadata',
        'mana_cost',
        'card_type',
        'abilities',
        'flavor_text',
        'power_toughness',
        'rarity'
    ];

    protected $casts = [
        'is_in_pack' => 'boolean',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public function originalOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

    public function getCreatedAtForHumansAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    public function addToPack(Pack $pack): bool
    {
        try {
            if ($this->is_in_pack || $this->pack_id) {
                throw new \Exception('Card is already in a pack');
            }

            $result = $this->update([
                'pack_id' => $pack->id,
                'is_in_pack' => true,
                'original_owner_id' => $this->user_id
            ]);

            if (!$result) {
                throw new \Exception('Failed to update card with pack information');
            }

            logger()->info('Card added to pack', [
                'card_id' => $this->id,
                'pack_id' => $pack->id,
                'user_id' => $this->user_id,
                'rarity' => $this->rarity
            ]);

            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to add card to pack', [
                'error' => $e->getMessage(),
                'card_id' => $this->id,
                'pack_id' => $pack->id,
                'user_id' => $this->user_id
            ]);
            return false;
        }
    }

    public function removeFromPack(): bool
    {
        if (!$this->is_in_pack || !$this->pack_id) {
            return false;
        }

        return $this->update([
            'pack_id' => null,
            'is_in_pack' => false
        ]);
    }

    public function scopeInPack($query)
    {
        return $query->where('is_in_pack', true);
    }

    public function scopeNotInPack($query)
    {
        return $query->where('is_in_pack', false);
    }

    public function scopeCards($query)
    {
        return $query->where('type', 'card');
    }
}
