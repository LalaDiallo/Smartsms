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
        Schema::create('spam_violations', function (Blueprint $table) {
            $table->id();

            // 🔑 TOUJOURS CONNU
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            // 🎯 OPTIONNEL (null si bloqué avant création)
            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained('campagnes')
                ->nullOnDelete();

            $table->foreignId('spam_rule_id')
                ->constrained('spam_rules')
                ->cascadeOnDelete();

            $table->string('channel'); // sms, email, whatsapp, push

            $table->enum('action', ['allow', 'warn', 'block', 'quarantine']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);

            $table->text('reason');
            $table->integer('score')->default(0);

            $table->timestamps();

            $table->index(['client_id', 'action', 'severity']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spam_violations');
    }
};
