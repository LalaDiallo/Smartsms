<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

/**
 * Intégration Wave (paiement mobile Afrique de l'Ouest).
 *
 * Documentation : https://docs.wave.com/business/api
 * Wave est disponible en Guinée, Sénégal, Côte d'Ivoire, Mali, Burkina Faso.
 *
 * Flux :
 *   1. Créer une checkout session → obtenir wave_launch_url
 *   2. Rediriger le client vers l'URL Wave
 *   3. Wave envoie un webhook quand le paiement est confirmé
 *
 * Credentials à configurer dans .env :
 *   WAVE_API_KEY
 */
class WaveService implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.wave.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.wave.api_key', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function initiate(array $data): array
    {
        $response = Http::timeout(30)
            ->withToken($this->apiKey)
            ->post("{$this->baseUrl}/checkout/sessions", [
                'currency'         => $data['currency'] ?? 'GNF',
                'amount'           => (string) (int) $data['amount'],
                'success_url'      => $data['return_url'],
                'error_url'        => $data['cancel_url'] ?? $data['return_url'],
                'client_reference' => $data['reference'],
            ]);

        $body = $response->json() ?? [];

        if ($response->successful() && !empty($body['wave_launch_url'])) {
            return [
                'success'                => true,
                'provider_transaction_id'=> $body['id'],
                'payment_url'            => $body['wave_launch_url'],
                'message'                => 'Session Wave créée avec succès',
                'raw'                    => $body,
            ];
        }

        return [
            'success' => false,
            'message' => $body['message'] ?? $body['error'] ?? 'Erreur Wave API',
            'raw'     => $body,
        ];
    }

    public function verify(string $providerTransactionId): array
    {
        $response = Http::timeout(15)
            ->withToken($this->apiKey)
            ->get("{$this->baseUrl}/checkout/sessions/{$providerTransactionId}");

        $body = $response->json() ?? [];

        $waveStatus = $body['checkout_status'] ?? '';
        $status = match ($waveStatus) {
            'complete'             => 'completed',
            'expired', 'errored'  => 'failed',
            default               => 'pending',
        };

        return [
            'success' => $status === 'completed',
            'status'  => $status,
            'message' => "Session Wave : {$waveStatus}",
            'raw'     => $body,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        // Wave webhook : { "type": "checkout.session.completed", "data": { "id": "cos_xxx", ... } }
        $sessionId = $payload['data']['id'] ?? null;

        if (!$sessionId) {
            return [
                'success' => false,
                'status'  => 'failed',
                'message' => 'Session ID manquant dans le webhook Wave',
                'provider_transaction_id' => null,
            ];
        }

        $verification = $this->verify($sessionId);

        return [
            'success'                => $verification['success'],
            'status'                 => $verification['status'],
            'provider_transaction_id'=> $sessionId,
            'message'                => $verification['message'],
        ];
    }
}
