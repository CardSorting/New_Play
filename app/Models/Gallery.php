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
        if ($this->is_in_pack || $this->pack_id) {
            return false;
        }

        return $this->update([
            'pack_id' => $pack->id,
            'is_in_pack' => true,
            'original_owner_id' => $this->user_id
        ]);
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
