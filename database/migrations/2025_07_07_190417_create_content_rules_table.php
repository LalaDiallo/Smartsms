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
        Schema::create('content_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spam_rule_id');
            $table->foreign('spam_rule_id')->references('id')->on('spam_rules')->onDelete('cascade');
            $table->string('rule', 255); //comment('Règle de contenu associée');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_rules');
    }
};
