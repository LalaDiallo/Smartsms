<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;

/**
 * Service central de paiement — factory + orchestration.
 *
 * Utilisation :
 *   $paymentService = app(PaymentService::class);
 *   $gateway = $paymentService->gateway('card');  // CinetPayService
 *   $result  = $paymentService->initiate($transaction, ['customer_name' => '...']);
 */
class PaymentService
{
    /**
     * Retourner le gateway correspondant à la méthode de paiement.
     * En mode simulation (PAYMENT_SIMULATION_MODE=true), retourne le gateway simulé.
     */
    public function gateway(string $method): PaymentGatewayInterface
    {
        if (config('services.payment.simulation_mode', false)) {
            return new SimulatedGatewayService();
        }

        return match ($method) {
            'card'   => new CinetPayService(),
            'orange' => new OrangeMoneyService(),
            'wave'   => new WaveService(),
            'mtn'    => new MtnMomoService(),
            default  => throw new \InvalidArgumentException("Méthode de paiement inconnue : {$method}"),
        };
    }

    /**
     * Initier le paiement pour une transaction et mettre à jour son statut.
     */
    public function initiate(PaymentTransaction $transaction, array $extraData = []): array
    {
        $gateway = $this->gateway($transaction->payment_method);

        if (!$gateway->isConfigured()) {
            return [
                'success' => false,
                'message' => "Le mode de paiement \"{$transaction->payment_method}\" n'est pas encore configuré. Ajoutez les credentials dans le .env.",
            ];
        }

        $data = array_merge([
            'reference'   => (string) $transaction->reference,
            'amount'      => (float) $transaction->amount,
            'currency'    => $transaction->currency,
            'description' => "Abonnement SmartSMS #{$transaction->subscription_id}",
            'notify_url'  => url("/api/payments/webhooks/{$transaction->payment_method}"),
            'return_url'  => rtrim(config('app.frontend_url'), '/') . '/payment/success',
            'cancel_url'  => rtrim(config('app.frontend_url'), '/') . '/payment/cancel',
            'phone'       => $transaction->phone ?? '',
        ], $extraData);

        $result = $gateway->initiate($data);

        $transaction->update([
            'status'                 => $result['success'] ? 'processing' : 'failed',
            'provider_transaction_id'=> $result['provider_transaction_id'] ?? null,
            'payment_url'            => $result['payment_url'] ?? null,
            'provider_response'      => $result['raw'] ?? null,
            'failure_reason'         => $result['success'] ? null : ($result['message'] ?? null),
        ]);

        return $result;
    }

    /**
     * Confirmer un paiement réussi et activer l'abonnement lié.
     */
    public function confirmPayment(PaymentTransaction $transaction): void
    {
        if ($transaction->status === 'completed') {
            return;
        }

        $transaction->update([
            'status'  => 'completed',
            'paid_at' => now(),
        ]);

        if ($transaction->subscription_id) {
            Subscription::find($transaction->subscription_id)?->update(['status' => 'active']);
        }

        Log::info('Paiement confirmé', [
            'reference'      => $transaction->reference,
            'method'         => $transaction->payment_method,
            'subscription_id'=> $transaction->subscription_id,
        ]);
    }

    /**
     * Marquer un paiement comme échoué.
     */
    public function failPayment(PaymentTransaction $transaction, string $reason = ''): void
    {
        $transaction->update([
            'status'         => 'failed',
            'failure_reason' => $reason,
        ]);

        Log::warning('Paiement échoué', [
            'reference' => $transaction->reference,
            'method'    => $transaction->payment_method,
            'reason'    => $reason,
        ]);
    }
}
