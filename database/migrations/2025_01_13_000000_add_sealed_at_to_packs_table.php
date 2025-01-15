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
        Schema::table('packs', function (Blueprint $table) {
            $table->timestamp('sealed_at')->nullable()->after('is_sealed');
        });

        // Update existing sealed packs
        DB::table('packs')
            ->where('is_sealed', true)
            ->whereNull('sealed_at')
            ->update(['sealed_at' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packs', function (Blueprint $table) {
            $table->dropColumn('sealed_at');
        });
    }
};
