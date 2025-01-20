<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->string('type'); // credit, debit
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('pack_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Add indexes for efficient balance calculations and lookups
            $table->index(['user_id', 'type']);
            $table->index(['pack_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
