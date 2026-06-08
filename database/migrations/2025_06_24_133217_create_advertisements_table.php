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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campagnes_id')->nullable();
            $table->text('content');
            $table->string('target_region', 100)->nullable();
            $table->json('target_interests')->nullable();
            $table->decimal('cost_reduction', 10, 2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreign('campagnes_id')->references('id')->on('campagnes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
