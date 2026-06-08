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
        Schema::create('training_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->enum('type', ['video', 'manual', 'live_session']);
            $table->string('content_url', 255)->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('sector', 100)->nullable();

            $table->foreign('language_id')->references('id')->on('languages')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_resources');
    }
};
