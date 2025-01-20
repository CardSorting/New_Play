<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('type')->default('image')->after('user_id');
            $table->string('name')->nullable()->after('type');
            $table->string('mana_cost')->nullable()->after('metadata');
            $table->string('card_type')->nullable()->after('mana_cost');
            $table->text('abilities')->nullable()->after('card_type');
            $table->text('flavor_text')->nullable()->after('abilities');
            $table->string('power_toughness')->nullable()->after('flavor_text');
            $table->string('rarity')->nullable()->after('power_toughness');
            
            // Make some columns nullable since they're only required for images
            $table->text('prompt')->nullable()->change();
            $table->string('aspect_ratio')->nullable()->change();
            $table->string('process_mode')->nullable()->change();
            $table->string('task_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'name',
                'mana_cost',
                'card_type',
                'abilities',
                'flavor_text',
                'power_toughness',
                'rarity'
            ]);
            
            // Revert nullable changes
            $table->text('prompt')->nullable(false)->change();
            $table->string('aspect_ratio')->nullable(false)->change();
            $table->string('process_mode')->nullable(false)->change();
            $table->string('task_id')->nullable(false)->change();
        });
    }
};
