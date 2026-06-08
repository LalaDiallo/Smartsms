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
        Schema::create('marketplace_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('service_type', 100)->nullable(); // Ex.: contenu, traduction
            $table->string('contact_email', 255)->nullable();
            $table->string('country', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_partners');
    }
};
