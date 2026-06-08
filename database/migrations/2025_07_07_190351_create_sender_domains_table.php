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
        Schema::create('sender_domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spam_rule_id');
            $table->foreign('spam_rule_id')->references('id')->on('spam_rules')->onDelete('cascade');
            $table->string('domain', 255); //comment('Nom de domaine expéditeur concerné');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sender_domains');
    }
};
