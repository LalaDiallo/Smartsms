<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

/**
 * Intégration CinetPay pour les paiements par carte Visa/Mastercard.
 *
 * Documentation : https://docs.cinetpay.com
 * CinetPay supporte la Guinée (GNF) et tous les pays d'Afrique de l'Ouest.
 *
 * Credentials à configurer dans .env :
 *   CINETPAY_API_KEY, CINETPAY_SITE_ID, CINETPAY_SECRET_KEY
 */
class CinetPayService implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $siteId;
    private string $secretKey;
    private string $baseUrl = 'https://api-checkout.cinetpay.com/v2';

    public function __construct()
    {
        $this->apiKey    = config('services.cinetpay.api_key', '');
        $this->siteId    = config('services.cinetpay.site_id', '');
        $this->secretKey = config('services.cinetpay.secret_key', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->siteId);
    }

    public function initiate(array $data): array
    {
        $response = Http::timeout(30)->post("{$this->baseUrl}/payment", [
            'apikey'                => $this->apiKey,
            'site_id'               => $this->siteId,
            'transaction_id'        => $data['reference'],
            'amount'                => (int) $data['amount'],
            'currency'              => $data['currency'] ?? 'GNF',
            'description'           => $data['description'] ?? 'Paiement SmartSMS',
            'notify_url'            => $data['notify_url'],
            'return_url'            => $data['return_url'],
            'cancel_url'            => $data['cancel_url'] ?? $data['return_url'],
            'channels'              => 'CREDIT_CARD',
            'lang'                  => 'fr',
            'customer_name'         => $data['customer_name'] ?? '',
            'customer_email'        => $data['customer_email'] ?? '',
            'customer_phone_number' => $data['customer_phone'] ?? '',
            'customer_address'      => 'Conakry',
            'customer_city'         => 'Conakry',
            'customer_country'      => 'GN',
            'customer_state'        => 'GN',
            'customer_zip_code'     => '00224',
            'metadata'              => $data['reference'],
        ]);

        $body = $response->json() ?? [];

        // CinetPay retourne code "201" (string) pour une création réussie
        if ($response->successful() && ($body['code'] ?? '') === '201') {
            return [
                'success'                => true,
                'provider_transaction_id'=> $data['reference'],
                'payment_url'            => $body['data']['payment_url'] ?? null,
                'message'                => $body['message'] ?? 'Paiement initialisé',
                'raw'                    => $body,
            ];
        }

        return [
            'success' => false,
            'message' => $body['description'] ?? $body['message'] ?? 'Erreur CinetPay',
            'raw'     => $body,
        ];
    }

    public function verify(string $providerTransactionId): array
    {
        $response = Http::timeout(30)->post("{$this->baseUrl}/payment/check", [
            'apikey'         => $this->apiKey,
            'site_id'        => $this->siteId,
            'transaction_id' => $providerTransactionId,
        ]);

        $body = $response->json() ?? [];

        $cinetStatus = $body['data']['status'] ?? '';
        $status = match ($cinetStatus) {
            'ACCEPTED'            => 'completed',
            'REFUSED', 'CANCELLED'=> 'failed',
            default               => 'pending',
        };

        return [
            'success' => $status === 'completed',
            'status'  => $status,
            'message' => $body['message'] ?? "Statut : {$cinetStatus}",
            'raw'     => $body,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        // CinetPay envoie : cpm_site_id, cpm_trans_id, cpm_trans_status, cpm_amount, ...
        $transactionId = $payload['cpm_trans_id'] ?? null;

        if (!$transactionId) {
            return [
                'success' => false,
                'status'  => 'failed',
                'message' => 'cpm_trans_id manquant dans le payload CinetPay',
                'provider_transaction_id' => null,
            ];
        }

        // On vérifie directement auprès de l'API pour confirmer l'authenticité
        $verification = $this->verify($transactionId);

        return [
            'success'                => $verification['success'],
            'status'                 => $verification['status'],
            'provider_transaction_id'=> $transactionId,
            'message'                => $verification['message'],
        ];
    }
}
