<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Correction des données du modèle économique :
 * - Prix Enterprise aligné sur le tableau long terme (4 500 000 GNF/mois)
 * - Features Freemium (correction typo)
 * - Freemium marqué sans remise long terme
 * - Features complètes selon le document économique
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            // ── Freemium ────────────────────────────────────────────────────
            DB::table('subscription_plans')
                ->where('slug', 'freemium')
                ->update([
                    'features'                => json_encode([
                        'Accès limité à l\'interface',
                        'Modèles de messages basiques',
                        'Ciblage manuel',
                        'Aucun engagement',
                        '50 SMS offerts',
                        '1 mois d\'essai gratuit',
                    ], JSON_UNESCAPED_UNICODE),
                    'has_long_term_discounts' => false,  // pas de remise long terme pour le freemium
                    'updated_at'              => now(),
                ]);

            // ── Starter (TPE) ───────────────────────────────────────────────
            DB::table('subscription_plans')
                ->where('slug', 'starter')
                ->update([
                    'features'   => json_encode([
                        'Ciblage manuel',
                        'Rapports simples',
                        'Support standard',
                        'Report SMS 1 mois',
                        '2 000 SMS inclus/mois',
                    ], JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);

            // ── Pro (PME) ───────────────────────────────────────────────────
            DB::table('subscription_plans')
                ->where('slug', 'pro')
                ->update([
                    'features'   => json_encode([
                        'Ciblage avancé',
                        'Traductions automatiques',
                        'API pour intégration',
                        'Report SMS 1 mois',
                        '10 000 SMS inclus/mois',
                    ], JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);

            // ── Enterprise ──────────────────────────────────────────────────
            // Prix corrigé à 4 500 000 GNF/mois (cohérent avec le tableau long terme)
            DB::table('subscription_plans')
                ->where('slug', 'enterprise')
                ->update([
                    'price_monthly_base' => 4500000,
                    'features'           => json_encode([
                        'Accès complet à toutes les fonctionnalités',
                        'Support prioritaire',
                        'API avancée',
                        'Report SMS illimité',
                        '40 000 SMS inclus/mois',
                        'Rapports analytiques avancés',
                        'Fonctionnalités premium exclusives',
                    ], JSON_UNESCAPED_UNICODE),
                    'updated_at'         => now(),
                ]);

            // ── Billing cycles – bonus SMS alignés sur le document ──────────
            // Trimestriel : +5 %
            DB::table('billing_cycles')->where('months', 3)->update([
                'discount_percent'  => 6.67,
                'sms_bonus_percent' => 5.00,
                'updated_at'        => now(),
            ]);

            // Semestriel : +10 %, support prioritaire, rapports avancés
            DB::table('billing_cycles')->where('months', 6)->update([
                'discount_percent'  => 10.00,
                'sms_bonus_percent' => 10.00,
                'priority_support'  => true,
                'advanced_reports'  => true,
                'updated_at'        => now(),
            ]);

            // Annuel : +15 %, toutes fonctionnalités premium
            DB::table('billing_cycles')->where('months', 12)->update([
                'discount_percent'  => 16.67,
                'sms_bonus_percent' => 15.00,
                'priority_support'  => true,
                'advanced_reports'  => true,
                'premium_features'  => true,
                'updated_at'        => now(),
            ]);
        });
    }

    public function down(): void
    {
        // Retour aux valeurs originales de la migration initiale
        DB::table('subscription_plans')->where('slug', 'freemium')->update([
            'features'                => json_encode(['Accès limité', 'Modèles basiques', 'Ciblage manuel', 'Validité illimitée'], JSON_UNESCAPED_UNICODE),
            'has_long_term_discounts' => true,
            'updated_at'              => now(),
        ]);

        DB::table('subscription_plans')->where('slug', 'enterprise')->update([
            'price_monthly_base' => 4000000,
            'updated_at'         => now(),
        ]);
    }
};
