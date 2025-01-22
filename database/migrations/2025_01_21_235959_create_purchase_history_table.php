<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('price');
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamps();

            $table->index('buyer_id');
            $table->index('pack_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_history');
    }
};
