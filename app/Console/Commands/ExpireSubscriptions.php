<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature   = 'subscriptions:expire';
    protected $description = 'Passe les abonnements dont la date de fin est dépassée en "expired"';

    public function handle(): int
    {
        $expired = Subscription::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->get();

        foreach ($expired as $sub) {
            $sub->expire();
            $this->line("  Abonnement #{$sub->id} (client #{$sub->client_id}) → expiré");
        }

        $count = $expired->count();
        $this->info("✓ {$count} abonnement(s) expiré(s).");

        return self::SUCCESS;
    }
}
