<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'original_user_id',
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
        'metadata' => 'array'
    ];

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public function originalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    public static function createFromGallery(Gallery $gallery, int $packId): self
    {
        return static::create([
            'pack_id' => $packId,
            'original_user_id' => $gallery->user_id,
            'type' => $gallery->type,
            'name' => $gallery->name,
            'image_url' => $gallery->image_url,
            'prompt' => $gallery->prompt,
            'aspect_ratio' => $gallery->aspect_ratio,
            'process_mode' => $gallery->process_mode,
            'task_id' => $gallery->task_id,
            'metadata' => $gallery->metadata,
            'mana_cost' => $gallery->mana_cost,
            'card_type' => $gallery->card_type,
            'abilities' => $gallery->abilities,
            'flavor_text' => $gallery->flavor_text,
            'power_toughness' => $gallery->power_toughness,
            'rarity' => $gallery->rarity
        ]);
    }
}
