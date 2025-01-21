<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('packs', function (Blueprint $table) {
            // Add index for opened_at and modify column
            $table->timestamp('opened_at')->nullable()->index()->change();
            
            // Add foreign key constraint
            $table->foreignId('gallery_id')->after('user_id')
                ->constrained('galleries')
                ->onDelete('cascade');
                
            // Add processing tracker
            $table->timestamp('collection_processed_at')->nullable()
                ->comment('Tracks background job completion');
        });

        // Create composite index separately
        Schema::table('packs', function (Blueprint $table) {
            $table->index(['user_id', 'opened_at']);
        });
    }

    public function down()
    {
        Schema::table('packs', function (Blueprint $table) {
            $table->dropForeign(['gallery_id']);
            $table->dropIndex(['opened_at']);
            $table->dropIndex(['user_id_opened_at_index']);
            $table->dropColumn(['gallery_id', 'collection_processed_at']);
        });
    }
};
