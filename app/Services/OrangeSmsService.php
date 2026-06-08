<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeSmsService
{
    private const TOKEN_URL = 'https://api.orange.com/oauth/v3/token';
    private const SMS_BASE_URL = 'https://api.orange.com/smsmessaging/v1/outbound';

    private string $clientId;
    private string $clientSecret;
    private string $senderAddress;
    private string $senderName;
    private string $defaultCountryCode;

    public function __construct()
    {
        $this->clientId          = config('services.orange.client_id');
        $this->clientSecret      = config('services.orange.client_secret');
        $this->senderAddress     = config('services.orange.sender_address');
        $this->senderName        = config('services.orange.sender_name', 'SmartSMS');
        $this->defaultCountryCode = config('services.orange.default_country_code', '224');
    }

    /**
     * Envoie un SMS via l'API Orange SMS.
     *
     * @param  string $phone   Numéro du destinataire au format international (ex: +224XXXXXXXXX)
     * @param  string $message Contenu du message
     * @return array           Réponse JSON d'Orange
     *
     * @throws \RuntimeException si l'API retourne une erreur
     */
    /**
     * @param string      $phone      Numéro destinataire
     * @param string      $message    Contenu du SMS
     * @param string|null $senderName Sender name du client (remplace le défaut config si fourni)
     */
    public function send(string $phone, string $message, ?string $senderName = null): array
    {
        $token          = $this->getAccessToken();
        $recipientAddr  = $this->normalizePhone($phone);
        $encodedSender  = urlencode($this->senderAddress);

        // Utiliser le sender name du client si fourni, sinon fallback config
        $effectiveSenderName = $senderName ?? $this->senderName;

        $response = Http::withToken($token)
            ->acceptJson()
            ->post(self::SMS_BASE_URL . "/{$encodedSender}/requests", [
                'outboundSMSMessageRequest' => [
                    'address'                => $recipientAddr,
                    'senderAddress'          => $this->senderAddress,
                    'senderName'             => $effectiveSenderName,
                    'outboundSMSTextMessage' => [
                        'message' => $message,
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Orange SMS API : échec envoi', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'phone'  => $phone,
            ]);
            throw new \RuntimeException(
                "Orange SMS API error [{$response->status()}]: {$response->body()}"
            );
        }

        return $response->json();
    }

    /**
     * Retourne les contrats/soldes SMS du compte Orange.
     */
    public function getContracts(): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://api.orange.com/sms/admin/v1/contracts');

        if ($response->failed()) {
            throw new \RuntimeException(
                "Orange Admin API error [{$response->status()}]: {$response->body()}"
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Récupère un access token OAuth2, mis en cache 55 minutes
     * (les tokens Orange expirent généralement après 60 minutes).
     */
    private function getAccessToken(): string
    {
        return Cache::remember('orange_sms_access_token', 3300, function () {
            $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Accept'        => 'application/json',
            ])->asForm()->post(self::TOKEN_URL, [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->failed()) {
                throw new \RuntimeException(
                    "Orange OAuth error [{$response->status()}]: {$response->body()}"
                );
            }

            $token = $response->json('access_token');

            if (empty($token)) {
                throw new \RuntimeException('Orange OAuth : access_token absent de la réponse');
            }

            return $token;
        });
    }

    /**
     * Normalise un numéro vers le format tel:+XXXX attendu par Orange.
     *
     * Cas gérés :
     *  - tel:+224620103254  → déjà bon
     *  - +224620103254      → tel:+224620103254
     *  - 00224620103254     → tel:+224620103254
     *  - 224620103254       → tel:+224620103254  (indicatif sans +)
     *  - 620103254          → tel:+224620103254  (local 9 chiffres, Guinée)
     *  - 0620103254         → tel:+224620103254  (local avec 0 devant)
     */
    public function normalizePhone(string $phone): string
    {
        $phone = trim($phone);

        if (str_starts_with($phone, 'tel:')) {
            return $phone;
        }

        // Supprimer espaces, tirets, parenthèses, points
        $digits = preg_replace('/[\s\-\(\)\.\+]+/', '', $phone);
        // Garder le + s'il était en tête
        $hasPlus = str_starts_with($phone, '+');

        // Commence par 00 → indicatif international
        if (str_starts_with($digits, '00')) {
            return 'tel:+' . substr($digits, 2);
        }

        // Déjà préfixé + → format international complet
        if ($hasPlus) {
            return "tel:+{$digits}";
        }

        // Numéro local commençant par 0 (ex: 0620103254) → retirer le 0, ajouter indicatif
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return "tel:+{$this->defaultCountryCode}" . substr($digits, 1);
        }

        // Numéro local sans 0 (ex: 620103254, 9 chiffres) → ajouter indicatif
        if (strlen($digits) === 9) {
            return "tel:+{$this->defaultCountryCode}{$digits}";
        }

        // Numéro avec indicatif sans + (ex: 224620103254) → ajouter +
        return "tel:+{$digits}";
    }
}
