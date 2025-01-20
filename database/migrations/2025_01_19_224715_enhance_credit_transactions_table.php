<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up()
    {
        // Add running balance and indexes
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->bigInteger('running_balance')->after('amount')->nullable();
            
            // Optimize queries with composite indexes
            $table->index(['user_id', 'type', 'created_at'], 'idx_user_type_created');
            $table->index(['user_id', 'running_balance'], 'idx_user_balance');
        });

        // Add check constraint using raw SQL
        DB::statement('ALTER TABLE credit_transactions ADD CONSTRAINT check_positive_amount CHECK (amount > 0)');

        // Initialize running balances for existing records
        DB::statement('
            WITH running_totals AS (
                SELECT 
                    id,
                    user_id,
                    SUM(
                        CASE 
                            WHEN type = \'credit\' THEN amount 
                            WHEN type = \'debit\' THEN -amount 
                            ELSE 0 
                        END
                    ) OVER (
                        PARTITION BY user_id 
                        ORDER BY created_at, id
                    ) as running_total
                FROM credit_transactions
            )
            UPDATE credit_transactions
            SET running_balance = running_totals.running_total
            FROM running_totals
            WHERE credit_transactions.id = running_totals.id
        ');

        // Make running_balance required after initialization
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->bigInteger('running_balance')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropColumn('running_balance');
            $table->dropIndex('idx_user_type_created');
            $table->dropIndex('idx_user_balance');
        });

        // Drop check constraint
        DB::statement('ALTER TABLE credit_transactions DROP CONSTRAINT IF EXISTS check_positive_amount');
    }
};