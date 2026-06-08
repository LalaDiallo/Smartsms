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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique(); // ex: email-marketing
            $table->timestamps();
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('extrait');
            $table->enum('channel', ['sms', 'whatsapp', 'email', 'push']);
            $table->text('content');
            $table->boolean('is_favori')->default(false);
            $table->string('branding_logo', 255)->nullable();
            $table->json('branding_colors')->nullable();
            $table->string('sector', 100)->nullable();

            $table->foreignId('language_id')
                  ->nullable()
                  ->constrained('languages')
                  ->onDelete('set null');

            $table->foreignId('category_id')
                  ->constrained()
                  ->onDelete('cascade');


            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->onDelete('set null');

            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('tag_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ⚠️ ordre inverse pour éviter les conflits de clés étrangères
        Schema::dropIfExists('tag_template');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('categories');
    }
};
