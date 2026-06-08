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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // VARCHAR(100) NOT NULL
            $table->string('country', 100)->nullable(); // VARCHAR(100)
            $table->decimal('cost_per_sms', 10, 2)->nullable(); // DECIMAL(10, 2)
            $table->decimal('reliability_score', 5, 2)->nullable(); // DECIMAL(5, 2)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
