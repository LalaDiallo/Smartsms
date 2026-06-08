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
        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create('notifications', function (Blueprint $table) {

            $table->id();

            // Celui qui reçoit la notification
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Celui qui déclenche (optionnel)
            $table->foreignId('from_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Type : campaign, sms, credit, system...
            $table->string('type')->default("system");

            // Message affiché
            $table->string('title')->nullable();
            $table->text('message');

            // Liens vers une campagne par exemple
            $table->unsignedBigInteger('campaign_id')->nullable();

            // ✅ Statut lecture
            $table->boolean('is_read')->default(false);

            // ✅ Date de lecture
            $table->timestamp('read_at')->nullable();

            // Données extra JSON
            $table->json('data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
