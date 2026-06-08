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
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campagnes_id');
            $table->string('metric_name', 100);
            $table->decimal('metric_value', 10, 2);
            $table->dateTime('recorded_at');
            $table->timestamps();

            $table->foreign('campagnes_id')->references('id')->on('campagnes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
