<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

/**
 * Intégration MTN Mobile Money (API Collections).
 *
 * Documentation : https://momodeveloper.mtn.com
 * Sandbox  : https://sandbox.momodeveloper.mtn.com
 * Production: https://proxy.momoapi.mtn.com
 *
 * Flux :
 *   1. Obtenir un access token (Basic Auth avec apiUser:apiKey)
 *   2. POST /collection/v1_0/requesttopay → MTN envoie une notification USSD au client
 *   3. Le client confirme sur son téléphone
 *   4. MTN appelle le callback_url OU on poll GET /collection/v1_0/requesttopay/{referenceId}
 *
 * Credentials à configurer dans .env :
 *   MTN_MOMO_API_USER, MTN_MOMO_API_KEY, MTN_MOMO_SUBSCRIPTION_KEY
 */
class MtnMomoService implements PaymentGatewayInterface
{
    private string $apiUser;
    private string $apiKey;
    private string $subscriptionKey;
    private string $environment;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiUser         = config('services.mtn_momo.api_user', '');
        $this->apiKey          = config('services.mtn_momo.api_key', '');
        $this->subscriptionKey = config('services.mtn_momo.subscription_key', '');
        $this->environment     = config('services.mtn_momo.env', 'sandbox');

        $this->baseUrl = $this->environment === 'production'
            ? 'https://proxy.momoapi.mtn.com'
            : 'https://sandbox.momodeveloper.mtn.com';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiUser) && !empty($this->apiKey) && !empty($this->subscriptionKey);
    }

    private function getAccessToken(): ?string
    {
        $response = Http::timeout(15)
            ->withBasicAuth($this->apiUser, $this->apiKey)
            ->withHeaders(['Ocp-Apim-Subscription-Key' => $this->subscriptionKey])
            ->post("{$this->baseUrl}/collection/token/");

        return $response->json('access_token');
    }

    public function initiate(array $data): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Échec d\'authentification MTN MoMo (vérifiez MTN_MOMO_API_USER, API_KEY et SUBSCRIPTION_KEY)',
                'raw'     => [],
            ];
        }

        $referenceId = (string) Str::uuid();
        $phone       = preg_replace('/[^0-9]/', '', $data['phone'] ?? '');

        $response = Http::timeout(30)
            ->withToken($token)
            ->withHeaders([
                'X-Reference-Id'            => $referenceId,
                'X-Target-Environment'      => $this->environment,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])
            ->post("{$this->baseUrl}/collection/v1_0/requesttopay", [
                'amount'       => (string) (int) $data['amount'],
                'currency'     => $data['currency'] ?? 'GNF',
                'externalId'   => $data['reference'],
                'payer'        => [
                    'partyIdType' => 'MSISDN',
                    'partyId'     => $phone,
                ],
                'payerMessage' => $data['description'] ?? 'Paiement SmartSMS',
                'payeeNote'    => $data['reference'],
            ]);

        // MTN retourne HTTP 202 (Accepted) si la demande est acceptée
        if ($response->status() === 202) {
            return [
                'success'                => true,
                'provider_transaction_id'=> $referenceId,
                'payment_url'            => null,
                'message'                => 'Demande MTN MoMo envoyée. Veuillez confirmer le paiement sur votre téléphone.',
                'raw'                    => ['reference_id' => $referenceId],
            ];
        }

        $body = $response->json() ?? [];

        return [
            'success' => false,
            'message' => $body['message'] ?? "Erreur MTN MoMo (HTTP {$response->status()})",
            'raw'     => $body,
        ];
    }

    public function verify(string $providerTransactionId): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'status' => 'pending', 'message' => 'Échec d\'authentification MTN MoMo', 'raw' => []];
        }

        $response = Http::timeout(15)
            ->withToken($token)
            ->withHeaders([
                'X-Target-Environment'      => $this->environment,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])
            ->get("{$this->baseUrl}/collection/v1_0/requesttopay/{$providerTransactionId}");

        $body = $response->json() ?? [];

        $mtnStatus = $body['status'] ?? '';
        $status = match ($mtnStatus) {
            'SUCCESSFUL' => 'completed',
            'FAILED'     => 'failed',
            default      => 'pending',
        };

        return [
            'success' => $status === 'completed',
            'status'  => $status,
            'message' => $body['reason'] ?? "Statut MTN MoMo : {$mtnStatus}",
            'raw'     => $body,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        // MTN MoMo peut envoyer le referenceId ou l'externalId dans le callback
        $referenceId = $payload['referenceId'] ?? $payload['externalId'] ?? null;

        if (!$referenceId) {
            return [
                'success' => false,
                'status'  => 'failed',
                'message' => 'referenceId manquant dans le webhook MTN MoMo',
                'provider_transaction_id' => null,
            ];
        }

        $verification = $this->verify($referenceId);

        return [
            'success'                => $verification['success'],
            'status'                 => $verification['status'],
            'provider_transaction_id'=> $referenceId,
            'message'                => $verification['message'],
        ];
    }
}
