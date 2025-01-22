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
        if (!Schema::hasColumn('packs', 'opened_at')) {
            Schema::table('packs', function (Blueprint $table) {
                $table->timestamp('opened_at')->nullable()->after('is_sealed');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('packs', 'opened_at')) {
            Schema::table('packs', function (Blueprint $table) {
                $table->dropColumn('opened_at');
            });
        }
    }
};
