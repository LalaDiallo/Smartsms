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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // VARCHAR(100) NOT NULL
            $table->string('region', 100)->nullable(); // VARCHAR(100)
            $table->date('event_date')->nullable(); // DATE
            $table->enum('category', ['cultural', 'commercial', 'other']); // ENUM NOT NULL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
