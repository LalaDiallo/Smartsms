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
        Schema::create('spam_detections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('keyword_id');
            $table->dateTime('detected_at');
            $table->enum('action_taken', ['flagged', 'blocked', 'reviewed']);
            $table->json('details')->nullable();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('keyword_id')->references('id')->on('spam_keywords')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spam_detections');
    }
};
