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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_cards');
    }
};
