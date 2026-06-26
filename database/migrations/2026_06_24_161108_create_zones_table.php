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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('name');
            $table->string('type', 20)->default('region'); // region|city|sector
            $table->string('status', 20)->default('active'); // active|suspended
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unique(['client_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
