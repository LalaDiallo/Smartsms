<?php

namespace App\Models;

use App\Models\AppNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'subscription_plan_id',
        'billing_cycle_id',
        'start_date',
        'end_date',
        'next_billing_date',
        'status',
        'auto_renew',
        'price',
        'currency',
        'sms_quota',
        'sms_used',
        'payment_ref',
        'payment_status',
        'upgrade_mode',
    ];

    protected $casts = [
        'auto_renew' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'next_billing_date' => 'datetime',
    ];

    /* ======================
     | RELATIONS
     ====================== */

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function billingCycle()
    {
        return $this->belongsTo(BillingCycle::class);
    }

    /* ======================
     | SCOPES
     ====================== */

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon(Builder $query, $days = 3)
    {
        return $query->whereNotNull('end_date')
                     ->whereBetween('end_date', [
                         now(),
                         now()->addDays((int) $days)
                     ]);
    }

    /* ======================
     | BUSINESS LOGIC
     ====================== */

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->end_date === null || $this->end_date->isFuture());
    }

    public function hasSmsAvailable(int $amount = 1): bool
    {
        return ($this->sms_used + $amount) <= $this->sms_quota;
    }

    public function consumeSms(int $amount = 1): void
    {
        $this->increment('sms_used', $amount);
        $this->refresh(); // recharger sms_used après increment
        cache()->forget("subscription:current:{$this->client_id}");

        $this->checkSmsQuotaAlert();
    }

    /**
     * Vérifie le quota restant et envoie une notification si seuil atteint.
     * Seuils : 20% (warning) et 10% (error).
     * Limite à 1 notification par seuil par jour pour éviter le spam.
     */
    public function checkSmsQuotaAlert(): void
    {
        if ($this->sms_quota <= 0) return;

        $remaining = max(0, $this->sms_quota - $this->sms_used);
        $pct       = ($remaining / $this->sms_quota) * 100;

        // Définir quel seuil est franchi
        $threshold = null;
        if ($pct <= 10) {
            $threshold = ['type' => 'error',   'pct' => 10,  'title' => 'Quota SMS critique !',   'action' => 'quota_10'];
        } elseif ($pct <= 20) {
            $threshold = ['type' => 'warning',  'pct' => 20,  'title' => 'Quota SMS faible',       'action' => 'quota_20'];
        }

        if (!$threshold) return;

        // Éviter les doublons : 1 notification par seuil par jour
        $alreadySent = AppNotification::where('client_id', $this->client_id)
            ->where('action', $threshold['action'])
            ->where('created_at', '>=', now()->startOfDay())
            ->exists();

        if ($alreadySent) return;

        AppNotification::create([
            'client_id'     => $this->client_id,
            'user_id'       => null, // pour tous les admins du client
            'type'          => $threshold['type'],
            'title'         => $threshold['title'],
            'body'          => "Il vous reste {$remaining} SMS (" . round($pct) . "% de votre quota). Rechargez votre compte pour continuer à envoyer vos campagnes.",
            'action'        => $threshold['action'],
            'resource_type' => 'subscription',
            'resource_id'   => $this->id,
            'link'          => '/subscriptions',
            'read'          => false,
        ]);
    }

    public function resetSmsUsage(): void
    {
        $this->update(['sms_used' => 0]);
    }

    /* ======================
     | RENEWAL
     ====================== */

    public function renew(): void
    {
        if (!$this->auto_renew) {
            return;
        }

        $cycle = $this->billingCycle;

        $start = $this->next_billing_date ?? now();

        $end = match ($cycle->code ?? $cycle->name) {
            'monthly' => Carbon::parse($start)->addMonth(),
            'yearly'  => Carbon::parse($start)->addYear(),
            default   => Carbon::parse($start)->addMonth(),
        };

        $this->update([
            'start_date'        => $start,
            'end_date'          => $end,
            'next_billing_date' => $end,
            'status'            => 'active',
            'sms_used'          => 0,
        ]);
    }

    /* ======================
     | STATUS MANAGEMENT
     ====================== */

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'auto_renew' => false
        ]);

        $this->client?->syncStatus();
    }

    public function expire(): void
    {
        $this->update(['status' => 'expired']);

        $this->client?->syncStatus();
    }

    /**
     * Active un abonnement programmé (queued) dont le paiement est déjà confirmé.
     * Appelé par subscriptions:expire quand le plan actuel arrive à échéance.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);

        // Suspendre le Freemium encore actif — le plan payant prend le relais
        self::where('client_id', $this->client_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'active')
            ->whereHas('plan', fn($q) => $q->where('is_freemium', true))
            ->update(['status' => 'suspended']);

        $this->client?->syncStatus();
    }

    public function subscriptionplan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
