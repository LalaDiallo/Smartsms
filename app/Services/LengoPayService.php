<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LengoPayService
{
    private string $siteId;
    private string $licenseKey;
    private string $apiUrl;

    public function __construct()
    {
        $cfg            = config('services.lengopay');
        $this->siteId      = $cfg['site_id'];
        $this->licenseKey  = $cfg['license_key'];
        $this->apiUrl      = $cfg['env'] === 'production'
            ? $cfg['prod_url']
            : $cfg['sandbox_url'];
    }

    /**
     * Génère une URL de paiement LengoPay.
     *
     * @return array{pay_id: string, payment_url: string}
     * @throws \RuntimeException
     */
    public function createPayment(
        int    $amount,
        string $currency   = 'GNF',
        string $returnUrl  = '',
        string $failureUrl = '',
        string $callbackUrl = '',
    ): array {
        $payload = [
            'websiteid'   => $this->siteId,
            'amount'      => $amount,
            'currency'    => $currency,
        ];

        if ($returnUrl)   $payload['return_url']   = $returnUrl;
        if ($failureUrl)  $payload['failure_url']  = $failureUrl;
        if ($callbackUrl) $payload['callback_url'] = $callbackUrl;

        $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->licenseKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->timeout(15)
            ->post($this->apiUrl, $payload);

        if (!$response->successful()) {
            Log::error('LengoPay createPayment failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('LengoPay: ' . ($response->json('message') ?? 'Erreur inconnue'));
        }

        $data = $response->json();

        if (!isset($data['pay_id'], $data['payment_url'])) {
            throw new \RuntimeException('LengoPay: réponse invalide');
        }

        return [
            'pay_id'      => $data['pay_id'],
            'payment_url' => $data['payment_url'],
        ];
    }
}
