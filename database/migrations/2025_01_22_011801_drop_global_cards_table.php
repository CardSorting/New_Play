<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('global_cards');
    }

    public function down()
    {
        Schema::create('global_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained();
            $table->string('name');
            $table->string('image_url');
            $table->string('prompt');
            $table->string('aspect_ratio');
            $table->string('process_mode');
            $table->string('task_id');
            $table->json('metadata');
            $table->timestamps();
        });
    }
};
