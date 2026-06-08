<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LengoPayGateway implements PaymentGatewayInterface
{
    private string $siteId;
    private string $licenseKey;
    private string $apiUrl;

    public function __construct()
    {
        $cfg              = config('services.lengopay');
        $this->siteId     = $cfg['site_id'];
        $this->licenseKey = $cfg['license_key'];
        $this->apiUrl     = $cfg['env'] === 'production'
            ? $cfg['prod_url']
            : $cfg['sandbox_url'];
    }

    public function name(): string { return 'LengoPay'; }

    public function createPayment(
        int    $amount,
        string $currency   = 'GNF',
        string $returnUrl  = '',
        string $failureUrl = '',
        string $callbackUrl = '',
    ): array {
        $payload = ['websiteid' => $this->siteId, 'amount' => $amount, 'currency' => $currency];
        if ($returnUrl)   $payload['return_url']   = $returnUrl;
        if ($failureUrl)  $payload['failure_url']  = $failureUrl;
        if ($callbackUrl) $payload['callback_url'] = $callbackUrl;

        $http = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->licenseKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->timeout(20);

        // En sandbox/local, désactiver la vérification SSL (cURL XAMPP Windows)
        if (config('services.lengopay.env') !== 'production') {
            $http = $http->withoutVerifying();
        }

        $response = $http->post($this->apiUrl, $payload);

        if (!$response->successful()) {
            Log::error('LengoPay createPayment failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('LengoPay: ' . ($response->json('message') ?? 'Erreur inconnue'));
        }

        $data = $response->json();
        if (!isset($data['pay_id'], $data['payment_url'])) {
            throw new \RuntimeException('LengoPay: réponse invalide');
        }

        return ['pay_id' => $data['pay_id'], 'payment_url' => $data['payment_url']];
    }

    public function handleCallback(array $payload): array
    {
        return [
            'status' => strtoupper($payload['status'] ?? ''),
            'pay_id' => $payload['pay_id'] ?? '',
        ];
    }
}
