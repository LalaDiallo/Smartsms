<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Plans d'abonnement
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Freemium, Starter (TPE), ...
            $table->string('slug')->unique();          // freemium, starter, pro, enterprise
            $table->bigInteger('price_monthly_base');  // prix de référence mensuel
            $table->integer('sms_included_monthly');
            $table->integer('sms_price_reference');    // prix "théorique" du SMS inclus
            $table->integer('rollover_months')->nullable(); // null = illimité
            $table->json('features')->nullable();
            $table->boolean('is_freemium')->default(false);
            $table->boolean('has_long_term_discounts')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // 2. Périodes d'engagement (mensuel, trimestriel, etc.)
        Schema::create('billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Mensuel, Trimestriel, Semestriel, Annuel
            $table->integer('months');
            $table->decimal('discount_percent', 5, 2)->default(0); // ex: 17.00
            $table->decimal('sms_bonus_percent', 5, 2)->default(0); // ex: 15.00
            $table->boolean('priority_support')->default(false);
            $table->boolean('advanced_reports')->default(false);
            $table->boolean('premium_features')->default(false);
            $table->timestamps();
        });

        // 3. Tarifs dégressifs SMS supplémentaires (par plan)
        Schema::create('extra_sms_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();
            $table->integer('price_per_sms');
            $table->timestamps();
        });

        // 4. Données de base : tout ton modèle économique
        DB::transaction(function () {

            // === Billing Cycles ===
            DB::table('billing_cycles')->insert([
                ['name' => 'Mensuel',     'months' => 1,  'discount_percent' => 0,    'sms_bonus_percent' => 0,   'priority_support' => false, 'advanced_reports' => false, 'premium_features' => false],
                ['name' => 'Trimestriel', 'months' => 3,  'discount_percent' => 6.67, 'sms_bonus_percent' => 5,   'priority_support' => false, 'advanced_reports' => false, 'premium_features' => false],
                ['name' => 'Semestriel',  'months' => 6,  'discount_percent' => 10,   'sms_bonus_percent' => 10,  'priority_support' => true,  'advanced_reports' => true,  'premium_features' => false],
                ['name' => 'Annuel',      'months' => 12, 'discount_percent' => 16.67,'sms_bonus_percent' => 15,  'priority_support' => true,  'advanced_reports' => true,  'premium_features' => true],
            ]);

            // === Plans ===
            $plans = [
                [
                    'name' => 'Freemium',
                    'slug' => 'freemium',
                    'price_monthly_base' => 0,
                    'sms_included_monthly' => 50,
                    'sms_price_reference' => 120,
                    'rollover_months' => 0,
                    'is_freemium' => true,
                    'features' => json_encode(['Accès limité', 'Modèles basiques', 'Cibl NASAage manuel', 'Validité illimitée'], JSON_UNESCAPED_UNICODE),
                ],
                [
                    'name' => 'Starter (TPE)',
                    'slug' => 'starter',
                    'price_monthly_base' => 250000,
                    'sms_included_monthly' => 2000,
                    'sms_price_reference' => 110,
                    'rollover_months' => 1,
                    'is_freemium' => false,
                    'features' => json_encode(['Ciblage manuel', 'Rapports simples', 'Support standard'], JSON_UNESCAPED_UNICODE),
                ],
                [
                    'name' => 'Pro (PME)',
                    'slug' => 'pro',
                    'price_monthly_base' => 1200000,
                    'sms_included_monthly' => 10000,
                    'sms_price_reference' => 100,
                    'rollover_months' => 1,
                    'is_freemium' => false,
                    'features' => json_encode(['Ciblage avancé', 'Traductions auto', 'API intégration'], JSON_UNESCAPED_UNICODE),
                ],
                [
                    'name' => 'Enterprise',
                    'slug' => 'enterprise',
                    'price_monthly_base' => 4000000,
                    'sms_included_monthly' => 40000,
                    'sms_price_reference' => 95,
                    'rollover_months' => null,
                    'is_freemium' => false,
                    'features' => json_encode(['Tout inclus', 'Support prioritaire', 'API avancée', 'Report illimité'], JSON_UNESCAPED_UNICODE),
                ],
            ];

            foreach ($plans as $plan) {
                $planId = DB::table('subscription_plans')->insertGetId($plan + ['created_at' => now(), 'updated_at' => now()]);

                // === Tarifs dégressifs par plan ===
                $tiers = match ($plan['slug']) {
                    'freemium' => [[1,10000,110], [10001,50000,105], [50001,100000,100], [100001,null,95]],
                    'starter'  => [[1,10000,110], [10001,50000,105], [50001,100000,100], [100001,null,95]],
                    'pro'      => [[1,10000,100], [10001,50000,95],  [50001,100000,90],  [100001,null,85]],
                    'enterprise'=>[[1,10000,95],  [10001,50000,90],  [50001,100000,85],  [100001,null,80]],
                };

                foreach ($tiers as $tier) {
                    DB::table('extra_sms_pricing')->insert([
                        'subscription_plan_id' => $planId,
                        'min_quantity' => $tier[0],
                        'max_quantity' => $tier[1],
                        'price_per_sms' => $tier[2],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_sms_pricing');
        Schema::dropIfExists('billing_cycles');
        Schema::dropIfExists('subscription_plans');
    }
};
