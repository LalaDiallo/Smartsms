<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_preferences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contact_id')
                  ->constrained('contacts')
                  ->cascadeOnDelete();

            $table->json('preferred_days')->nullable();   // ["monday","friday"]
            $table->json('preferred_hours')->nullable();  // ["08:00-12:00"]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_preferences');
    }
};

