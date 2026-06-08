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
        Schema::create('conflicts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campagnes_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('conflict_type', ['modification', 'access', 'budget']);
            $table->text('description');
            $table->enum('status', ['detected', 'alerted', 'resolved'])->default('detected');
            $table->text('resolution_suggestion')->nullable();

            $table->foreign('campagnes_id')->references('id')->on('campagnes')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conflicts');
    }
};
