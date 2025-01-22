<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->foreignId('pack_id')->nullable()->constrained();
            $table->boolean('is_in_pack')->default(false);
            $table->foreignId('original_owner_id')->nullable()->constrained('users');
        });
    }

    public function down()
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropForeign(['pack_id']);
            $table->dropForeign(['original_owner_id']);
            $table->dropColumn(['pack_id', 'is_in_pack', 'original_owner_id']);
        });
    }
};
