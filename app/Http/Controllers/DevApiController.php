<?php

namespace App\Http\Controllers;

use App\Models\Campagnes;
use App\Models\SenderName;
use App\Services\OrangeSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DevApiController extends Controller
{
    // ── Envoyer un SMS unique ─────────────────────────────────────────────

    public function sendSms(Request $request, OrangeSmsService $sms)
    {
        $client = $request->_dev_client;
        $apiKey = $request->_dev_api_key;

        $validated = $request->validate([
            'to'      => 'required|string|max:20',
            'message' => 'required|string|max:160',
        ]);

        $phone = preg_replace('/\D/', '', $validated['to']);
        if (strlen($phone) === 9) {
            $phone = '224' . $phone;
        }

        $senderName = SenderName::where('client_id', $client->id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->value('name') ?? $client->company_name;

        $content = $validated['message'] . "\n-- \n" . $senderName;

        try {
            $sms->send($phone, $content);

            $messageId = Str::uuid()->toString();

            Log::info('DevAPI SMS sent', [
                'service_id' => $apiKey->service_id,
                'to'         => $phone,
            ]);

            return response()->json([
                'success'    => true,
                'message_id' => $messageId,
                'to'         => $phone,
                'status'     => 'sent',
                'sent_at'    => now()->toIso8601String(),
            ]);

        } catch (\Throwable $e) {
            Log::error('DevAPI sendSms error', ['error' => $e->getMessage(), 'service_id' => $apiKey->service_id]);
            return response()->json([
                'success' => false,
                'error'   => 'send_failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ── Envoyer des SMS en masse ──────────────────────────────────────────

    public function sendBulk(Request $request, OrangeSmsService $sms)
    {
        $client = $request->_dev_client;
        $apiKey = $request->_dev_api_key;

        $validated = $request->validate([
            'recipients'    => 'required|array|min:1|max:1000',
            'recipients.*'  => 'required|string|max:20',
            'message'       => 'required|string|max:160',
            'campaign_name' => 'nullable|string|max:255',
        ]);

        $senderName = SenderName::where('client_id', $client->id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->value('name') ?? $client->company_name;

        $content = $validated['message'] . "\n-- \n" . $senderName;

        $campaign = Campagnes::create([
            'client_id'  => $client->id,
            'user_id'    => $client->users()->first()?->id,
            'name'       => $validated['campaign_name'] ?? ('API Bulk ' . now()->format('Y-m-d H:i')),
            'status'     => 'programmer',
            'channel'    => 'sms',
            'region'     => 'API',
            'start_date' => now(),
            'end_date'   => now(),
            'settings'   => ['source' => 'developer_api', 'service_id' => $apiKey->service_id],
        ]);

        $sent   = 0;
        $failed = 0;
        $details = [];

        foreach ($validated['recipients'] as $recipient) {
            $phone = preg_replace('/\D/', '', $recipient);
            if (strlen($phone) === 9) {
                $phone = '224' . $phone;
            }

            try {
                $sms->send($phone, $content);
                $sent++;
                $details[] = ['to' => $phone, 'status' => 'sent'];
            } catch (\Throwable $e) {
                $failed++;
                $details[] = ['to' => $phone, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        $campaign->update(['status' => 'terminer']);

        return response()->json([
            'success'     => true,
            'campaign_id' => $campaign->id,
            'total'       => count($validated['recipients']),
            'sent'        => $sent,
            'failed'      => $failed,
            'details'     => $details,
        ]);
    }

    // ── Solde SMS ─────────────────────────────────────────────────────────

    public function balance(Request $request)
    {
        $client = $request->_dev_client;

        $subscription = DB::table('subscriptions')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        return response()->json([
            'success'   => true,
            'client'    => $client->company_name,
            'sms_quota' => $subscription?->sms_quota ?? 0,
            'sms_used'  => $subscription?->sms_used ?? 0,
            'sms_left'  => $subscription ? ($subscription->sms_quota - $subscription->sms_used) : 0,
        ]);
    }

    // ── Documentation ─────────────────────────────────────────────────────

    public function docs()
    {
        $base = rtrim(config('app.url'), '/') . '/dev/v1';

        return response()->json([
            'title'       => 'SmartSMS Developer API',
            'version'     => '1.0.0',
            'base_url'    => $base,
            'description' => "Intégrez l'envoi de SMS dans vos applications avec l'API SmartSMS.",

            'authentication' => [
                'type'        => 'API Key via headers HTTP',
                'description' => "Chaque requête doit contenir les deux headers suivants.",
                'headers' => [
                    'X-Service-ID'   => 'Votre identifiant de service (ex: svc_xxxxxxxxxxxxxxxx)',
                    'X-Secret-Token' => 'Votre token secret (affiché une seule fois à la création de la clé)',
                ],
                'note' => "Le token secret est haché en SHA-256 côté serveur. Conservez-le précieusement — il ne peut pas être récupéré, seulement régénéré.",
            ],

            'endpoints' => [

                [
                    'method'      => 'POST',
                    'path'        => '/sms/send',
                    'full_url'    => "$base/sms/send",
                    'description' => 'Envoyer un SMS à un destinataire unique.',
                    'headers_required' => ['X-Service-ID', 'X-Secret-Token', 'Content-Type: application/json'],
                    'body' => [
                        ['field' => 'to',      'type' => 'string', 'required' => true,  'max' => 20,  'description' => 'Numéro de téléphone (9 ou 12 chiffres)'],
                        ['field' => 'message', 'type' => 'string', 'required' => true,  'max' => 160, 'description' => 'Contenu du SMS'],
                    ],
                    'response_200' => [
                        'success'    => true,
                        'message_id' => 'uuid-généré',
                        'to'         => '224620000000',
                        'status'     => 'sent',
                        'sent_at'    => '2026-05-20T14:00:00+00:00',
                    ],
                    'examples' => [
                        'curl'       => "curl -X POST $base/sms/send \\\n  -H \"X-Service-ID: svc_VOTRE_ID\" \\\n  -H \"X-Secret-Token: VOTRE_SECRET\" \\\n  -H \"Content-Type: application/json\" \\\n  -d '{\"to\":\"224620000000\",\"message\":\"Votre code OTP est 4821\"}'",
                        'php'        => "<?php\n\$response = file_get_contents('$base/sms/send', false, stream_context_create([\n  'http' => [\n    'method'  => 'POST',\n    'header'  => \"X-Service-ID: svc_VOTRE_ID\\r\\nX-Secret-Token: VOTRE_SECRET\\r\\nContent-Type: application/json\\r\\n\",\n    'content' => json_encode(['to' => '224620000000', 'message' => 'Code: 4821']),\n  ]\n]));\n\$data = json_decode(\$response, true);",
                        'javascript' => "const res = await fetch('$base/sms/send', {\n  method: 'POST',\n  headers: {\n    'X-Service-ID': 'svc_VOTRE_ID',\n    'X-Secret-Token': 'VOTRE_SECRET',\n    'Content-Type': 'application/json'\n  },\n  body: JSON.stringify({ to: '224620000000', message: 'Code: 4821' })\n});\nconst data = await res.json();",
                        'python'     => "import requests\ndata = requests.post('$base/sms/send',\n  headers={'X-Service-ID':'svc_VOTRE_ID','X-Secret-Token':'VOTRE_SECRET'},\n  json={'to':'224620000000','message':'Code: 4821'}\n).json()",
                    ],
                ],

                [
                    'method'      => 'POST',
                    'path'        => '/sms/bulk',
                    'full_url'    => "$base/sms/bulk",
                    'description' => 'Envoyer un SMS identique à plusieurs destinataires (max 1000).',
                    'body' => [
                        ['field' => 'recipients',    'type' => 'array',  'required' => true,  'max' => 1000, 'description' => 'Tableau de numéros'],
                        ['field' => 'message',       'type' => 'string', 'required' => true,  'max' => 160,  'description' => 'Contenu du SMS'],
                        ['field' => 'campaign_name', 'type' => 'string', 'required' => false, 'description' => 'Nom de la campagne (optionnel)'],
                    ],
                    'response_200' => [
                        'success'     => true,
                        'campaign_id' => 42,
                        'total'       => 3,
                        'sent'        => 3,
                        'failed'      => 0,
                        'details'     => [
                            ['to' => '224620000000', 'status' => 'sent'],
                            ['to' => '224621000000', 'status' => 'failed', 'error' => 'Raison'],
                        ],
                    ],
                    'examples' => [
                        'curl'       => "curl -X POST $base/sms/bulk \\\n  -H \"X-Service-ID: svc_VOTRE_ID\" \\\n  -H \"X-Secret-Token: VOTRE_SECRET\" \\\n  -H \"Content-Type: application/json\" \\\n  -d '{\"recipients\":[\"224620000000\",\"224621000000\"],\"message\":\"Promo !\",\"campaign_name\":\"Promo Mai\"}'",
                        'javascript' => "const res = await fetch('$base/sms/bulk', {\n  method: 'POST',\n  headers: {'X-Service-ID':'svc_VOTRE_ID','X-Secret-Token':'VOTRE_SECRET','Content-Type':'application/json'},\n  body: JSON.stringify({recipients:['224620000000','224621000000'],message:'Promo !'}) \n});\nconst data = await res.json();",
                        'python'     => "import requests\ndata = requests.post('$base/sms/bulk',\n  headers={'X-Service-ID':'svc_VOTRE_ID','X-Secret-Token':'VOTRE_SECRET'},\n  json={'recipients':['224620000000','224621000000'],'message':'Promo !'}\n).json()",
                    ],
                ],

                [
                    'method'      => 'GET',
                    'path'        => '/balance',
                    'full_url'    => "$base/balance",
                    'description' => 'Consulter le solde SMS disponible sur votre compte.',
                    'body'        => [],
                    'response_200' => [
                        'success'   => true,
                        'client'    => 'Votre Entreprise',
                        'sms_quota' => 10000,
                        'sms_used'  => 3250,
                        'sms_left'  => 6750,
                    ],
                    'examples' => [
                        'curl'       => "curl -X GET $base/balance \\\n  -H \"X-Service-ID: svc_VOTRE_ID\" \\\n  -H \"X-Secret-Token: VOTRE_SECRET\"",
                        'javascript' => "const res = await fetch('$base/balance', {\n  headers: {'X-Service-ID':'svc_VOTRE_ID','X-Secret-Token':'VOTRE_SECRET'}\n});\nconst data = await res.json();",
                        'python'     => "import requests\ndata = requests.get('$base/balance',\n  headers={'X-Service-ID':'svc_VOTRE_ID','X-Secret-Token':'VOTRE_SECRET'}\n).json()",
                    ],
                ],
            ],

            'error_codes' => [
                ['http_status' => 401, 'error' => 'missing_credentials', 'description' => 'Headers X-Service-ID ou X-Secret-Token absents'],
                ['http_status' => 401, 'error' => 'invalid_credentials', 'description' => 'Clé invalide, désactivée ou introuvable'],
                ['http_status' => 422, 'error' => 'validation_error',    'description' => 'Données invalides — voir le champ errors'],
                ['http_status' => 429, 'error' => 'rate_limit',          'description' => 'Trop de requêtes — 60 max par minute par clé'],
                ['http_status' => 500, 'error' => 'send_failed',         'description' => 'Erreur côté passerelle SMS'],
            ],

            'phone_number_format' => [
                'accepted' => ['224620000000', '620000000', '+224620000000'],
                'note'     => 'Les numéros à 9 chiffres sont automatiquement préfixés avec 224 (Guinée).',
            ],

            'limits' => [
                'sms_max_chars'      => 160,
                'bulk_max_per_call'  => 1000,
                'rate_limit'         => '60 requêtes / minute par clé API',
            ],
        ]);
    }
}
