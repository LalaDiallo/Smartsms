<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PaymentGatewayFactory;
use App\Helpers\ActivityLogger;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    // ─── Initiation ────────────────────────────────────────────────────────────

    /**
     * Initier un paiement pour un abonnement en attente.
     * POST /api/payments/initiate
     *
     * Body : {
     *   subscription_id : int     (requis)
     *   payment_method  : string  card|orange|wave|mtn (requis)
     *   phone           : string  numéro du client (requis pour orange/wave/mtn)
     * }
     *
     * Réponse :
     *   - card/orange/wave : payment_url → le frontend redirige l'utilisateur
     *   - mtn              : message demandant de confirmer sur le téléphone
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|integer|exists:subscriptions,id',
            'payment_method'  => 'required|in:card,orange,wave,mtn',
            'phone'           => 'nullable|string|max:20',
        ]);

        /** @var \App\Models\User $user */
        $user         = Auth::user();
        $subscription = Subscription::findOrFail($validated['subscription_id']);

        if ($subscription->client_id !== $user->client_id && !in_array($user->role, ['super_admin', 'admin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if ($subscription->status !== 'pending') {
            return response()->json([
                'message' => 'Cet abonnement n\'est pas en attente de paiement (statut : ' . $subscription->status . ')',
            ], 422);
        }

        if ($subscription->price <= 0) {
            return response()->json(['message' => 'Abonnement gratuit, aucun paiement requis'], 422);
        }

        $transaction = PaymentTransaction::create([
            'reference'      => PaymentTransaction::generateReference(),
            'subscription_id'=> $subscription->id,
            'client_id'      => $subscription->client_id,
            'user_id'        => $user->id,
            'payment_method' => $validated['payment_method'],
            'provider_name'  => $this->resolveProviderName($validated['payment_method']),
            'amount'         => $subscription->price,
            'currency'       => $subscription->currency ?? 'GNF',
            'phone'          => $validated['phone'] ?? null,
        ]);

        $result = $this->paymentService->initiate($transaction, [
            'customer_name'  => $user->name ?? '',
            'customer_email' => $user->email ?? '',
            'customer_phone' => $validated['phone'] ?? '',
        ]);

        if (!$result['success']) {
            return response()->json([
                'message'   => $result['message'],
                'reference' => $transaction->reference,
            ], 422);
        }

        return response()->json([
            'message'        => $result['message'],
            'reference'      => $transaction->reference,
            'payment_url'    => $result['payment_url'] ?? null,
            'payment_method' => $validated['payment_method'],
        ]);
    }

    // ─── Vérification manuelle ─────────────────────────────────────────────────

    /**
     * Vérifier manuellement le statut d'un paiement (polling frontend).
     * POST /api/payments/verify
     *
     * Body : { reference: string }
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|exists:payment_transactions,reference',
        ]);

        /** @var \App\Models\User $user */
        $user        = Auth::user();
        $transaction = PaymentTransaction::where('reference', $validated['reference'])->firstOrFail();

        if ($transaction->client_id !== $user->client_id && !in_array($user->role, ['super_admin', 'admin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if ($transaction->status === 'completed') {
            return response()->json(['message' => 'Paiement déjà confirmé', 'status' => 'completed']);
        }

        if (!$transaction->provider_transaction_id) {
            return response()->json(['message' => 'Paiement non encore initié', 'status' => $transaction->status]);
        }

        $gateway = $this->paymentService->gateway($transaction->payment_method);
        $result  = $gateway->verify($transaction->provider_transaction_id);

        if ($result['success']) {
            $this->paymentService->confirmPayment($transaction);
            return response()->json(['message' => 'Paiement confirmé avec succès', 'status' => 'completed']);
        }

        return response()->json([
            'message' => $result['message'],
            'status'  => $result['status'],
        ]);
    }

    // ─── Historique ────────────────────────────────────────────────────────────

    /**
     * Historique des transactions de paiement du client connecté.
     * GET /api/payments/history
     */
    public function history()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $transactions = PaymentTransaction::where('client_id', $user->client_id)
            ->with('subscription.plan')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($transactions);
    }

    // ─── LengoPay ──────────────────────────────────────────────────────────────

    /**
     * Initialiser un paiement via LengoPay.
     * POST /api/payments/lengopay/initiate
     */
    public function lengoInitiate(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required_without:topup_id|integer|exists:subscriptions,id',
            'topup_id'        => 'required_without:subscription_id|integer|exists:sms_topup_payments,id',
        ]);

        // ── Recharge SMS (topup) ──────────────────────────────────────────────
        if (!empty($validated['topup_id'])) {
            return $this->lengoInitiateTopup($request, $validated['topup_id']);
        }

        /** @var \App\Models\User $user */
        $user         = Auth::user();
        $subscription = Subscription::with('plan')->findOrFail($validated['subscription_id']);

        if ($subscription->client_id !== $user->client_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Abonnement gratuit — activer directement sans passer par LengoPay
        if ($subscription->price <= 0) {
            $subscription->update(['status' => 'active', 'payment_status' => 'paid']);
            cache()->forget("subscription:current:{$user->client_id}");
            ActivityLogger::log('subscription.created', ['plan' => $subscription->plan?->name ?? 'Freemium'], 'subscription', $subscription->id);
            return response()->json(['message' => 'Abonnement activé (gratuit)', 'free' => true]);
        }

        // Abonnement déjà payé
        if ($subscription->payment_status === 'paid') {
            return response()->json(['message' => 'Cet abonnement est déjà payé', 'free' => false], 422);
        }

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $backendUrl  = config('app.url', 'http://localhost:8000');

            Log::info('LengoPay initiate', [
                'subscription_id' => $subscription->id,
                'amount'          => $subscription->price,
                'plan'            => $subscription->plan?->name,
                'callback_url'    => "{$backendUrl}/api/payments/lengopay/callback",
            ]);

            $gateway = PaymentGatewayFactory::make();
            $result  = $gateway->createPayment(
                amount:      (int) $subscription->price,
                currency:    $subscription->currency ?? 'GNF',
                returnUrl:   "{$frontendUrl}/payment/success",
                failureUrl:  "{$frontendUrl}/payment/cancel",
                callbackUrl: "{$backendUrl}/api/payments/lengopay/callback",
            );

            $subscription->update([
                'status'         => 'pending',
                'payment_ref'    => $result['pay_id'],
                'payment_status' => 'pending',
            ]);

            Log::info('LengoPay payment_url générée', [
                'pay_id'      => $result['pay_id'],
                'payment_url' => $result['payment_url'],
            ]);

            return response()->json([
                'message'     => 'Redirection vers LengoPay',
                'payment_url' => $result['payment_url'],
                'pay_id'      => $result['pay_id'],
            ]);

        } catch (\Throwable $e) {
            Log::error('LengoPay initiate error', [
                'error'           => $e->getMessage(),
                'subscription_id' => $subscription->id,
            ]);
            return response()->json(['message' => 'Erreur LengoPay : ' . $e->getMessage()], 500);
        }
    }

    /** Initier le paiement LengoPay pour une recharge SMS */
    private function lengoInitiateTopup(Request $request, int $topupId): \Illuminate\Http\JsonResponse
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $topup = \App\Models\SmsTopupPayment::findOrFail($topupId);

        if ($topup->client_id !== $user->client_id) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if ($topup->status === 'paid') {
            return response()->json(['message' => 'Cette recharge est déjà payée'], 422);
        }

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $backendUrl  = config('app.url', 'http://localhost:8000');

            $gateway = PaymentGatewayFactory::make();
            $result  = $gateway->createPayment(
                amount:      (int) $topup->price,
                currency:    $topup->currency,
                returnUrl:   "{$frontendUrl}/payment/success",
                failureUrl:  "{$frontendUrl}/payment/cancel",
                callbackUrl: "{$backendUrl}/api/payments/lengopay/callback",
            );

            $topup->update(['payment_ref' => $result['pay_id'], 'status' => 'pending']);

            Log::info('LengoPay topup initiated', [
                'topup_id' => $topup->id, 'sms_count' => $topup->sms_count,
                'pay_id'   => $result['pay_id'],
            ]);

            return response()->json([
                'message'     => "Recharge {$topup->sms_count} SMS — Redirection vers LengoPay",
                'payment_url' => $result['payment_url'],
                'pay_id'      => $result['pay_id'],
            ]);

        } catch (\Throwable $e) {
            Log::error('LengoPay topup initiate error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur LengoPay : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Callback POST envoyé par LengoPay après le paiement.
     * POST /api/payments/lengopay/callback  (route publique — pas de auth:sanctum)
     */
    public function lengoCallback(Request $request)
    {
        $payId  = $request->input('pay_id');
        $status = strtoupper($request->input('status', ''));

        Log::info('LengoPay callback reçu', $request->all());

        if (!$payId) {
            return response()->json(['error' => 'pay_id manquant'], 400);
        }

        // ── Vérifier si c'est une recharge SMS (topup) ───────────────────────
        $topup = \App\Models\SmsTopupPayment::where('payment_ref', $payId)->first();
        if ($topup) {
            return $this->handleTopupCallback($topup, $status);
        }

        // ── Sinon, c'est un abonnement ────────────────────────────────────────
        $subscription = Subscription::where('payment_ref', $payId)->with('plan')->first();

        if (!$subscription) {
            Log::warning('LengoPay callback: paiement introuvable', ['pay_id' => $payId]);
            return response()->json(['received' => true]);
        }

        if ($status === 'SUCCESS') {
            $months = $subscription->billingCycle?->months ?? 1;
            $subscription->update([
                'status'         => 'active',
                'payment_status' => 'paid',
                'start_date'     => now(),
                'end_date'       => now()->addMonths((int) $months),
            ]);

            // Expirer le Freemium si encore actif — le plan payant prend sa place
            Subscription::where('client_id', $subscription->client_id)
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->whereHas('plan', fn($q) => $q->where('slug', 'freemium')->orWhere('price_monthly_base', 0))
                ->update(['status' => 'expired']);

            cache()->forget("subscription:current:{$subscription->client_id}");

            ActivityLogger::log(
                'subscription.created',
                ['plan' => $subscription->plan?->name ?? 'Plan'],
                'subscription',
                $subscription->id
            );

            Log::info('LengoPay: abonnement activé', ['id' => $subscription->id]);

        } else {
            $subscription->update(['status' => 'expired', 'payment_status' => 'failed']);
            cache()->forget("subscription:current:{$subscription->client_id}");
            Log::warning('LengoPay: abonnement expiré (paiement échoué)', ['pay_id' => $payId]);
        }

        return response()->json(['received' => true]);
    }

    // ─── Webhooks (routes publiques) ────────────────────────────────────────────

    /** POST /api/payments/webhooks/card — CinetPay */
    public function webhookCard(Request $request)
    {
        return $this->handleWebhook('card', $request->all());
    }

    /** POST /api/payments/webhooks/orange — Orange Money */
    public function webhookOrange(Request $request)
    {
        return $this->handleWebhook('orange', $request->all());
    }

    /** POST /api/payments/webhooks/wave — Wave */
    public function webhookWave(Request $request)
    {
        return $this->handleWebhook('wave', $request->all());
    }

    /** POST /api/payments/webhooks/mtn — MTN MoMo */
    public function webhookMtn(Request $request)
    {
        return $this->handleWebhook('mtn', $request->all());
    }

    // ─── Traitement webhook interne ─────────────────────────────────────────────

    // ─── Simulation (désactivé en production) ──────────────────────────────────

    /**
     * Confirmer manuellement un paiement simulé (mode simulation uniquement).
     * GET /api/payments/simulate/confirm?reference=PAY-XXXX
     */
    public function simulateConfirm(Request $request)
    {
        if (!config('services.payment.simulation_mode', false)) {
            abort(404);
        }

        $transaction = PaymentTransaction::where('reference', $request->query('reference'))->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction introuvable'], 404);
        }

        if ($transaction->status === 'completed') {
            return response()->json(['message' => 'Déjà confirmé', 'status' => 'completed']);
        }

        $this->paymentService->confirmPayment($transaction);

        return response()->json([
            'message'        => '[SIMULATION] Paiement confirmé avec succès',
            'reference'      => $transaction->reference,
            'status'         => 'completed',
            'subscription_id'=> $transaction->subscription_id,
        ]);
    }

    /**
     * Vérification manuelle d'un paiement LengoPay.
     * Utilisé quand le callback n'arrive pas (ex: localhost).
     * POST /api/payments/lengopay/verify
     * Body: { pay_id: string }
     */
    public function lengoVerify(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['pay_id' => 'required|string']);

        $payId = $request->input('pay_id');
        $user  = Auth::user();

        // Chercher un topup en attente avec ce pay_id
        $topup = \App\Models\SmsTopupPayment::where('payment_ref', $payId)
            ->where('client_id', $user->client_id)
            ->where('status', 'pending')
            ->first();

        if ($topup) {
            return $this->handleTopupCallback($topup, 'SUCCESS');
        }

        // Chercher une subscription en attente avec ce pay_id
        $subscription = Subscription::where('payment_ref', $payId)
            ->where('client_id', $user->client_id)
            ->where('status', 'pending')
            ->with('plan')
            ->first();

        if ($subscription) {
            $months = $subscription->billingCycle?->months ?? 1;
            $subscription->update([
                'status'         => 'active',
                'payment_status' => 'paid',
                'start_date'     => now(),
                'end_date'       => now()->addMonths((int) $months),
            ]);

            // Expirer le Freemium si encore actif
            Subscription::where('client_id', $user->client_id)
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->whereHas('plan', fn($q) => $q->where('slug', 'freemium')->orWhere('price_monthly_base', 0))
                ->update(['status' => 'expired']);

            cache()->forget("subscription:current:{$user->client_id}");
            ActivityLogger::log('subscription.created', ['plan' => $subscription->plan?->name ?? 'Plan'], 'subscription', $subscription->id);

            return response()->json([
                'activated' => true,
                'type'      => 'subscription',
                'plan_name' => $subscription->plan?->name,
                'sms_quota' => $subscription->sms_quota,
            ]);
        }

        // Peut-être déjà activé
        $activeSub = Subscription::where('client_id', $user->client_id)
            ->where('status', 'active')
            ->with('plan')
            ->latest()
            ->first();

        if ($activeSub) {
            return response()->json([
                'activated' => true,
                'type'      => 'already_active',
                'plan_name' => $activeSub->plan?->name,
                'sms_quota' => $activeSub->sms_quota,
            ]);
        }

        return response()->json(['activated' => false, 'message' => 'Paiement introuvable'], 404);
    }

    /** Confirme une recharge SMS après callback LengoPay SUCCESS */
    private function handleTopupCallback(\App\Models\SmsTopupPayment $topup, string $status): \Illuminate\Http\JsonResponse
    {
        if ($status === 'SUCCESS') {
            $topup->update(['status' => 'paid']);

            // Ajouter les SMS au quota de l'abonnement lié
            if ($topup->subscription_id) {
                $sub = Subscription::find($topup->subscription_id);
                if ($sub) {
                    $sub->increment('sms_quota', $topup->sms_count);
                    cache()->forget("subscription:current:{$topup->client_id}");
                }
            }

            ActivityLogger::log('subscription.topup', [
                'count' => $topup->sms_count,
                'price' => $topup->price,
            ], 'subscription', $topup->subscription_id);

            Log::info('LengoPay: recharge SMS confirmée', [
                'topup_id' => $topup->id,
                'sms'      => $topup->sms_count,
            ]);

        } else {
            $topup->update(['status' => 'failed']);
            Log::warning('LengoPay: recharge SMS échouée', ['topup_id' => $topup->id]);
        }

        return response()->json(['received' => true]);
    }

    private function handleWebhook(string $method, array $payload): \Illuminate\Http\JsonResponse
    {
        Log::info("Webhook {$method} reçu", ['payload' => $payload]);

        try {
            $gateway = $this->paymentService->gateway($method);
            $result  = $gateway->handleWebhook($payload);

            if (!$result['success'] || empty($result['provider_transaction_id'])) {
                Log::info("Webhook {$method} ignoré", ['result' => $result]);
                return response()->json(['status' => 'ignored'], 200);
            }

            // Chercher la transaction par provider_transaction_id OU reference (selon le provider)
            $transaction = PaymentTransaction::where('provider_transaction_id', $result['provider_transaction_id'])
                ->orWhere('reference', $result['provider_transaction_id'])
                ->first();

            if (!$transaction) {
                Log::warning("Transaction introuvable — webhook {$method}", [
                    'provider_transaction_id' => $result['provider_transaction_id'],
                ]);
                return response()->json(['status' => 'not_found'], 200);
            }

            match ($result['status']) {
                'completed' => $this->paymentService->confirmPayment($transaction),
                'failed'    => $this->paymentService->failPayment($transaction, $result['message'] ?? ''),
                default     => null,
            };

            return response()->json(['status' => 'ok'], 200);

        } catch (\Throwable $e) {
            // On retourne toujours 200 pour éviter que le provider retry en boucle
            Log::error("Erreur traitement webhook {$method} : " . $e->getMessage(), [
                'payload' => $payload,
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error'], 200);
        }
    }

    private function resolveProviderName(string $method): string
    {
        return match ($method) {
            'card'   => 'cinetpay',
            'orange' => 'orange_money',
            'wave'   => 'wave',
            'mtn'    => 'mtn_momo',
        };
    }
}
