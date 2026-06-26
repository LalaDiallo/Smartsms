<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\BillingCycle;

class SubscriptionController extends Controller
{
    // ─── Helpers privés ────────────────────────────────────────────────────────

    /**
     * Résolution du BillingCycle depuis le paramètre period du frontend.
     * null / absent → mensuel (1 mois)
     * 'quarterly'   → 3 mois
     * 'semiannual'  → 6 mois
     * 'annual'      → 12 mois
     */
    private function resolveBillingCycle(?string $period): BillingCycle
    {
        $months = match ($period) {
            'quarterly'  => 3,
            'semiannual' => 6,
            'annual'     => 12,
            default      => 1,
        };

        return BillingCycle::where('months', $months)->firstOrFail();
    }

    /**
     * Résolution du SubscriptionPlan depuis un ID numérique ou un slug string.
     */
    private function resolvePlan(string $planId): SubscriptionPlan
    {
        return is_numeric($planId)
            ? SubscriptionPlan::findOrFail((int) $planId)
            : SubscriptionPlan::where('slug', $planId)->firstOrFail();
    }

    /**
     * Prix par SMS pour un achat top-up selon la quantité.
     */
    private function topupPricePerSms(int $qty): int
    {
        if ($qty >= 100001) return 100;
        if ($qty >= 100000) return 105;
        if ($qty >= 50000)  return 110;
        if ($qty >= 10000)  return 115;
        return 120;
    }

    /**
     * Format de réponse unifié pour un abonnement.
     */
    private function formatSubscription(Subscription $sub): array
    {
        return [
            'id'                   => $sub->id,
            'client_id'            => $sub->client_id,
            'subscription_plan_id' => $sub->subscription_plan_id,
            'billing_cycle_id'     => $sub->billing_cycle_id,
            'status'               => $sub->status,
            'start_date'           => $sub->start_date,
            'end_date'             => $sub->end_date,
            'next_billing_date'    => $sub->next_billing_date,
            'auto_renew'           => (bool) $sub->auto_renew,
            'price'                => $sub->price,
            'currency'             => $sub->currency,
            'sms_quota'            => $sub->sms_quota,
            'sms_used'             => $sub->sms_used,
            'plan_name'            => $sub->plan?->name,
            'plan_slug'            => $sub->plan?->slug,
        ];
    }

    // ─── Mise à jour tarif dégressif (super_admin) ────────────────────────────

    /**
     * Modification du prix d'un palier SMS supplémentaire.
     * PUT /subscriptions/plans/{planId}/extra-sms/{tierId}
     */
    public function updateExtraTier(Request $request, int $planId, int $tierId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role !== 'super_admin') {
            return response()->json(['message' => 'Accès réservé au super administrateur'], 403);
        }

        $tier = \App\Models\ExtraSmsPricing::where('subscription_plan_id', $planId)
            ->findOrFail($tierId);

        $validated = $request->validate([
            'price_per_sms' => 'required|integer|min:1',
        ]);

        $tier->update(['price_per_sms' => $validated['price_per_sms']]);

        return response()->json([
            'message' => 'Tarif dégressif mis à jour',
            'tier'    => $tier->fresh(),
        ]);
    }

    // ─── Mise à jour cycle de facturation (super_admin) ───────────────────────

    /**
     * Modification d'un cycle de facturation (remise, bonus SMS, options premium).
     * PUT /subscriptions/billing-cycles/{id}
     */
    public function updateBillingCycle(Request $request, int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role !== 'super_admin') {
            return response()->json(['message' => 'Accès réservé au super administrateur'], 403);
        }

        $cycle = BillingCycle::findOrFail($id);

        $validated = $request->validate([
            'discount_percent'  => 'nullable|numeric|min:0|max:100',
            'sms_bonus_percent' => 'nullable|numeric|min:0|max:100',
            'priority_support'  => 'nullable|boolean',
            'advanced_reports'  => 'nullable|boolean',
            'premium_features'  => 'nullable|boolean',
        ]);

        $data = array_filter($validated, fn ($v) => $v !== null);
        $cycle->update($data);

        return response()->json([
            'message' => "Cycle \"{$cycle->name}\" mis à jour",
            'cycle'   => $cycle->fresh(),
        ]);
    }

    // ─── Mise à jour d'un plan (super_admin uniquement) ───────────────────────

    /**
     * Modification des tarifs d'un plan d'abonnement.
     * PUT /subscriptions/plans/{id}
     */
    public function updatePlan(Request $request, int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'super_admin') {
            return response()->json(['message' => 'Accès réservé au super administrateur'], 403);
        }

        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'price_monthly_base'   => 'nullable|integer|min:0',
            'sms_included_monthly' => 'nullable|integer|min:0',
            'sms_price_reference'  => 'nullable|integer|min:0',
            'rollover_months'      => 'nullable|integer|min:0',
            'features'             => 'nullable|array',
            'features.*'           => 'string|max:255',
        ]);

        // On ne met à jour que les champs effectivement envoyés
        $data = array_filter($validated, fn ($v) => $v !== null);

        $plan->update($data);

        return response()->json([
            'message' => "Plan \"{$plan->name}\" mis à jour avec succès",
            'plan'    => [
                'id'                  => (string) $plan->id,
                'slug'                => $plan->slug,
                'name'                => $plan->name,
                'price_monthly'       => $plan->price_monthly_base,
                'sms_included'        => $plan->sms_included_monthly,
                'sms_price_included'  => $plan->sms_price_reference,
                'sms_price_reference' => $plan->sms_price_reference,
                'rollover_months'     => $plan->rollover_months,
                'is_freemium'         => $plan->is_freemium,
                'popular'             => $plan->slug === 'pro',
                'features'            => is_array($plan->features) ? $plan->features : [],
            ],
        ]);
    }

    // ─── Plans & Tarifs ────────────────────────────────────────────────────────

    /**
     * Liste des plans actifs avec les cycles de facturation.
     * GET /subscriptions/plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::with(['extraSmsPricing'])
            ->where('status', 'active')
            ->orderBy('price_monthly_base')
            ->get()
            ->map(fn ($plan) => [
                'id'                  => (string) $plan->id,
                'slug'                => $plan->slug,
                'name'                => $plan->name,
                'price_monthly'       => $plan->price_monthly_base,
                'sms_included'        => $plan->sms_included_monthly,
                'sms_price_included'  => $plan->sms_price_reference,  // alias utilisé par le front
                'sms_price_reference' => $plan->sms_price_reference,
                'rollover_months'     => $plan->rollover_months,
                'is_freemium'         => $plan->is_freemium,
                'popular'             => $plan->slug === 'pro',        // plan le plus populaire
                'features'            => is_array($plan->features) ? $plan->features : [],
                'extra_sms_pricing'   => $plan->extraSmsPricing,
            ]);

        $billingCycles = BillingCycle::orderBy('months')->get();

        return response()->json([
            'plans'          => $plans,
            'billing_cycles' => $billingCycles,
        ]);
    }

    /**
     * Tarifs long terme (cycles > 1 mois) avec remises et bonus SMS.
     * GET /subscriptions/long-term
     * Réponse : { plans: { "slug": { months: {…} } } }  ← format attendu par le front
     */
    public function longTerm()
    {
        $plans  = SubscriptionPlan::where('status', 'active')->get();
        $cycles = BillingCycle::where('months', '>', 1)->orderBy('months')->get();

        $data = [];

        foreach ($plans as $plan) {
            $longTerm = [];

            foreach ($cycles as $cycle) {
                $price    = $plan->basePriceForCycle($cycle);
                $bonusSms = (int) round(
                    $plan->sms_included_monthly * $cycle->months * ($cycle->sms_bonus_percent / 100)
                );

                $longTerm[$cycle->months] = [
                    'label'        => $cycle->name,
                    'months'       => $cycle->months,
                    'price'        => $price,
                    'discount_pct' => $cycle->discount_percent,
                    'bonus_sms'    => $bonusSms,
                    'bonus_pct'    => $cycle->sms_bonus_percent,
                ];
            }

            // Clé = slug du plan (format attendu par le frontend)
            $data[$plan->slug] = $longTerm;
        }

        return response()->json(['plans' => $data]);
    }

    /**
     * Calcul du prix et du quota SMS pour un plan + cycle.
     * POST /subscriptions/pricing
     */
    public function pricing(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|string',
            'period'  => 'nullable|in:quarterly,semiannual,annual',
        ]);

        $plan  = $this->resolvePlan($request->plan_id);
        $cycle = $this->resolveBillingCycle($request->period);

        return response()->json([
            'price'     => $plan->basePriceForCycle($cycle),
            'sms_quota' => $plan->smsIncludedForCycle($cycle),
            'currency'  => 'GNF',
            'cycle'     => $cycle->name,
        ]);
    }

    // ─── Abonnement actuel ─────────────────────────────────────────────────────

    /**
     * Abonnement actif de l'utilisateur connecté.
     * GET /subscriptions/current
     */
    public function current()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->client_id) {
            return response()->json(['subscription' => null]);
        }

        $cacheKey = "subscription:current:{$user->client_id}";

        $cached = cache()->get($cacheKey);
        if ($cached) {
            return response()->json(['subscription' => $cached]);
        }

        // Priorité : active > plan payant suspendu > pending > Freemium suspendu (fallback permanent)
        $subscription =
            Subscription::where('client_id', $user->client_id)
                ->where('status', 'active')
                ->with('plan')->latest()->first()
            ?? Subscription::where('client_id', $user->client_id)
                ->where('status', 'suspended')
                ->whereHas('plan', fn($q) => $q->where('is_freemium', false))
                ->with('plan')->latest()->first()
            ?? Subscription::where('client_id', $user->client_id)
                ->where('status', 'pending')
                ->with('plan')->latest()->first()
            ?? Subscription::where('client_id', $user->client_id)
                ->where('status', 'suspended')
                ->whereHas('plan', fn($q) => $q->where('is_freemium', true))
                ->with('plan')->latest()->first();

        if (!$subscription) {
            return response()->json(['subscription' => null]);
        }

        $formatted = $this->formatSubscription($subscription);

        // Cache 1 minute — les quotas SMS changent fréquemment
        cache()->put($cacheKey, $formatted, now()->addMinute());

        return response()->json(['subscription' => $formatted]);
    }

    /**
     * Abonnement actif d'un client spécifique (usage admin).
     * GET /subscriptions/active/{clientId}
     */
    public function active(int $clientId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!in_array($user->role, ['super_admin', 'admin']) && $user->client_id !== $clientId) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $subscription = Subscription::where('client_id', $clientId)
            ->where('status', 'active')
            ->with(['plan', 'billingCycle'])
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Aucun abonnement actif'], 404);
        }

        return response()->json($subscription);
    }

    // ─── Souscription & Achat ──────────────────────────────────────────────────

    /**
     * Calcule le montant pro-rata pour un upgrade immédiat (lecture seule, aucune modification).
     * POST /subscriptions/upgrade-preview
     */
    public function upgradePreview(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|string',
            'period'  => 'nullable|in:quarterly,semiannual,annual',
        ]);

        /** @var \App\Models\User $user */
        $user   = Auth::user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $plan  = $this->resolvePlan($request->plan_id);
        $cycle = $this->resolveBillingCycle($request->period);

        $newPrice    = $plan->basePriceForCycle($cycle);
        $newSmsQuota = $plan->smsIncludedForCycle($cycle);

        $currentActive = Subscription::where('client_id', $client->id)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->whereHas('plan', fn($q) => $q->where('is_freemium', false))
            ->with('plan')
            ->orderByDesc('end_date')
            ->first();

        if (!$currentActive) {
            return response()->json(['upgrade_needed' => false]);
        }

        $remainingDays = max(0, (int) now()->diffInDays($currentActive->end_date, false));

        return response()->json([
            'upgrade_needed'    => true,
            'current_plan_name' => $currentActive->plan?->name,
            'current_end_date'  => $currentActive->end_date->toDateString(),
            'days_remaining'    => $remainingDays,
            'new_plan_name'     => $plan->name,
            'new_plan_price'    => $newPrice,
            'new_sms_quota'     => $newSmsQuota,
            'currency'          => 'GNF',
        ]);
    }

    /**
     * Souscription à un plan ou achat de crédits SMS (top-up).
     * POST /subscriptions/subscribe
     *
     * Payload attendu :
     *   plan_id        string   slug ou ID du plan (ou pack top-up pour is_topup)
     *   payment_method string   card|orange|wave|mtn
     *   period         ?string  quarterly|semiannual|annual  (null → mensuel)
     *   is_long_term   ?bool    true si engagement long terme
     *   is_topup       ?bool    true pour achat de crédits SMS
     *   sms_count      ?int     quantité SMS pour top-up
     *   upgrade_mode   ?string  immediate|queued  (si plan payant actif)
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id'        => 'required|string',
            'payment_method' => 'nullable|in:card,orange,wave,mtn', // optionnel — géré par LengoPay
            'period'         => 'nullable|in:quarterly,semiannual,annual',
            'is_long_term'   => 'nullable|boolean',
            'is_topup'       => 'nullable|boolean',
            'sms_count'      => 'nullable|integer|min:1',
            'upgrade_mode'   => 'nullable|in:immediate,queued',
        ]);

        /** @var \App\Models\User $user */
        $user   = Auth::user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        // ── Achat top-up de crédits SMS ──────────────────────────────────────
        if ($request->boolean('is_topup')) {
            return $this->handleTopup($request, $client);
        }

        // ── Souscription à un plan ───────────────────────────────────────────
        return DB::transaction(function () use ($request, $client) {
            $plan  = $this->resolvePlan($request->plan_id);
            $cycle = $this->resolveBillingCycle($request->period);

            $price    = $plan->basePriceForCycle($cycle);
            $smsQuota = $plan->smsIncludedForCycle($cycle);

            // ── Abonnement actif avec du temps restant ? ─────────────────────
            $currentActive = Subscription::where('client_id', $client->id)
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->where('end_date', '>', now())
                ->orderByDesc('end_date')
                ->first();

            // Statut initial : pending si payant, active si gratuit (freemium)
            $isPaid      = $price > 0;
            $upgradeMode = null;

            if ($currentActive) {
                // Si l'abonnement actif est Freemium et qu'on souscrit à un plan payant,
                // on n'enfile pas — on remplace simplement (Freemium laisse place au payant)
                $isCurrentFreemium = $currentActive->plan?->slug === 'freemium'
                    || $currentActive->plan?->price_monthly_base == 0;

                if ($isPaid && $isCurrentFreemium) {
                    // Ne pas expirer le Freemium maintenant — il sera expiré par lengoCallback()
                    // après confirmation réelle du paiement. Le client garde l'accès pendant l'attente.
                    // Annuler tout éventuel paiement pending précédent pour éviter les doublons.
                    Subscription::where('client_id', $client->id)
                        ->where('status', 'pending')
                        ->where('payment_status', 'pending')
                        ->update(['status' => 'expired', 'payment_status' => 'cancelled']);

                    $start  = now();
                    $end    = now()->addMonths((int) $cycle->months);
                    $status = 'pending';
                    $queued = false;
                } else {
                    // Réabonnement avant la fin d'un plan payant
                    $upgradeMode = $request->input('upgrade_mode', 'queued');

                    if ($upgradeMode === 'immediate') {
                        // Upgrade immédiat : l'ancien plan est annulé (sans crédit) et le
                        // nouveau est facturé plein tarif puis activé tout de suite.
                        // Annuler les anciens pending pour éviter les doublons
                        Subscription::where('client_id', $client->id)
                            ->where('status', 'pending')
                            ->where('payment_status', 'pending')
                            ->update(['status' => 'expired', 'payment_status' => 'cancelled']);

                        $start  = now();
                        $end    = now()->addMonths((int) $cycle->months);
                        $status = $isPaid ? 'pending' : 'active';
                        $queued = false;
                    } else {
                        // Queued (par défaut) : démarre à la fin de l'abonnement actuel
                        $start  = $currentActive->end_date;
                        $end    = $currentActive->end_date->copy()->addMonths((int) $cycle->months);
                        $status = $isPaid ? 'pending' : 'active';
                        $queued = true;
                    }
                }
            } else {
                // Pas d'abonnement actif → expirer les pending/suspended (hors Freemium) et démarrer
                Subscription::where('client_id', $client->id)
                    ->whereIn('status', ['pending', 'suspended'])
                    ->whereHas('plan', fn($q) => $q->where('is_freemium', false))
                    ->update(['status' => 'expired']);

                $start  = now();
                $end    = now()->addMonths((int) $cycle->months);
                $status = $isPaid ? 'pending' : 'active';
                $queued = false;
            }

            $subscription = Subscription::create([
                'client_id'            => $client->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle_id'     => $cycle->id,
                'start_date'           => $start,
                'end_date'             => $end,
                'next_billing_date'    => $end,
                'status'               => $status,
                'auto_renew'           => true,
                'price'                => $price,
                'currency'             => 'GNF',
                'sms_quota'            => $smsQuota,
                'sms_used'             => 0,
                'upgrade_mode'         => $upgradeMode === 'immediate' ? 'immediate' : null,
            ]);

            // Synchroniser plan_id sur le client (rétrocompat)
            $client->update(['plan_id' => $plan->id]);

            $message = $queued
                ? 'Souscription programmée — votre nouveau plan démarrera le '
                  . $start->format('d/m/Y') . ' à la fin de votre abonnement actuel'
                : 'Souscription créée avec succès';

            // Pour les plans gratuits → activer et loguer immédiatement
            // Pour les plans payants → en attente de confirmation LengoPay (callback)
            $requiresPayment = $price > 0;

            if (!$requiresPayment) {
                cache()->forget("subscription:current:{$client->id}");
                ActivityLogger::log('subscription.created', ['plan' => $plan->name], 'subscription', $subscription->id);

                // Upgrade immédiat vers un plan gratuit (sans paiement) → écraser l'ancien plan tout de suite
                if ($upgradeMode === 'immediate' && $status === 'active' && $currentActive) {
                    $currentActive->update(['status' => 'cancelled']);
                }

                $client->syncStatus();
            }

            return response()->json([
                'message'      => $message,
                'queued'       => $queued,
                'starts_at'    => $start->toDateString(),
                'requires_payment' => $requiresPayment,
                'subscription' => array_merge(
                    $this->formatSubscription($subscription->load('plan')),
                    ['id' => $subscription->id]
                ),
            ], 201);
        });
    }

    /**
     * Recharge de crédits SMS — crée un enregistrement pending et attend le paiement LengoPay.
     */
    private function handleTopup(Request $request, $client)
    {
        $smsCount = (int) ($request->sms_count ?? 0);

        if ($smsCount < 1) {
            return response()->json(['message' => 'Quantité de SMS invalide'], 422);
        }

        // Trouver l'abonnement existant (actif de préférence, sinon le plus récent)
        $subscription = Subscription::where('client_id', $client->id)
            ->where('status', 'active')
            ->latest()
            ->first()
            ?? Subscription::where('client_id', $client->id)->latest()->first();

        // Aucun abonnement — créer un compte Freemium de base
        if (!$subscription) {
            $freemiumPlan = SubscriptionPlan::where('slug', 'freemium')->first();
            $monthlyCycle = BillingCycle::where('months', 1)->first();
            if ($freemiumPlan && $monthlyCycle) {
                $subscription = Subscription::create([
                    'client_id'            => $client->id,
                    'subscription_plan_id' => $freemiumPlan->id,
                    'billing_cycle_id'     => $monthlyCycle->id,
                    'start_date'           => now(),
                    'end_date'             => null,
                    'status'               => 'active',
                    'auto_renew'           => false,
                    'price'                => 0,
                    'currency'             => 'GNF',
                    'sms_quota'            => 0,
                    'sms_used'             => 0,
                ]);
            }
        }

        if (!$subscription) {
            return response()->json(['message' => 'Impossible d\'initialiser le compte de crédits'], 500);
        }

        $pricePerSms = $this->topupPricePerSms($smsCount);
        $totalPrice  = $smsCount * $pricePerSms;

        // Créer un enregistrement de recharge en attente de paiement
        $topup = \App\Models\SmsTopupPayment::create([
            'client_id'       => $client->id,
            'subscription_id' => $subscription->id,
            'sms_count'       => $smsCount,
            'price'           => $totalPrice,
            'currency'        => 'GNF',
            'status'          => 'pending',
        ]);

        return response()->json([
            'message'         => "Recharge de {$smsCount} SMS en attente de paiement",
            'topup_id'        => $topup->id,
            'sms_count'       => $smsCount,
            'price'           => $totalPrice,
            'currency'        => 'GNF',
            'requires_payment'=> true,
            'is_topup'        => true,
            'subscription'    => array_merge(
                $this->formatSubscription($subscription->fresh()->load('plan')),
                ['id' => $subscription->id]
            ),
        ]);
    }

    // ─── Mise à jour ───────────────────────────────────────────────────────────

    /**
     * Mise à jour de l'abonnement (renouvellement automatique, etc.).
     * PUT /subscriptions/{id}
     */
    public function update(Request $request, int $id)
    {
        $subscription = Subscription::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin', 'admin']) && $subscription->client_id !== $user->client_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'auto_renew' => 'nullable|boolean',
        ]);

        if (array_key_exists('auto_renew', $validated)) {
            $subscription->update(['auto_renew' => $validated['auto_renew']]);
        }

        cache()->forget("subscription:current:{$subscription->client_id}");

        return response()->json([
            'message'      => 'Abonnement mis à jour',
            'subscription' => $this->formatSubscription($subscription->fresh()->load('plan')),
        ]);
    }

    // ─── Annulation ────────────────────────────────────────────────────────────

    /**
     * Annulation d'un abonnement.
     * POST /subscriptions/{id}/cancel  ou  DELETE /subscriptions/{id}
     */
    public function cancel(int $id)
    {
        $subscription = Subscription::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin', 'admin']) && $subscription->client_id !== $user->client_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if (in_array($subscription->status, ['cancelled', 'expired'])) {
            return response()->json(['message' => 'Cet abonnement est déjà annulé ou expiré'], 422);
        }

        $planName = $subscription->plan?->name ?? 'abonnement';
        $subscription->cancel();
        cache()->forget("subscription:current:{$subscription->client_id}");

        ActivityLogger::log('subscription.cancelled', ['plan' => $planName], 'subscription', $subscription->id);

        return response()->json(['message' => 'Abonnement annulé avec succès']);
    }

    // ─── Historique ────────────────────────────────────────────────────────────

    /**
     * Historique complet des paiements d'un client (abonnements + recharges).
     * GET /subscriptions/invoices/client/{clientId}
     */
    public function clientInvoices(int $clientId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin', 'admin']) && $user->client_id !== $clientId) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $subscriptions = Subscription::where('client_id', $clientId)
            ->with(['plan', 'billingCycle'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($sub) => [
                'id'            => $sub->id,
                'reference'     => 'SMS-' . str_pad($sub->id, 6, '0', STR_PAD_LEFT),
                'created_at'    => $sub->created_at,
                'plan_name'     => $sub->plan?->name ?? 'Recharge SMS',
                'plan_slug'     => $sub->plan?->slug ?? null,
                'amount'        => (float) $sub->price,
                'currency'      => $sub->currency ?? 'GNF',
                'status'        => $sub->status,
                'payment_status'=> $this->mapPaymentStatus($sub->status),
                'period'        => $sub->billingCycle?->name ?? 'Mensuel',
                'billing_months'=> $sub->billingCycle?->months ?? 1,
                'sms_quota'     => $sub->sms_quota,
                'sms_used'      => $sub->sms_used,
                'start_date'    => $sub->start_date,
                'end_date'      => $sub->end_date,
            ]);

        return response()->json([
            'invoices' => $subscriptions,
            'summary'  => [
                'total_paid'   => $subscriptions->where('status', '!=', 'cancelled')->sum('amount'),
                'total_count'  => $subscriptions->count(),
                'total_sms'    => $subscriptions->sum('sms_quota'),
            ],
        ]);
    }

    /**
     * Correspondance statut abonnement → statut paiement.
     */
    private function mapPaymentStatus(string $status): string
    {
        return match ($status) {
            'active'    => 'paid',
            'expired'   => 'paid',     // abonnement payé mais expiré
            'pending'   => 'pending',
            'suspended' => 'pending',
            'cancelled' => 'cancelled',
            default     => 'pending',
        };
    }
}
