<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('token', 255);                   // FCM registration token (max ~163 chars)
            $table->enum('platform', ['web', 'android', 'ios'])->default('web');
            $table->string('user_agent', 512)->nullable();  // pour debug
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token'], 'device_tokens_user_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
