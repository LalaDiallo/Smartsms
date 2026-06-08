<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

/**
 * Intégration Orange Money Guinée (WebPay API).
 *
 * Documentation : https://developer.orange.com/apis/om-webpay-guinea
 * Endpoint de production : https://api.orange.com/orange-money-webpay/gn/v1
 *
 * NOTE : Ce service est distinct du OrangeSmsService (qui gère l'envoi de SMS).
 * Les credentials Orange Money sont séparés des credentials Orange SMS.
 *
 * Credentials à configurer dans .env :
 *   ORANGE_MONEY_CLIENT_ID, ORANGE_MONEY_CLIENT_SECRET, ORANGE_MONEY_MERCHANT_KEY
 */
class OrangeMoneyService implements PaymentGatewayInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $merchantKey;
    private string $baseUrl;
    private string $authUrl = 'https://api.orange.com/oauth/v3/token';

    public function __construct()
    {
        $this->clientId     = config('services.orange_money.client_id', '');
        $this->clientSecret = config('services.orange_money.client_secret', '');
        $this->merchantKey  = config('services.orange_money.merchant_key', '');

        $env = config('services.orange_money.env', 'sandbox');
        $this->baseUrl = $env === 'production'
            ? 'https://api.orange.com/orange-money-webpay/gn/v1'
            : 'https://api.orange.com/orange-money-webpay/dev/v1';
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->merchantKey);
    }

    private function getAccessToken(): ?string
    {
        $response = Http::timeout(15)
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->authUrl, ['grant_type' => 'client_credentials']);

        return $response->json('access_token');
    }

    public function initiate(array $data): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Échec d\'authentification Orange Money (vérifiez ORANGE_MONEY_CLIENT_ID et CLIENT_SECRET)',
                'raw'     => [],
            ];
        }

        $response = Http::timeout(30)
            ->withToken($token)
            ->post("{$this->baseUrl}/{$this->merchantKey}/webpayment", [
                'merchant_key' => $this->merchantKey,
                'currency'     => $data['currency'] ?? 'GNF',
                'order_id'     => $data['reference'],
                'amount'       => (int) $data['amount'],
                'return_url'   => $data['return_url'],
                'cancel_url'   => $data['cancel_url'] ?? $data['return_url'],
                'notif_url'    => $data['notify_url'],
                'lang'         => 'fr',
                'reference'    => $data['description'] ?? 'SmartSMS',
            ]);

        $body = $response->json() ?? [];

        if ($response->successful() && !empty($body['payment_url'])) {
            return [
                'success'                => true,
                'provider_transaction_id'=> $body['pay_token'] ?? $data['reference'],
                'payment_url'            => $body['payment_url'],
                'message'                => 'Paiement Orange Money initialisé',
                'raw'                    => $body,
            ];
        }

        return [
            'success' => false,
            'message' => $body['message'] ?? 'Erreur Orange Money WebPay',
            'raw'     => $body,
        ];
    }

    public function verify(string $providerTransactionId): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'status' => 'pending', 'message' => 'Échec d\'authentification Orange Money', 'raw' => []];
        }

        $response = Http::timeout(15)
            ->withToken($token)
            ->get("{$this->baseUrl}/{$this->merchantKey}/paymentstatus", [
                'order_id' => $providerTransactionId,
            ]);

        $body = $response->json() ?? [];

        $status = match ($body['status'] ?? '') {
            'SUCCESS'              => 'completed',
            'FAILED', 'CANCELLED' => 'failed',
            default                => 'pending',
        };

        return [
            'success' => $status === 'completed',
            'status'  => $status,
            'message' => $body['message'] ?? "Statut Orange Money : " . ($body['status'] ?? 'PENDING'),
            'raw'     => $body,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        // Orange Money envoie order_id et notif_token dans le webhook
        $orderId = $payload['order_id'] ?? $payload['notif_token'] ?? null;

        if (!$orderId) {
            return [
                'success' => false,
                'status'  => 'failed',
                'message' => 'order_id manquant dans le webhook Orange Money',
                'provider_transaction_id' => null,
            ];
        }

        $verification = $this->verify($orderId);

        return [
            'success'                => $verification['success'],
            'status'                 => $verification['status'],
            'provider_transaction_id'=> $orderId,
            'message'                => $verification['message'],
        ];
    }
}
