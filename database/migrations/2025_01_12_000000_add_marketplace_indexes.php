<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packs', function (Blueprint $table) {
            // Add indexes for marketplace queries
            $table->index(['is_listed', 'listed_at']);
            $table->index(['user_id', 'is_listed']);
            $table->index(['price', 'listed_at']);
        });

        Schema::table('credit_transactions', function (Blueprint $table) {
            // Add indexes for marketplace transactions
            $table->index(['user_id', 'created_at']);
            $table->index(['pack_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('packs', function (Blueprint $table) {
            $table->dropIndex(['is_listed', 'listed_at']);
            $table->dropIndex(['user_id', 'is_listed']);
            $table->dropIndex(['price', 'listed_at']);
        });

        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['pack_id', 'created_at']);
        });
    }
};
