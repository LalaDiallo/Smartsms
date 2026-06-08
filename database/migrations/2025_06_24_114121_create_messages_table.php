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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagnes_id')->nullable()->constrained('campagnes')->onDelete('cascade'); // FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade'); // FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
            $table->text('content'); // TEXT NOT NULL
            $table->string('subject', 255)->nullable(); // pour message.subject
            $table->dateTime('sent_at')->nullable(); // DATETIME
            $table->string('media', 255)->nullable();      // pour message.media
            $table->string('cta', 100)->nullable();        // pour message.cta
            $table->string('cta_url', 255)->nullable();    // pour message.cta_url
            $table->string('reply_token', 255)->nullable(); // pour message.reply_token
            $table->enum('status', ['scheduled', 'queued', 'sent', 'delivered', 'failed', 'spam']);
            $table->enum('channel', ['sms', 'whatsapp', 'email', 'push']); // ENUM NOT NULL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
