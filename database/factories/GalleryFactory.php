<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'type' => 'card',
            'name' => $this->faker->words(3, true),
            'image_url' => $this->faker->imageUrl(),
            'prompt' => $this->faker->sentence(),
            'aspect_ratio' => '1:1',
            'process_mode' => 'standard',
            'task_id' => null,
            'metadata' => json_encode([]),
            'mana_cost' => $this->faker->randomElement(['{1}{R}', '{2}{G}', '{3}{U}']),
            'card_type' => $this->faker->randomElement(['Creature', 'Instant', 'Sorcery']),
            'abilities' => $this->faker->words(3, true),
            'flavor_text' => $this->faker->sentence(),
            'power_toughness' => $this->faker->randomElement(['1/1', '2/2', '3/3']),
            'rarity' => $this->faker->randomElement(['common', 'uncommon', 'rare']),
        ];
    }
}
