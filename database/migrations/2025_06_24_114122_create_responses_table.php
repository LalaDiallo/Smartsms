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
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')
                  ->constrained('messages')
                  ->onDelete('cascade'); // FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
            $table->foreignId('contact_id')
                  ->constrained('contacts')
                  ->onDelete('cascade'); // FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
            $table->text('content'); // TEXT NOT NULL
            $table->dateTime('received_at'); // DATETIME NOT NULL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
