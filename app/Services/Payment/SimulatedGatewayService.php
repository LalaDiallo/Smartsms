<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

/**
 * Gateway de simulation — utilisé quand PAYMENT_SIMULATION_MODE=true.
 *
 * Permet de tester tout le flux de paiement sans credentials réels :
 *   1. initiate()  → retourne une URL de simulation locale
 *   2. verify()    → retourne "completed" pour simuler un paiement réussi
 *   3. handleWebhook() → non utilisé en mode simulation
 */
class SimulatedGatewayService implements PaymentGatewayInterface
{
    public function isConfigured(): bool
    {
        return true;
    }

    public function initiate(array $data): array
    {
        $confirmUrl = rtrim(config('app.url'), '/') . '/api/payments/simulate/confirm?reference=' . urlencode($data['reference']);

        return [
            'success'                => true,
            'provider_transaction_id'=> 'SIM-' . strtoupper(uniqid()),
            'payment_url'            => $confirmUrl,
            'message'                => '[SIMULATION] Paiement simulé — cliquez sur payment_url pour confirmer',
            'raw'                    => ['mode' => 'simulation', 'reference' => $data['reference']],
        ];
    }

    public function verify(string $providerTransactionId): array
    {
        return [
            'success' => true,
            'status'  => 'completed',
            'message' => '[SIMULATION] Paiement validé automatiquement',
            'raw'     => ['mode' => 'simulation'],
        ];
    }

    public function handleWebhook(array $payload): array
    {
        return [
            'success'                => true,
            'status'                 => 'completed',
            'provider_transaction_id'=> $payload['provider_transaction_id'] ?? null,
            'message'                => '[SIMULATION] Webhook simulé',
        ];
    }
}
