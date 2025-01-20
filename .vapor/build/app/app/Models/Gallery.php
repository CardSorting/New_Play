<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtForHumansAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }
}
