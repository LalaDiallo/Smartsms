<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $provider;

    public function __construct()
    {
        $this->provider = config('services.whatsapp.provider', 'meta');
    }

    /**
     * Envoie un message WhatsApp au numéro donné.
     *
     * @throws \RuntimeException si non configuré ou si l'API échoue
     */
    public function send(string $phone, string $message): array
    {
        return match ($this->provider) {
            'meta'   => $this->sendViaMeta($phone, $message),
            'twilio' => $this->sendViaTwilio($phone, $message),
            default  => throw new \RuntimeException("Provider WhatsApp inconnu : {$this->provider}"),
        };
    }

    // ─── Meta WhatsApp Business API ───────────────────────────────────────────

    private function sendViaMeta(string $phone, string $message): array
    {
        $token         = config('services.whatsapp.meta.access_token');
        $phoneNumberId = config('services.whatsapp.meta.phone_number_id');

        if (empty($token) || empty($phoneNumberId)) {
            throw new \RuntimeException('WhatsApp Meta non configuré — renseignez WHATSAPP_META_TOKEN et WHATSAPP_META_PHONE_ID dans le .env');
        }

        $recipient = $this->normalizePhone($phone);

        $response = Http::withToken($token)
            ->acceptJson()
            ->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $recipient,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ]);

        if ($response->failed()) {
            Log::error('WhatsApp Meta : échec envoi', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'phone'  => $phone,
            ]);
            throw new \RuntimeException("WhatsApp Meta error [{$response->status()}]: {$response->body()}");
        }

        return $response->json();
    }

    // ─── Twilio WhatsApp ──────────────────────────────────────────────────────

    private function sendViaTwilio(string $phone, string $message): array
    {
        $sid   = config('services.whatsapp.twilio.account_sid');
        $token = config('services.whatsapp.twilio.auth_token');
        $from  = config('services.whatsapp.twilio.from_number');

        if (empty($sid) || empty($token) || empty($from)) {
            throw new \RuntimeException('WhatsApp Twilio non configuré — renseignez TWILIO_SID, TWILIO_TOKEN et TWILIO_WHATSAPP_FROM dans le .env');
        }

        $recipient = 'whatsapp:' . $this->normalizePhone($phone);

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => 'whatsapp:' . $from,
                'To'   => $recipient,
                'Body' => $message,
            ]);

        if ($response->failed()) {
            Log::error('WhatsApp Twilio : échec envoi', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'phone'  => $phone,
            ]);
            throw new \RuntimeException("WhatsApp Twilio error [{$response->status()}]: {$response->body()}");
        }

        return $response->json();
    }

    // ─── Utilitaires ─────────────────────────────────────────────────────────

    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $digits = preg_replace('/[\s\-\(\)\.]+/', '', $phone);

        if (str_starts_with($digits, '00')) {
            return '+' . substr($digits, 2);
        }
        if (str_starts_with($digits, '+')) {
            return $digits;
        }
        if (strlen($digits) === 9) {
            return '+' . config('services.orange.default_country_code', '224') . $digits;
        }
        return '+' . $digits;
    }
}
