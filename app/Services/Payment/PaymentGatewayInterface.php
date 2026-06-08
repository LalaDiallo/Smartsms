<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Génère une URL de paiement et retourne le pay_id + payment_url.
     *
     * @return array{pay_id: string, payment_url: string}
     */
    public function createPayment(
        int    $amount,
        string $currency,
        string $returnUrl,
        string $failureUrl,
        string $callbackUrl,
    ): array;

    /**
     * Traite le callback/webhook reçu du provider.
     * Retourne ['status' => 'SUCCESS'|'FAILED', 'pay_id' => string]
     */
    public function handleCallback(array $payload): array;

    /** Nom lisible du provider (pour les logs). */
    public function name(): string;
}
