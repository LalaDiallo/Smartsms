<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Initier une demande de paiement auprès du provider.
     *
     * $data attendu : reference, amount, currency, description, notify_url,
     *                 return_url, cancel_url, customer_name, customer_email,
     *                 customer_phone, phone (mobile money)
     *
     * Retourne : [
     *   'success'                => bool,
     *   'provider_transaction_id'=> string|null,
     *   'payment_url'            => string|null,  // URL de redirection (card/orange/wave)
     *   'message'                => string,
     *   'raw'                    => array,         // réponse brute du provider
     * ]
     */
    public function initiate(array $data): array;

    /**
     * Vérifier le statut d'un paiement auprès du provider.
     *
     * Retourne : [
     *   'success' => bool,
     *   'status'  => 'pending'|'completed'|'failed',
     *   'message' => string,
     *   'raw'     => array,
     * ]
     */
    public function verify(string $providerTransactionId): array;

    /**
     * Traiter un webhook entrant du provider.
     *
     * Retourne : [
     *   'success'                => bool,
     *   'status'                 => 'pending'|'completed'|'failed',
     *   'provider_transaction_id'=> string|null,
     *   'message'                => string,
     * ]
     */
    public function handleWebhook(array $payload): array;

    /**
     * Vérifier que le service est correctement configuré (clés API présentes).
     */
    public function isConfigured(): bool;
}
