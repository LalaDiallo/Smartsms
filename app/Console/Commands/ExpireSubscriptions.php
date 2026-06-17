<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ExpireSubscriptions extends Command
{
    protected $signature   = 'subscriptions:expire';
    protected $description = 'Expire les abonnements échus, convertit le Freemium en fallback permanent, réactive le Freemium après expiration d\'un plan payant';

    public function handle(): int
    {
        $expired = Subscription::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->with('plan')
            ->get();

        $count = 0;

        foreach ($expired as $sub) {
            // ── Freemium : fin de la période d'essai d'1 mois ────────────────────
            // Ne pas supprimer — convertir en fallback permanent sans SMS.
            // Les 50 SMS étaient valables 1 mois uniquement.
            if ($sub->plan?->is_freemium) {
                $sub->update(['sms_quota' => 0, 'sms_used' => 0, 'end_date' => null]);
                Cache::forget("subscription:current:{$sub->client_id}");
                $this->line("  Freemium #{$sub->id} (client #{$sub->client_id}) → période d'essai terminée, converti en fallback permanent (0 SMS)");
                $count++;
                continue;
            }

            // ── Plan payant : expiration normale ─────────────────────────────────
            $sub->expire();
            Cache::forget("subscription:current:{$sub->client_id}");
            $this->line("  Abonnement #{$sub->id} (client #{$sub->client_id}) → expiré");
            $count++;

            // Réactiver le Freemium suspendu si aucun autre plan payant actif ou en attente
            $hasOtherPaidSub = Subscription::where('client_id', $sub->client_id)
                ->where('id', '!=', $sub->id)
                ->whereIn('status', ['active', 'pending'])
                ->whereHas('plan', fn($q) => $q->where('is_freemium', false))
                ->exists();

            if (!$hasOtherPaidSub) {
                $freemium = Subscription::where('client_id', $sub->client_id)
                    ->where('status', 'suspended')
                    ->whereHas('plan', fn($q) => $q->where('is_freemium', true))
                    ->first();

                if ($freemium) {
                    // Réactiver sans SMS — les 50 SMS de bienvenue ne sont jamais renouvelés
                    $freemium->update(['status' => 'active', 'sms_quota' => 0, 'sms_used' => 0, 'end_date' => null]);
                    Cache::forget("subscription:current:{$sub->client_id}");
                    $this->line("  → Freemium #{$freemium->id} (client #{$sub->client_id}) réactivé comme fallback");
                }
            }
        }

        $this->info("✓ {$count} abonnement(s) traité(s).");

        return self::SUCCESS;
    }
}
