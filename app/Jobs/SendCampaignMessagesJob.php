<?php

namespace App\Jobs;

use App\Mail\CampaignEmailMail;
use App\Models\Branding;
use App\Models\Messages;
use App\Models\Campagnes;
use App\Models\SenderName;
use App\Models\Subscription;
use App\Models\DeviceToken;
use App\Services\OrangeSmsService;
use App\Services\PushService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCampaignMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    protected Campagnes $campaign;

    public function __construct(Campagnes $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(OrangeSmsService $sms, WhatsAppService $whatsapp): void
    {
        $campaign = $this->campaign->fresh();

        if (!$campaign) {
            Log::warning('SendCampaignMessagesJob : campagne introuvable', ['id' => $this->campaign->id]);
            return;
        }

        // Vérifier le quota SMS de l'abonnement actif
        $subscription = Subscription::where('client_id', $campaign->client_id)
            ->where('status', 'active')
            ->first();

        $messages = Messages::where('campagnes_id', $campaign->id)
            ->whereIn('status', ['queued', 'scheduled'])
            ->get();

        if ($messages->isEmpty()) {
            Log::info('SendCampaignMessagesJob : aucun message en file', ['campaign_id' => $campaign->id]);
            $campaign->update(['status' => 'terminer']);
            return;
        }

        $totalMessages = $messages->count();

        // Bloquer si quota insuffisant
        if ($subscription && !$subscription->hasSmsAvailable($totalMessages)) {
            Log::warning('SendCampaignMessagesJob : quota SMS insuffisant', [
                'campaign_id' => $campaign->id,
                'needed'      => $totalMessages,
                'available'   => $subscription->sms_quota - $subscription->sms_used,
            ]);
            $campaign->update(['status' => 'rejeter']);
            return;
        }

        // Charger le sender name par défaut du client pour cet envoi
        $defaultSender = SenderName::where('client_id', $campaign->client_id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderByDesc('is_default')  // is_default = true en premier
            ->first();

        $senderName = $defaultSender?->name;

        // Branding actif du client (pour les campagnes email)
        $branding = Branding::where('client_id', $campaign->client_id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->first()
            ?? new Branding(['brand_name' => $campaign->client?->company_name]);

        Log::info('SendCampaignMessagesJob : sender name résolu', [
            'campaign_id' => $campaign->id,
            'sender_name' => $senderName ?? 'défaut config',
        ]);

        // ─── Push : broadcast unique → une seule notification pour tous les devices ───
        if ($campaign->channel === 'push') {
            $first  = $messages->first();
            $tokens = DeviceToken::where('client_id', $campaign->client_id)
                ->pluck('token')
                ->toArray();

            if (!empty($tokens)) {
                try {
                    (new PushService())->sendToTokens(
                        $tokens,
                        $first->subject ?? $campaign->name,
                        $first->content,
                        ['campaign_id' => (string) $campaign->id],
                    );
                    $campaign->client?->increment('messages_sent', $messages->count());
                } catch (\Throwable $e) {
                    Log::error('Push broadcast échoué', ['campaign_id' => $campaign->id, 'error' => $e->getMessage()]);
                    $campaign->update(['status' => 'rejeter']);
                    return;
                }
            } else {
                Log::warning('Push: aucun device token enregistré', ['campaign_id' => $campaign->id]);
            }

            Messages::where('campagnes_id', $campaign->id)
                ->whereIn('status', ['queued', 'scheduled'])
                ->update(['status' => 'sent', 'sent_at' => now()]);

            $campaign->update(['status' => 'terminer']);
            Log::info('Push broadcast terminé', ['campaign_id' => $campaign->id, 'tokens' => count($tokens)]);
            return;
        }
        // ────────────────────────────────────────────────────────────────────────────

        $sentCount   = 0;
        $failedCount = 0;

        foreach ($messages as $message) {
            try {
                $this->dispatchToGateway($message, $sms, $whatsapp, $senderName, $branding);

                $message->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                ]);

                $sentCount++;

            } catch (\Throwable $e) {
                Log::error('Échec envoi message', [
                    'message_id'  => $message->id,
                    'campaign_id' => $campaign->id,
                    'error'       => $e->getMessage(),
                ]);

                $message->update(['status' => 'failed']);
                $failedCount++;
            }
        }

        // Déduire du quota
        if ($subscription && $sentCount > 0) {
            $subscription->consumeSms($sentCount);
        }

        // Mettre à jour le compteur messages_sent du client
        if ($sentCount > 0) {
            $campaign->client?->increment('messages_sent', $sentCount);
        }

        $campaign->update(['status' => 'terminer']);

        Log::info('SendCampaignMessagesJob terminé', [
            'campaign_id' => $campaign->id,
            'sent'        => $sentCount,
            'failed'      => $failedCount,
        ]);
    }

    protected function dispatchToGateway(Messages $message, OrangeSmsService $sms, WhatsAppService $whatsapp, ?string $senderName = null, ?Branding $branding = null): void
    {
        $contact = $message->contact;
        $channel = $message->channel ?? 'sms';

        switch ($channel) {

            case 'sms':
                if (empty($contact?->phone)) {
                    throw new \RuntimeException("Contact sans numéro de téléphone (message_id={$message->id})");
                }
                // Utilise le sender name du client (créé à l'inscription) ou le défaut config
                $sms->send($contact->phone, $message->content, $senderName);
                break;

            case 'whatsapp':
                if (empty($contact?->phone)) {
                    throw new \RuntimeException("Contact sans numéro de téléphone (message_id={$message->id})");
                }
                $whatsapp->send($contact->phone, $message->content);
                break;

            case 'email':
                if (empty($contact?->email)) {
                    throw new \RuntimeException("Contact sans adresse email (message_id={$message->id})");
                }
                $activeBranding = $branding ?? new Branding(['brand_name' => config('app.name')]);
                Mail::to($contact->email)->send(new CampaignEmailMail(
                    campaign : $message->campaign,
                    branding : $activeBranding,
                    contact  : (object) ['name' => $contact->name ?? null, 'email' => $contact->email],
                    content  : $message->content,
                    ctaText  : $message->cta,
                    ctaUrl   : $message->cta_url,
                ));
                break;

            default:
                throw new \RuntimeException("Canal inconnu : {$channel} (message_id={$message->id})");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendCampaignMessagesJob a échoué définitivement', [
            'campaign_id' => $this->campaign->id,
            'error'       => $exception->getMessage(),
        ]);

        $this->campaign->update(['status' => 'rejeter']);
    }
}
