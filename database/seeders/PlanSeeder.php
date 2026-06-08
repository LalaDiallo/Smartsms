<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\SmsPricingTier;
use Illuminate\Database\Seeder;

/**
 * Seeder du modèle Plan (legacy) — aligné sur le modèle économique actuel.
 *
 * Plans :
 *   Freemium  — 0 GNF   — 50 SMS/mois   — 120 GNF/SMS supp.
 *   Starter   — 250 000 — 2 000 SMS/mois — 110 GNF/SMS supp.
 *   Pro       — 1 200 000 — 10 000 SMS/mois — 100 GNF/SMS supp.
 *   Enterprise — 4 500 000 — 40 000 SMS/mois — 95 GNF/SMS supp.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ── Freemium ──────────────────────────────────────────────────
            [
                'name'                  => 'Freemium',
                'price'                 => 0.00,
                'currency'              => 'GNF',
                'included_sms_volume'   => 50,
                'overuse_price_per_sms' => 120.00,
                'limitations'           => 'Accès limité · 50 SMS offerts · 1 mois d\'essai gratuit',
                'is_active'             => true,
                'features'              => [
                    'Accès limité à l\'interface',
                    'Modèles de messages basiques',
                    'Ciblage manuel',
                    'Aucun engagement',
                    '50 SMS offerts',
                    '1 mois d\'essai gratuit',
                ],
                'tiers' => [
                    ['min' => 1,    'max' => 10000,  'price' => 120.00],
                    ['min' => 10001,'max' => 50000,  'price' => 115.00],
                    ['min' => 50001,'max' => 100000, 'price' => 110.00],
                    ['min' => 100001,'max' => null,  'price' => 105.00],
                ],
            ],

            // ── Starter (TPE) ─────────────────────────────────────────────
            [
                'name'                  => 'Starter',
                'price'                 => 250000.00,
                'currency'              => 'GNF',
                'included_sms_volume'   => 2000,
                'overuse_price_per_sms' => 110.00,
                'limitations'           => 'Sans traductions automatiques ni API.',
                'is_active'             => true,
                'features'              => [
                    'Ciblage manuel',
                    'Rapports simples',
                    'Support standard',
                    'Report SMS 1 mois',
                    '2 000 SMS inclus/mois',
                ],
                'tiers' => [
                    ['min' => 1,    'max' => 10000,  'price' => 110.00],
                    ['min' => 10001,'max' => 50000,  'price' => 105.00],
                    ['min' => 50001,'max' => 100000, 'price' => 100.00],
                    ['min' => 100001,'max' => null,  'price' => 95.00],
                ],
            ],

            // ── Pro (PME) ─────────────────────────────────────────────────
            [
                'name'                  => 'Pro',
                'price'                 => 1200000.00,
                'currency'              => 'GNF',
                'included_sms_volume'   => 10000,
                'overuse_price_per_sms' => 100.00,
                'limitations'           => 'Sans support dédié 24/7.',
                'is_active'             => true,
                'features'              => [
                    'Ciblage avancé',
                    'Traductions automatiques',
                    'API pour intégration',
                    'Report SMS 1 mois',
                    '10 000 SMS inclus/mois',
                ],
                'tiers' => [
                    ['min' => 1,    'max' => 10000,  'price' => 100.00],
                    ['min' => 10001,'max' => 50000,  'price' => 95.00],
                    ['min' => 50001,'max' => 100000, 'price' => 90.00],
                    ['min' => 100001,'max' => null,  'price' => 85.00],
                ],
            ],

            // ── Enterprise ────────────────────────────────────────────────
            [
                'name'                  => 'Enterprise',
                'price'                 => 4500000.00,
                'currency'              => 'GNF',
                'included_sms_volume'   => 40000,
                'overuse_price_per_sms' => 95.00,
                'limitations'           => 'Aucune limitation. Accès total.',
                'is_active'             => true,
                'features'              => [
                    'Accès complet à toutes les fonctionnalités',
                    'Support prioritaire',
                    'API avancée',
                    'Report SMS illimité',
                    '40 000 SMS inclus/mois',
                    'Rapports analytiques avancés',
                    'Fonctionnalités premium exclusives',
                ],
                'tiers' => [
                    ['min' => 1,    'max' => 10000,  'price' => 95.00],
                    ['min' => 10001,'max' => 50000,  'price' => 90.00],
                    ['min' => 50001,'max' => 100000, 'price' => 85.00],
                    ['min' => 100001,'max' => null,  'price' => 80.00],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $plan = Plan::create([
                'name'                  => $planData['name'],
                'price'                 => $planData['price'],
                'currency'              => $planData['currency'],
                'included_sms_volume'   => $planData['included_sms_volume'],
                'overuse_price_per_sms' => $planData['overuse_price_per_sms'],
                'limitations'           => $planData['limitations'],
                'is_active'             => $planData['is_active'],
                'created_at'            => Carbon::now(),
                'updated_at'            => Carbon::now(),
            ]);

            foreach ($planData['features'] as $featureText) {
                PlanFeature::create([
                    'plan_id'    => $plan->id,
                    'feature'    => $featureText,
                    'enabled'    => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            foreach ($planData['tiers'] as $tier) {
                SmsPricingTier::create([
                    'plan_id'       => $plan->id,
                    'min_volume'    => $tier['min'],
                    'max_volume'    => $tier['max'],
                    'price_per_sms' => $tier['price'],
                    'currency'      => 'GNF',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
            }
        }
    }
}
