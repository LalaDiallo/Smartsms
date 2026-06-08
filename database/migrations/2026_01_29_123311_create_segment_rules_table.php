<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('segment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_id')
                  ->constrained('groupes')
                  ->cascadeOnDelete();

            $table->string('field');        // ex: country, language, last_opened_at
            $table->string('operator');     // =, !=, >, <, IN
            $table->string('value');        // valeur ou JSON
            $table->enum('logical', ['AND', 'OR'])->default('AND');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segment_rules');
    }
};

