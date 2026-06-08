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
        Schema::create('frequences', function (Blueprint $table) {
            $table->id();
            $table->integer('frequency_limit')->default(10); //Limite de fréquence (nombre de messages autorisés)
            $table->enum('frequency_period', ['minute', 'hour', 'day'])->default('hour'); //Période de fréquence (minute, heure, jour)
            $table->unsignedBigInteger('spam_rule_id');
            $table->foreign('spam_rule_id')->references('id')->on('spam_rules')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequences');
    }
};
