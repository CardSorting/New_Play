<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pack_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('credit_transactions')->onDelete('cascade');
            $table->decimal('sale_amount', 10, 2);
            $table->timestamp('sale_date')->useCurrent();
            $table->string('status')->default('completed');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'sale_date']);
            $table->index(['pack_id', 'sale_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_history');
    }
};
