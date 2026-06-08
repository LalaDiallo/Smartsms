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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade'); // FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
            $table->string('first_name', 100); // VARCHAR(100) NOT NULL
            $table->string('last_name', 100)->nullable(); // VARCHAR(100)
            $table->string('email', 255)->unique()->nullable(); // VARCHAR(255)
            $table->string('phone', 20)->unique()->nullable(); // VARCHAR(20) NOT NULL
            $table->string('region', 100)->nullable(); // VARCHAR(100)
            $table->enum('preferred_channel', ['sms', 'whatsapp', 'email', 'push'])->default('sms'); // ENUM DEFAULT 'sms'
            $table->boolean('is_spammer')->default(false); // BOOLEAN DEFAULT FALSE
            $table->enum('status', ['active', 'inactive','NotInsert','employes'])->default('active'); // ENUM DEFAULT 'active'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
