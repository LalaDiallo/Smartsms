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
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            $table->string('name', 100); // VARCHAR(100) NOT NULL
            $table->enum('status', ['brouillon','programmer', 'attente', 'terminer', 'rejeter'])->default('brouillon');
            $table->boolean('archived')->default(false);
            $table->dateTime('start_date')->nullable(); // DATETIME
            $table->dateTime('end_date')->nullable(); // DATETIME
            $table->string('region', 100)->nullable(); // VARCHAR(100)
            $table->enum('channel', ['sms', 'whatsapp', 'email', 'push']); // ENUM NOT NULL
            $table->foreignId('template_id')
                  ->nullable()
                  ->constrained('templates')
                  ->onDelete('set null'); // FOREIGN KEY (template_id) REFERENCES
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagnes');
    }
};
