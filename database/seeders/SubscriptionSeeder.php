<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Clients;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\BillingCycle;
use Illuminate\Database\Seeder;

/**
 * Crée des abonnements réalistes pour chaque client seedé.
 * Dépend de : ClientsSeeder, et des migrations qui ont inséré
 * subscription_plans, billing_cycles et extra_sms_pricing.
 */
class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Charger les plans par slug
        $plans = SubscriptionPlan::all()->keyBy('slug');

        if ($plans->isEmpty()) {
            $this->command->warn('Aucun plan trouvé — lancez d\'abord les migrations.');
            return;
        }

        $monthly  = BillingCycle::where('months', 1)->first();
        $annual   = BillingCycle::where('months', 12)->first();
        $semester = BillingCycle::where('months', 6)->first();

        if (!$monthly) {
            $this->command->warn('Cycle mensuel introuvable — migrations manquantes ?');
            return;
        }

        $clients = Clients::all();

        foreach ($clients as $client) {
            // Sélectionner le plan selon le revenu mensuel du client
            $planSlug = match (true) {
                $client->monthly_revenue >= 4000000 => 'enterprise',
                $client->monthly_revenue >= 1200000 => 'pro',
                $client->monthly_revenue >= 250000  => 'starter',
                default                             => 'freemium',
            };

            $plan  = $plans[$planSlug] ?? $plans['freemium'];
            $cycle = match ($planSlug) {
                'enterprise' => $annual   ?? $monthly,
                'pro'        => $semester ?? $monthly,
                default      => $monthly,
            };

            // Dates selon le statut du client
            $isActive    = in_array($client->status, ['active', 'essaie']);
            $joinedAt    = $client->joined_at instanceof Carbon
                ? $client->joined_at
                : Carbon::parse($client->joined_at ?? now()->subMonths(3));

            $startDate   = $joinedAt;
            $endDate     = $isActive
                ? now()->addMonths((int) $cycle->months)
                : now()->subDays(rand(5, 30));    // expiré si suspendu/résilié

            $smsQuota    = $plan->smsIncludedForCycle($cycle);
            // Simuler une utilisation réaliste (20–80 %)
            $usageRatio  = $isActive ? rand(20, 80) / 100 : rand(70, 100) / 100;
            $smsUsed     = (int) round($smsQuota * $usageRatio);

            Subscription::create([
                'client_id'            => $client->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle_id'     => $cycle->id,
                'start_date'           => $startDate,
                'end_date'             => $endDate,
                'next_billing_date'    => $isActive ? $endDate : null,
                'status'               => $isActive ? 'active' : 'expired',
                'auto_renew'           => $isActive && $planSlug !== 'freemium',
                'price'                => $plan->basePriceForCycle($cycle),
                'currency'             => 'GNF',
                'sms_quota'            => $smsQuota,
                'sms_used'             => min($smsUsed, $smsQuota),
            ]);

            // Mettre à jour plan_id du client pour la compatibilité rétrograde
            $client->update(['plan_id' => $plan->id]);

            $this->command->line("  ✓ {$client->company_name} → {$plan->name} ({$cycle->name})");
        }

        $this->command->info('SubscriptionSeeder terminé — ' . $clients->count() . ' abonnements créés.');
    }
}
