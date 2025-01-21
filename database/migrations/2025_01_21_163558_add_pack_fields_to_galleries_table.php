<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Add pack-related fields
            $table->foreignId('pack_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->boolean('is_in_pack')->default(false)->after('pack_id');
            $table->foreignId('original_owner_id')->nullable()->after('is_in_pack')->constrained('users')->onDelete('set null');
        });

        // Migrate existing cards from global_cards
        DB::statement('
            INSERT INTO galleries (
                user_id,
                pack_id,
                is_in_pack,
                original_owner_id,
                type,
                name,
                image_url,
                prompt,
                aspect_ratio,
                process_mode,
                task_id,
                metadata,
                mana_cost,
                card_type,
                abilities,
                flavor_text,
                power_toughness,
                rarity,
                created_at,
                updated_at
            )
            SELECT 
                p.user_id,
                gc.pack_id,
                true,
                gc.original_user_id,
                gc.type,
                gc.name,
                gc.image_url,
                gc.prompt,
                gc.aspect_ratio,
                gc.process_mode,
                gc.task_id,
                gc.metadata,
                gc.mana_cost,
                gc.card_type,
                gc.abilities,
                gc.flavor_text,
                gc.power_toughness,
                gc.rarity,
                gc.created_at,
                gc.updated_at
            FROM global_cards gc
            JOIN packs p ON gc.pack_id = p.id
            WHERE NOT EXISTS (
                -- Skip if card already exists in galleries
                SELECT 1 FROM galleries g 
                WHERE g.pack_id = gc.pack_id 
                AND g.name = gc.name
                AND g.image_url = gc.image_url
            )
        ');

        // Drop global_cards table
        Schema::dropIfExists('global_cards');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate global_cards table
        Schema::create('global_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained()->onDelete('cascade');
            $table->foreignId('original_user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('name');
            $table->string('image_url');
            $table->text('prompt')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->string('process_mode')->nullable();
            $table->string('task_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('mana_cost')->nullable();
            $table->string('card_type')->nullable();
            $table->text('abilities')->nullable();
            $table->text('flavor_text')->nullable();
            $table->string('power_toughness')->nullable();
            $table->string('rarity')->nullable();
            $table->timestamps();
        });

        // Move cards back to global_cards
        DB::statement('
            INSERT INTO global_cards (
                pack_id,
                original_user_id,
                type,
                name,
                image_url,
                prompt,
                aspect_ratio,
                process_mode,
                task_id,
                metadata,
                mana_cost,
                card_type,
                abilities,
                flavor_text,
                power_toughness,
                rarity,
                created_at,
                updated_at
            )
            SELECT 
                pack_id,
                original_owner_id,
                type,
                name,
                image_url,
                prompt,
                aspect_ratio,
                process_mode,
                task_id,
                metadata,
                mana_cost,
                card_type,
                abilities,
                flavor_text,
                power_toughness,
                rarity,
                created_at,
                updated_at
            FROM galleries
            WHERE is_in_pack = true
        ');

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropForeign(['pack_id']);
            $table->dropForeign(['original_owner_id']);
            $table->dropColumn(['pack_id', 'is_in_pack', 'original_owner_id']);
        });
    }
};
