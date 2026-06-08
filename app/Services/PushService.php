<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushService
{
    private array  $serviceAccount;
    private string $projectId;

    public function __construct()
    {
        $credPath = config('services.firebase.credentials');

        if (!$credPath || !file_exists($credPath)) {
            throw new \RuntimeException('Firebase credentials file not found: ' . $credPath);
        }

        $this->serviceAccount = json_decode(file_get_contents($credPath), true);
        $this->projectId      = config('services.firebase.project_id')
            ?? $this->serviceAccount['project_id']
            ?? throw new \RuntimeException('Firebase project_id not configured');
    }

    // ── API publique ────────────────────────────────────────────────────────────

    /**
     * Envoie une notification push à tous les appareils d'un client.
     */
    public function sendToClient(int $clientId, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::where('client_id', $clientId)
            ->pluck('token')
            ->toArray();

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoie une notification push à tous les appareils d'un utilisateur.
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::where('user_id', $userId)
            ->pluck('token')
            ->toArray();

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoie à une liste de tokens FCM.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        if (empty($tokens)) {
            return ['sent' => 0, 'failed' => 0, 'invalid' => []];
        }

        $accessToken = $this->getAccessToken();
        $results     = ['sent' => 0, 'failed' => 0, 'invalid' => []];

        foreach ($tokens as $token) {
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(10)
                    ->post(
                        "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                        [
                            'message' => [
                                'token'        => $token,
                                'notification' => ['title' => $title, 'body' => $body],
                                'webpush'      => [
                                    'notification' => [
                                        'title' => $title,
                                        'body'  => $body,
                                        'icon'  => '/icon-192x192.png',
                                        'badge' => '/badge-72x72.png',
                                        'vibrate' => [200, 100, 200],
                                    ],
                                    'fcm_options' => ['link' => '/dashboard'],
                                ],
                                'data' => array_map('strval', $data),
                            ],
                        ]
                    );

                if ($response->successful()) {
                    $results['sent']++;
                    DeviceToken::where('token', $token)
                        ->update(['last_used_at' => now()]);
                } else {
                    $errorCode = $response->json('error.details.0.errorCode') ?? '';
                    // Tokens invalides/expirés → on les supprime
                    if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        DeviceToken::where('token', $token)->delete();
                        $results['invalid'][] = $token;
                    } else {
                        $results['failed']++;
                        Log::warning('FCM send failed', [
                            'token' => substr($token, 0, 20) . '...',
                            'error' => $response->json('error.message'),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                Log::error('FCM exception', ['error' => $e->getMessage()]);
            }
        }

        Log::info('PushService result', $results);
        return $results;
    }

    // ── OAuth 2.0 — Service Account → Access Token ──────────────────────────────

    private function getAccessToken(): string
    {
        return Cache::remember('firebase_fcm_access_token', 3500, function () {
            $now = time();

            $header  = $this->base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = $this->base64url(json_encode([
                'iss'   => $this->serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ]));

            $signingInput = "{$header}.{$payload}";
            $key          = openssl_pkey_get_private($this->serviceAccount['private_key']);

            if (!$key) {
                throw new \RuntimeException('Clé privée Firebase invalide');
            }

            openssl_sign($signingInput, $signature, $key, OPENSSL_ALGO_SHA256);

            $jwt = "{$signingInput}.{$this->base64url($signature)}";

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException('Firebase OAuth error: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    private function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
