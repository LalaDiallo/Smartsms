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
        Schema::create('developer_api_keys', function (Blueprint $table) {
            $table->id();

            // Propriétaire de la clé
            $table->foreignId('client_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Données visibles dans l’UI
            $table->string('name');
            $table->string('service_id')->unique();
            $table->string('secret_token'); // hashé
            $table->string('webhook_url')->nullable();

            // Sécurité & usage
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_api_keys');
    }
};
