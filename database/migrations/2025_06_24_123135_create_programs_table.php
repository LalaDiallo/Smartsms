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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // VARCHAR(100) NOT NULL
            $table->text('description')->nullable(); // TEXT
            $table->integer('points_per_action'); // INT NOT NULL
            $table->enum('reward_type', ['discount', 'gift', 'cash']); // ENUM NOT NULL
            $table->decimal('reward_value', 10, 2)->nullable(); // DECIMAL(10, 2)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
