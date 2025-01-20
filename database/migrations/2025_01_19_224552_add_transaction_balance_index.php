<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            // Add composite index for balance calculation queries
            $table->index(['user_id', 'type', 'amount'], 'credit_transactions_balance_idx');
        });
    }

    public function down()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropIndex('credit_transactions_balance_idx');
        });
    }
};