<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('GNF'); // ex: GNF, EUR, USD
            $table->integer('included_sms_volume')->default(0);
            $table->decimal('overuse_price_per_sms', 10, 2)->default(0.00); // Prix par SMS supplémentaire
            $table->text('limitations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tarification par paliers (pour les volumes > included_sms_volume)
        Schema::create('sms_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')
                  ->constrained('plans')
                  ->onDelete('cascade');

            $table->integer('min_volume');
            $table->integer('max_volume')->nullable(); // null = illimité
            $table->decimal('price_per_sms', 10, 2);
            $table->string('currency', 3)->default('GNF');

            $table->timestamps();
        });

        // Fonctionnalités incluses dans chaque plan
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')
                  ->constrained('plans')
                  ->onDelete('cascade');

            $table->string('feature', 255);
            $table->boolean('enabled')->default(true);

            $table->timestamps();

            // Optionnel : unicité par plan + feature
            $table->unique(['plan_id', 'feature']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('sms_pricing_tiers');
        Schema::dropIfExists('plans');
    }
};
