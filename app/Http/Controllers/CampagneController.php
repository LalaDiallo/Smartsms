<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branding;
use App\Models\SenderName;
use App\Models\Groupe;
use App\Models\Contacts;
use App\Models\Messages;
use App\Models\Campagnes;
use App\Models\Subscription;
use App\Models\SpamViolation;
use App\Models\DeviceToken;
use App\Services\PushService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use App\Mail\CampaignRejectionEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendCampaignMessagesJob;
use App\Mail\CampaignApprovalRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CampagneController extends Controller
{
    // Rôles autorisés à envoyer directement sans approbation
    private const DIRECT_SEND_ROLES = ['super_admin', 'admin', 'responsable_regional'];

    // Rôles qui doivent passer par l'approbation
    private const APPROVAL_ROLES = ['manager', 'operator'];

    public function index(Request $request)
    {
        $client = auth()->user()->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $query = Campagnes::with(['messages'])
            ->where('client_id', $client->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('archived') !== null) {
            $query->where('archived', $request->boolean('archived', false));
        }

        $campagnes = $query
            ->withCount('messages')
            ->withCount(['messages as messages_delivered' => fn ($q) => $q->where('status', 'delivered')])
            ->withCount(['messages as messages_failed'    => fn ($q) => $q->where('status', 'failed')])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $campagnes,
            'message' => 'Campagnes récupérées avec succès',
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // ── Vérification du plan pour les canaux premium ──────────────────
            $channel = $request->input('type', 'sms');
            if (in_array($channel, ['whatsapp', 'email', 'push'])) {
                $subscription = Subscription::where('client_id', $user->client_id)
                    ->where('status', 'active')
                    ->with('plan')
                    ->latest()
                    ->first();

                $planSlug = $subscription?->plan?->slug ?? 'freemium';
                $planLevel = ['freemium' => 0, 'starter' => 1, 'pro' => 2, 'enterprise' => 3][$planSlug] ?? 0;

                if ($planLevel < 2) { // Pro requis
                    $channelNames = ['whatsapp' => 'WhatsApp', 'email' => 'Email', 'push' => 'Push'];
                    return response()->json([
                        'error'         => 'plan_required',
                        'message'       => "Le canal {$channelNames[$channel]} est disponible à partir du plan Pro (1 200 000 GNF/mois).",
                        'required_plan' => 'pro',
                        'current_plan'  => $planSlug,
                        'upgrade_url'   => '/subscriptions',
                    ], 403);
                }
            }
            // ─────────────────────────────────────────────────────────────────

            $validatedData = $this->validateCampaignRequest($request);

            $spamInfo = $validatedData['_spam'] ?? null;
            unset($validatedData['_spam']);

            $requestedStatus = $validatedData['status'] ?? null;

            // Anti-spam : quarantaine → forcer en attente d'approbation même pour les rôles à envoi direct
            $quarantined = !empty($spamInfo['violations']) && collect($spamInfo['violations'])
                ->contains(fn($v) => ($v->action ?? '') === 'quarantine');

            if ($quarantined && $requestedStatus !== 'brouillon') {
                $requestedStatus = 'attente';
            }

            // Cas brouillon : sauvegarde rapide sans messages ni envoi
            if ($requestedStatus === 'brouillon') {
                $campaign = Campagnes::create([
                    'user_id'    => $user->id,
                    'client_id'  => $user->client_id,
                    'name'       => $validatedData['name'],
                    'status'     => 'brouillon',
                    'start_date' => Carbon::now(),
                    'end_date'   => Carbon::now()->addDay(),
                    'region'     => $validatedData['region'],
                    'channel'    => $validatedData['type'],
                    'settings'   => [
                        'timezone' => $validatedData['settings']['timezone'] ?? 'UTC',
                        'message'  => [
                            'subject' => $validatedData['message']['subject'] ?? null,
                            'content' => $validatedData['message']['content'] ?? null,
                            'cta'     => $validatedData['message']['cta'] ?? null,
                            'cta_url' => $validatedData['message']['cta_url'] ?? null,
                        ],
                    ],
                ]);

                ActivityLogger::log('campaign.created', ['name' => $campaign->name, 'status' => 'brouillon'], 'campaign', $campaign->id);

                return response()->json([
                    'message' => 'Brouillon sauvegardé avec succès',
                    'spam'    => $this->formatSpamResponse($spamInfo),
                    'data'    => ['campaign' => $campaign],
                ], 201);
            }

            // Résoudre les contacts pour les autres cas
            $contacts = $this->resolveAudienceContacts($request, $validatedData);

            if (!$contacts || $contacts->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucun contact trouvé pour l\'audience sélectionnée.',
                ], 400);
            }

            // Cas attente (partager brouillon depuis un rôle direct) : messages créés, pas d'envoi immédiat
            if ($requestedStatus === 'attente' && in_array($user->role, self::DIRECT_SEND_ROLES)) {
                $campaign = DB::transaction(function () use ($validatedData, $user, $contacts) {
                    $c = $this->createNewCampaignWithStatus($validatedData, $user, 'attente');
                    $this->createMessagesForCampaign($c, $contacts, $validatedData);
                    return $c;
                });

                $responsable = User::where('client_id', $user->client_id)
                    ->where('role', 'admin')
                    ->first();

                if ($responsable) {
                    Mail::to($responsable->email)
                        ->send(new CampaignApprovalRequest($user, $campaign));
                }

                ActivityLogger::log('campaign.created', ['name' => $campaign->name, 'status' => 'attente'], 'campaign', $campaign->id);

                return response()->json([
                    'message' => 'Brouillon partagé avec l\'équipe pour approbation',
                    'spam'    => $this->formatSpamResponse($spamInfo),
                    'data'    => ['campaign' => $campaign, 'contact_count' => $contacts->count()],
                ], 201);
            }

            // Cas 1 : Envoi direct (admin / responsable régional)
            if (in_array($user->role, self::DIRECT_SEND_ROLES)) {
                $campaign = DB::transaction(function () use ($validatedData, $user, $contacts) {
                    $c = $this->createNewCampaign($validatedData, $user, 'direct');
                    $this->createMessagesForCampaign($c, $contacts, $validatedData);
                    return $c;
                });
                $this->scheduleCampaign($campaign, $validatedData);

                $message = $validatedData['schedule'] === 'later'
                    ? "Campagne programmée pour le {$validatedData['scheduled_date']} à {$validatedData['scheduled_time']}"
                    : 'Campagne lancée avec succès';

                ActivityLogger::log('campaign.launched', ['name' => $campaign->name], 'campaign', $campaign->id);

                return response()->json([
                    'message' => $message,
                    'spam' => $this->formatSpamResponse($spamInfo),
                    'data' => [
                        'campaign' => $campaign,
                        'contact_count' => $contacts->count(),
                    ],
                ], 201);
            }

            // Cas 2 : Demande d'approbation (manager / opérateur)
            if (in_array($user->role, self::APPROVAL_ROLES)) {
                $campaign = DB::transaction(function () use ($validatedData, $user, $contacts) {
                    $c = $this->createNewCampaign($validatedData, $user, 'approval');
                    $this->createMessagesForCampaign($c, $contacts, $validatedData);
                    return $c;
                });

                $responsable = User::where('client_id', $user->client_id)
                    ->where('role', 'admin')
                    ->first();

                if ($responsable) {
                    Mail::to($responsable->email)
                        ->send(new CampaignApprovalRequest($user, $campaign));
                }

                ActivityLogger::log('campaign.created', ['name' => $campaign->name, 'status' => 'approbation'], 'campaign', $campaign->id);

                return response()->json([
                    'message' => 'Demande d\'approbation envoyée au responsable',
                    'spam' => $this->formatSpamResponse($spamInfo),
                    'data' => ['campaign' => $campaign],
                ], 201);
            }

            return response()->json([
                'error' => 'Vous n\'êtes pas autorisé à créer une campagne',
            ], 403);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création campagne', [
                'error'     => $e->getMessage(),
                'user_id'   => Auth::id(),
                'client_id' => Auth::user()?->client_id,
                'trace'     => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Échec lors de la création de la campagne',
            ], 500);
        }
    }

    protected function formatSpamResponse(?array $spamInfo): ?array
    {
        if (!$spamInfo || empty($spamInfo['violations'])) {
            return null;
        }

        $highest = collect($spamInfo['violations'])
            ->sortByDesc('score')
            ->first();

        if (!$highest) {
            return null;
        }

        return [
            'level' => match($highest->action) {
                'warn'       => 'warn',
                'flag'       => 'flag',
                'quarantine' => 'quarantine',
                'block'      => 'block',
                default      => 'flag',
            },
            'severity'    => $highest->severity,
            'reason'      => $highest->reason,
            'score'       => $spamInfo['score'] ?? 0,
            'quarantined' => collect($spamInfo['violations'])->contains(fn($v) => ($v->action ?? '') === 'quarantine'),
        ];
    }

    protected function validateCampaignRequest(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name'                           => 'required|string|max:255',
            'region'                         => 'required|string|max:255',
            'type'                           => 'required|in:sms,whatsapp,email,push',
            'status'                         => 'nullable|in:brouillon,programmer,attente,terminer,rejeter',
            'audience'                       => 'required|in:all,employees,group,contacts,import',
            'schedule'                       => 'required|in:now,later',
            'scheduled_date'                 => 'nullable|required_if:schedule,later|date_format:Y-m-d|after_or_equal:today',
            'scheduled_time'                 => 'nullable|required_if:schedule,later|date_format:H:i',
            'message.subject'                => 'nullable|required_if:type,email,push|string|max:255',
            'message.content'                => 'required|string|max:' . ($request->input('type') === 'sms' ? 160 : 65535),
            'message.media'                  => 'nullable|string|max:255',
            'message.cta'                    => 'nullable|string|max:100',
            'message.cta_url'               => 'nullable|url|max:255',
            'message.use_default_reply_url' => 'required|boolean',
            'message.reply_url'             => [
                'nullable',
                'url',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$request->input('message.use_default_reply_url') && empty($value)) {
                        $fail('Une URL de réponse est requise lorsque le lien par défaut est désactivé.');
                    }
                },
            ],
            'settings.timezone'              => 'required|string|timezone',
            'selected_contacts'              => 'nullable|required_if:audience,contacts|array',
            'selected_contacts.*'           => [
                'nullable',
                Rule::exists('contacts', 'id')->where('client_id', auth()->user()->client_id),
            ],
            'groupId'                        => [
                'nullable',
                'required_if:audience,group',
                Rule::exists('groupes', 'id')->where('client_id', auth()->user()->client_id),
            ],
            'file'                           => 'nullable|required_if:audience,import|file|mimes:csv,txt',
            'includeFullName'               => 'nullable|boolean',
            'column_mapping'                => 'required_if:audience,import|json',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $spamResult = analyze_campaign_spam(
            clientId: auth()->user()->client_id,
            content: $validated['message']['content'],
            channel: $validated['type']
        );

        return array_merge($validated, ['_spam' => $spamResult]);
    }

    // Résout les contacts depuis l'audience — utilise les données déjà validées sans rappeler validate
    protected function resolveAudienceContacts(Request $request, array $validatedData) // phpcs:ignore
    {
        $client = auth()->user()->client;

        if (!$client) {
            return collect([]);
        }

        $audience = $validatedData['audience'];
        $contactsQuery = Contacts::where('client_id', $client->id)
            ->where('status', '!=', 'NotInsert');

        switch ($audience) {
            case 'employees':
                $contactsQuery->where('status', 'employes');
                break;

            case 'group':
                $groupId = $validatedData['groupId'] ?? null;
                if (!$groupId) {
                    return collect([]);
                }
                $groupe = Groupe::where('client_id', $client->id)
                    ->with(['contacts', 'rules'])
                    ->find($groupId);
                if (!$groupe) {
                    throw new \Exception('Groupe introuvable ou accès non autorisé');
                }

                if ($groupe->type === 'dynamic') {
                    // Segment dynamique : résoudre les règles pour trouver les contacts
                    $allowed = ['region', 'status', 'preferred_channel', 'timezone', 'engagement_score', 'is_spammer'];
                    foreach ($groupe->rules as $rule) {
                        if (!in_array($rule->field, $allowed)) continue;
                        $op = strtoupper($rule->operator ?? '=');
                        if ($op === 'IN') {
                            $vals = is_string($rule->value) ? json_decode($rule->value, true) : (array) $rule->value;
                            $contactsQuery->whereIn($rule->field, $vals);
                        } elseif ($op === 'LIKE') {
                            $contactsQuery->where($rule->field, 'LIKE', '%' . $rule->value . '%');
                        } else {
                            $contactsQuery->where($rule->field, $op, $rule->value);
                        }
                    }
                } else {
                    // Segment statique ou groupe normal : contacts de la pivot
                    $contactsQuery->whereIn('id', $groupe->contacts->pluck('id'));
                }
                break;

            case 'contacts':
                $selectedContacts = $validatedData['selected_contacts'] ?? [];
                if (empty($selectedContacts)) {
                    return collect([]);
                }
                $contactsQuery->whereIn('id', $selectedContacts);
                break;

            case 'import':
                return $this->importContactsFromFile($validatedData);

            case 'all':
            default:
                break;
        }

        return $contactsQuery->get();
    }

    protected function importContactsFromFile(array $validatedData)
    {
        if (empty($validatedData['file'])) {
            return collect([]);
        }

        $file = $validatedData['file'];
        $mapping = json_decode($validatedData['column_mapping'], true);
        $clientId = auth()->user()->client_id;
        $contacts = collect();

        if (($handle = fopen($file->getRealPath(), 'r')) === false) {
            return collect([]);
        }

        $headers = fgetcsv($handle);
        $fieldIndices = [];

        foreach (['first_name', 'last_name', 'email', 'phone'] as $field) {
            if (!empty($mapping[$field])) {
                $index = array_search($mapping[$field], $headers);
                if ($index !== false) {
                    $fieldIndices[$field] = $index;
                }
            }
        }

        while (($row = fgetcsv($handle)) !== false) {
            $data = [
                'client_id'  => $clientId,
                'first_name' => null,
                'last_name'  => null,
                'email'      => null,
                'phone'      => null,
                'status'     => 'NotInsert',
                'source'     => 'campaign_import',
            ];

            foreach ($fieldIndices as $field => $index) {
                if (!empty($row[$index])) {
                    $data[$field] = trim($row[$index]);
                }
            }

            if (empty($data['phone']) && empty($data['email'])) {
                continue;
            }

            if (!empty($data['phone'])) {
                $data['phone'] = preg_replace('/\D/', '', $data['phone']);
                if (strlen($data['phone']) === 9) {
                    $data['phone'] = '224' . $data['phone'];
                }
            }

            $contact = Contacts::where('client_id', $clientId)
                ->where(function ($q) use ($data) {
                    if (!empty($data['phone'])) {
                        $q->orWhere('phone', $data['phone']);
                    }
                    if (!empty($data['email'])) {
                        $q->orWhere('email', $data['email']);
                    }
                })
                ->first();

            if (!$contact) {
                $contact = Contacts::create($data);
            }

            $contacts->push($contact);
        }

        fclose($handle);

        return $contacts;
    }

    public function contact()
    {
        $client = auth()->user()->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $totalContacts = Contacts::where('client_id', $client->id)
            ->where('status', '!=', 'NotInsert')
            ->count();

        $employeesCount = Contacts::where('client_id', $client->id)
            ->where('status', 'employes')
            ->count();

        $data = [
            [
                'id' => 'all',
                'name' => 'Tous les contacts',
                'count' => $totalContacts,
                'description' => 'Votre base complète',
                'engagement' => 78,
            ],
            [
                'id' => 'employees',
                'name' => 'Employés',
                'count' => $employeesCount,
                'description' => 'Tous vos employés',
                'engagement' => 90,
            ],
            [
                'id' => 'group',
                'name' => 'Sélectionner un groupe',
                'count' => 0,
                'description' => 'Choisissez parmi les groupes prédéfinis',
                'engagement' => 0,
            ],
            [
                'id' => 'contacts',
                'name' => 'Sélectionner les contacts',
                'count' => 0,
                'description' => 'Sélectionnez des contacts individuels',
                'engagement' => 0,
            ],
            [
                'id' => 'import',
                'name' => 'Importer',
                'count' => 0,
                'description' => 'Importer un fichier CSV depuis votre ordinateur',
                'engagement' => 0,
            ],
        ];

        return response()->json(['data' => $data]);
    }

    public function groupesEtContacts()
    {
        $client = auth()->user()->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $groupes = Groupe::with(['contacts' => function ($query) use ($client) {
            $query->where('contacts.client_id', $client->id)
                  ->where('contacts.status', '!=', 'NotInsert');
        }])
            ->where('client_id', $client->id)
            ->get();

        $contacts = Contacts::where('client_id', $client->id)
            ->where('status', '!=', 'NotInsert')
            ->get(['id', 'first_name', 'last_name', 'phone', 'email']);

        return response()->json([
            'groupes' => $groupes,
            'contacts' => $contacts,
        ]);
    }

    protected function createNewCampaign(array $validatedData, User $user, string $mode): Campagnes
    {
        if ($validatedData['schedule'] === 'now') {
            $endDate = Carbon::now();
            // Statut selon le mode : envoi direct ou approbation
            $status = $mode === 'direct' ? 'programmer' : 'attente';
        } else {
            $endDate = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validatedData['scheduled_date'] . ' ' . $validatedData['scheduled_time'],
                $validatedData['settings']['timezone']
            )->setTimezone('UTC');
            $status = $mode === 'direct' ? 'programmer' : 'attente';
        }

        return Campagnes::create([
            'user_id'    => $user->id,
            'client_id'  => $user->client_id,
            'name'       => $validatedData['name'],
            'status'     => $status,
            'start_date' => Carbon::now(),
            'end_date'   => $endDate,
            'region'     => $validatedData['region'],
            'channel'    => $validatedData['type'],
            'settings'   => ['timezone' => $validatedData['settings']['timezone'] ?? 'UTC'],
        ]);
    }

    protected function createNewCampaignWithStatus(array $validatedData, User $user, string $status): Campagnes
    {
        if ($validatedData['schedule'] === 'later') {
            $endDate = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validatedData['scheduled_date'] . ' ' . $validatedData['scheduled_time'],
                $validatedData['settings']['timezone']
            )->setTimezone('UTC');
        } else {
            $endDate = Carbon::now()->addDay();
        }

        return Campagnes::create([
            'user_id'    => $user->id,
            'client_id'  => $user->client_id,
            'name'       => $validatedData['name'],
            'status'     => $status,
            'start_date' => Carbon::now(),
            'end_date'   => $endDate,
            'region'     => $validatedData['region'],
            'channel'    => $validatedData['type'],
            'settings'   => ['timezone' => $validatedData['settings']['timezone'] ?? 'UTC'],
        ]);
    }

    protected function createMessagesForCampaign(Campagnes $campaign, \Illuminate\Support\Collection $contacts, array $validatedData): void
    {
        $senderName = SenderName::where('client_id', $campaign->client_id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->first();

        $messages = $contacts->map(function ($contact) use ($campaign, $validatedData, $senderName) {
            $content = $validatedData['message']['content'];

            if (!empty($validatedData['includeFullName'])) {
                $content = str_replace(
                    ['{first_name}', '{last_name}'],
                    [$contact->first_name ?? '', $contact->last_name ?? ''],
                    $content
                );
            }

            $useDefaultReply = $validatedData['message']['use_default_reply_url'] ?? true;
            $replyToken = null;
            $actionUrl = null;

            if ($useDefaultReply) {
                $replyToken = Str::uuid()->toString();
                $actionUrl  = url("/reply/{$replyToken}");
            } else {
                $actionUrl = $validatedData['message']['reply_url'] ?? null;
            }

            if (!empty($actionUrl)) {
                $content = rtrim($content) . "\n" . $actionUrl;
            }

            if ($senderName) {
                $content = rtrim($content) . "\n-- \n" . $senderName->name;
            }

            return [
                'campagnes_id' => $campaign->id,
                'contact_id'   => $contact->id,
                'content'      => $content,
                'channel'      => $validatedData['type'],
                'status'       => $validatedData['schedule'] === 'now' ? 'queued' : 'scheduled',
                'sent_at'      => $validatedData['schedule'] === 'now' ? now() : null,
                'subject'      => $validatedData['message']['subject'] ?? null,
                'media'        => $validatedData['message']['media'] ?? null,
                'cta'          => $validatedData['message']['cta'] ?? null,
                'cta_url'      => $actionUrl,
                'reply_token'  => $replyToken,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        })->toArray();

        // Insertion par lots pour éviter les problèmes de taille de paquet MySQL
        collect($messages)->chunk(500)->each(function ($chunk) {
            Messages::insert($chunk->toArray());
        });
    }

    protected function scheduleCampaign(Campagnes $campaign, array $validatedData): void
    {
        if ($validatedData['schedule'] === 'now') {
            // Exécution immédiate, pas besoin de queue worker
            SendCampaignMessagesJob::dispatchSync($campaign);
        } else {
            $scheduledAt = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validatedData['scheduled_date'] . ' ' . $validatedData['scheduled_time'],
                $validatedData['settings']['timezone']
            )->setTimezone('UTC');

            // Envoi différé — nécessite que php artisan queue:work soit actif
            SendCampaignMessagesJob::dispatch($campaign)->delay($scheduledAt);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();

            $campaign = Campagnes::where('id', $id)
                ->where('client_id', $user->client_id)
                ->firstOrFail();

            $name = $campaign->name;
            $campaign->delete();

            ActivityLogger::log('campaign.deleted', ['name' => $name], 'campaign', (int) $id);

            return response()->json(['message' => 'Campagne supprimée avec succès'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable ou accès non autorisé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur suppression campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:campagnes,id',
        ]);

        try {
            $clientId = auth()->user()->client_id;
            $ids = $request->input('ids');

            $validCount = Campagnes::whereIn('id', $ids)
                ->where('client_id', $clientId)
                ->count();

            if ($validCount !== count($ids)) {
                return response()->json(['message' => 'Certaines campagnes ne vous appartiennent pas'], 403);
            }

            DB::transaction(function () use ($ids) {
                Campagnes::whereIn('id', $ids)->delete();
            });

            return response()->json([
                'message' => count($ids) . ' campagne(s) supprimée(s) avec succès',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression groupée', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }

    public function statistics($id)
    {
        try {
            $user = Auth::user();

            $campaign = Campagnes::where('id', $id)
                ->where('client_id', $user->client_id)
                ->with(['messages'])
                ->firstOrFail();

            $messages   = $campaign->messages;
            $total      = $messages->count();
            $messageIds = $messages->pluck('id');

            $byStatus = $messages->groupBy('status')->map->count();

            $sent      = $byStatus->get('sent', 0) + $byStatus->get('delivered', 0);
            $delivered = $byStatus->get('delivered', 0);
            $failed    = $byStatus->get('failed', 0);
            $pending   = $byStatus->get('queued', 0)
                       + $byStatus->get('scheduled', 0)
                       + $byStatus->get('pending', 0);

            $deliveryRate = $total > 0 ? round(($delivered / $total) * 100, 1) : 0;
            $failRate     = $total > 0 ? round(($failed  / $total) * 100, 1) : 0;

            // ── Réponses réelles ──────────────────────────────────────────────
            $responsesCount = DB::table('responses')
                ->whereIn('message_id', $messageIds)
                ->count();

            $replyRate = $sent > 0 ? round(($responsesCount / $sent) * 100, 1) : 0;

            // Réponses par canal (jointure avec messages)
            $responsesByChannel = DB::table('responses')
                ->join('messages', 'responses.message_id', '=', 'messages.id')
                ->whereIn('responses.message_id', $messageIds)
                ->selectRaw('messages.channel, COUNT(responses.id) as total')
                ->groupBy('messages.channel')
                ->pluck('total', 'channel');

            // Réponses par jour
            $responsesByDay = DB::table('responses')
                ->whereIn('message_id', $messageIds)
                ->selectRaw('DATE(received_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');

            // Liste des réponses détaillées (50 dernières)
            $recentResponses = DB::table('responses')
                ->join('messages', 'responses.message_id', '=', 'messages.id')
                ->leftJoin('contacts', 'responses.contact_id', '=', 'contacts.id')
                ->whereIn('responses.message_id', $messageIds)
                ->selectRaw('
                    responses.id,
                    responses.content,
                    responses.received_at,
                    messages.channel,
                    contacts.first_name,
                    contacts.last_name,
                    contacts.phone,
                    contacts.email
                ')
                ->orderByDesc('responses.received_at')
                ->limit(50)
                ->get()
                ->map(fn ($r) => [
                    'id'          => $r->id,
                    'content'     => $r->content,
                    'received_at' => $r->received_at,
                    'channel'     => $r->channel,
                    'contact'     => [
                        'name'  => trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? '')) ?: null,
                        'phone' => $r->phone,
                        'email' => $r->email,
                    ],
                ]);

            // ── Stats par canal ───────────────────────────────────────────────
            $channelColors = [
                'sms'      => '#3B82F6',
                'email'    => '#10B981',
                'whatsapp' => '#25D366',
                'push'     => '#F59E0B',
            ];

            $byChannel = $messages->groupBy('channel')->map(function ($channelMsgs, $channel) use ($total, $channelColors, $responsesByChannel, $sent) {
                $st        = $channelMsgs->groupBy('status')->map->count();
                $chTotal   = $channelMsgs->count();
                $chSent    = $st->get('sent', 0) + $st->get('delivered', 0);
                $chFailed  = $st->get('failed', 0);
                $chPending = $st->get('queued', 0) + $st->get('scheduled', 0) + $st->get('pending', 0);
                $chReplies = (int) ($responsesByChannel[$channel] ?? 0);

                return [
                    'name'         => ucfirst($channel),
                    'channel'      => $channel,
                    'total'        => $chTotal,
                    'sent'         => $chSent,
                    'delivered'    => $st->get('delivered', 0),
                    'failed'       => $chFailed,
                    'pending'      => $chPending,
                    'responses'    => $chReplies,
                    'reply_rate'   => $chSent > 0 ? round(($chReplies / $chSent) * 100, 1) : 0,
                    'value'        => $total > 0 ? round(($chTotal / $total) * 100, 1) : 0,
                    'fail_rate'    => $chTotal > 0 ? round(($chFailed  / $chTotal) * 100, 1) : 0,
                    'color'        => $channelColors[$channel] ?? '#6B7280',
                ];
            })->values();

            // ── Évolution quotidienne ─────────────────────────────────────────
            $trend = Messages::where('campagnes_id', $campaign->id)
                ->whereNotNull('sent_at')
                ->selectRaw('DATE(sent_at) as date, channel, COUNT(*) as total,
                    SUM(status = "delivered") as delivered,
                    SUM(status = "sent")      as sent,
                    SUM(status = "failed")    as failed,
                    SUM(status = "pending")   as pending')
                ->groupBy('date', 'channel')
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(fn ($rows, $date) => [
                    'date'      => $date,
                    'total'     => $rows->sum('total'),
                    'sent'      => $rows->sum('sent'),
                    'delivered' => $rows->sum('delivered'),
                    'failed'    => $rows->sum('failed'),
                    'pending'   => $rows->sum('pending'),
                    'responses' => (int) ($responsesByDay[$date] ?? 0),
                    'channels'  => $rows->keyBy('channel')->map(fn ($r) => [
                        'sent'      => (int) $r->sent,
                        'delivered' => (int) $r->delivered,
                        'failed'    => (int) $r->failed,
                        'pending'   => (int) $r->pending,
                        'total'     => (int) $r->total,
                    ]),
                ])
                ->values();

            // ── Données de coût ───────────────────────────────────────────────
            $subscription = Subscription::where('client_id', $user->client_id)
                ->where('status', 'active')
                ->with('plan')
                ->latest()
                ->first();

            // Prix de référence par SMS (overuse ou plan de base)
            $pricePerSms = 0;
            if ($subscription) {
                $pricePerSms = (float) ($subscription->plan?->sms_price_reference
                    ?? $subscription->plan?->price_monthly_base / max(1, $subscription->plan?->sms_included_monthly ?? 1)
                    ?? 120);
            }
            if ($pricePerSms <= 0) $pricePerSms = 120; // fallback Freemium

            $totalCost       = round($sent * $pricePerSms);
            $costPerDelivered = $delivered > 0 ? round($totalCost / $delivered, 1) : 0;
            $costPerResponse  = $responsesCount > 0 ? round($totalCost / $responsesCount, 1) : 0;
            $roi              = $totalCost > 0 && $responsesCount > 0
                ? round(($responsesCount / $totalCost) * 1000, 2) // réponses pour 1000 GNF
                : 0;

            $cost = [
                'total'           => $totalCost,
                'per_sms'         => $pricePerSms,
                'per_delivered'   => $costPerDelivered,
                'per_response'    => $costPerResponse,
                'roi_per_1000gnf' => $roi,
                'currency'        => 'GNF',
                'plan_name'       => $subscription?->plan?->name ?? 'Freemium',
            ];

            return response()->json([
                'message' => 'Statistiques récupérées avec succès',
                'data' => [
                    'summary' => [
                        'total'         => $total,
                        'sent'          => $sent,
                        'delivered'     => $delivered,
                        'failed'        => $failed,
                        'pending'       => $pending,
                        'responses'     => $responsesCount,
                        'delivery_rate' => $deliveryRate,
                        'fail_rate'     => $failRate,
                        'reply_rate'    => $replyRate,
                    ],
                    'by_status'        => $byStatus,
                    'by_channel'       => $byChannel,
                    'performance'      => $trend,
                    'recent_responses' => $recentResponses,
                    'channel'          => $byChannel,
                    'audience'         => [],
                    'cost'             => $cost,
                ],
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable ou accès non autorisé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur stats campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    public function globalAnalytics(Request $request)
    {
        try {
            $client = auth()->user()->client;

            if (!$client) {
                return response()->json(['message' => 'Client introuvable'], 404);
            }

            $days      = max(1, (int) $request->query('days', 30));
            $cacheKey  = "analytics:{$client->id}:days:{$days}";
            $startDate = Carbon::now()->subDays($days)->startOfDay();

            // Cache 3 minutes — helper global, fonctionne sans facade
            try {
                $cached = cache()->get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }
            } catch (\Throwable $ce) {
                // cache indisponible — on continue sans cache
            }

            $campaignIds = Campagnes::where('client_id', $client->id)->pluck('id');

            // ── KPIs campagnes ────────────────────────────────────────────────
            $totalCampaigns    = $campaignIds->count();
            $activeCampaigns   = Campagnes::where('client_id', $client->id)
                ->whereIn('status', ['programmer', 'attente'])->count();
            $finishedCampaigns = Campagnes::where('client_id', $client->id)
                ->where('status', 'terminer')->count();
            $draftCampaigns    = Campagnes::where('client_id', $client->id)
                ->where('status', 'brouillon')->count();

            $totalContacts = Contacts::where('client_id', $client->id)
                ->where('status', '!=', 'NotInsert')->count();

            // ── Stats globales messages ───────────────────────────────────────
            $msgStats = Messages::whereIn('campagnes_id', $campaignIds)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(status IN ("sent","delivered")) as sent,
                    SUM(status = "delivered") as delivered,
                    SUM(status = "failed") as failed,
                    SUM(status IN ("queued","scheduled","pending")) as pending
                ')
                ->first();

            $total     = (int) ($msgStats->total     ?? 0);
            $sent      = (int) ($msgStats->sent      ?? 0);
            $delivered = (int) ($msgStats->delivered ?? 0);
            $failed    = (int) ($msgStats->failed    ?? 0);
            $pending   = (int) ($msgStats->pending   ?? 0);

            $deliveryRate = $total > 0 ? round(($delivered / $total) * 100, 1) : 0;
            $failRate     = $total > 0 ? round(($failed    / $total) * 100, 1) : 0;

            $messageIds     = Messages::whereIn('campagnes_id', $campaignIds)->pluck('id');
            $totalResponses = DB::table('responses')->whereIn('message_id', $messageIds)->count();
            $replyRate      = $sent > 0 ? round(($totalResponses / $sent) * 100, 1) : 0;

            // ── Stats par canal ───────────────────────────────────────────────
            $channelColors = [
                'sms'      => '#3B82F6',
                'email'    => '#10B981',
                'whatsapp' => '#25D366',
                'push'     => '#F59E0B',
            ];

            $byChannel = Messages::whereIn('campagnes_id', $campaignIds)
                ->selectRaw('
                    channel,
                    COUNT(*) as total,
                    SUM(status IN ("sent","delivered")) as sent,
                    SUM(status = "delivered") as delivered,
                    SUM(status = "failed") as failed
                ')
                ->groupBy('channel')
                ->get()
                ->map(fn ($row) => [
                    'name'      => ucfirst($row->channel),
                    'channel'   => $row->channel,
                    'total'     => (int) $row->total,
                    'sent'      => (int) $row->sent,
                    'delivered' => (int) $row->delivered,
                    'failed'    => (int) $row->failed,
                    'value'     => $total > 0 ? round(((int) $row->total / $total) * 100, 1) : 0,
                    'color'     => $channelColors[$row->channel] ?? '#6B7280',
                ])->values();

            // ── Distribution par statut campagne ─────────────────────────────
            $byStatus = Campagnes::where('client_id', $client->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            // ── Évolution quotidienne (période sélectionnée) ──────────────────
            $trend = Messages::whereIn('campagnes_id', $campaignIds)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('
                    DATE(created_at) as date,
                    COUNT(*) as total,
                    SUM(status IN ("sent","delivered")) as sent,
                    SUM(status = "delivered") as delivered,
                    SUM(status = "failed") as failed
                ')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date'      => $row->date,
                    'total'     => (int) $row->total,
                    'sent'      => (int) $row->sent,
                    'delivered' => (int) $row->delivered,
                    'failed'    => (int) $row->failed,
                ])->values();

            // ── Données de facturation & coûts ───────────────────────────────
            $activeSubscription = Subscription::where('client_id', $client->id)
                ->where('status', 'active')
                ->with('plan:id,name,slug')
                ->latest()
                ->first();

            $totalSpent = (float) Subscription::where('client_id', $client->id)->sum('price');

            $spendingTrend = Subscription::where('client_id', $client->id)
                ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(price) as amount, COUNT(*) as cnt')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn ($row) => [
                    'month'  => $row->month,
                    'amount' => (float) $row->amount,
                    'count'  => (int) $row->cnt,
                ])->values();

            $smsQuota     = (int) ($activeSubscription?->sms_quota ?? 0);
            $smsUsed      = (int) ($activeSubscription?->sms_used  ?? 0);
            $smsRemaining = max(0, $smsQuota - $smsUsed);
            $costPerSms   = $sent > 0 && $totalSpent > 0 ? round($totalSpent / $sent, 1) : 0;

            $billing = [
                'total_spent'    => $totalSpent,
                'mrr'            => (float) ($activeSubscription?->price ?? 0),
                'current_plan'   => $activeSubscription?->plan?->name ?? 'Aucun abonnement',
                'plan_slug'      => $activeSubscription?->plan?->slug ?? null,
                'sms_quota'      => $smsQuota,
                'sms_used'       => $smsUsed,
                'sms_remaining'  => $smsRemaining,
                'cost_per_sms'   => $costPerSms,
                'currency'       => 'GNF',
                'sub_status'     => $activeSubscription?->status ?? null,
                'sub_end_date'   => $activeSubscription?->end_date?->toDateString(),
                'spending_trend' => $spendingTrend,
            ];

            // ── Top 10 campagnes ──────────────────────────────────────────────
            $topCampaigns = Campagnes::where('client_id', $client->id)
                ->withCount('messages')
                ->withCount(['messages as messages_delivered' => fn ($q) => $q->where('status', 'delivered')])
                ->withCount(['messages as messages_failed'    => fn ($q) => $q->where('status', 'failed')])
                ->orderByDesc('messages_count')
                ->limit(10)
                ->get(['id', 'name', 'status', 'channel', 'created_at'])
                ->map(fn ($c) => [
                    'id'            => $c->id,
                    'name'          => $c->name,
                    'status'        => $c->status,
                    'channel'       => $c->channel,
                    'created_at'    => $c->created_at,
                    'total'         => $c->messages_count,
                    'delivered'     => $c->messages_delivered,
                    'failed'        => $c->messages_failed,
                    'delivery_rate' => $c->messages_count > 0
                        ? round(($c->messages_delivered / $c->messages_count) * 100, 1)
                        : 0,
                ]);

            $payload = [
                'summary' => [
                    'total_campaigns'    => $totalCampaigns,
                    'active_campaigns'   => $activeCampaigns,
                    'finished_campaigns' => $finishedCampaigns,
                    'draft_campaigns'    => $draftCampaigns,
                    'total_contacts'     => $totalContacts,
                    'total_messages'     => $total,
                    'sent'               => $sent,
                    'delivered'          => $delivered,
                    'failed'             => $failed,
                    'pending'            => $pending,
                    'responses'          => $totalResponses,
                    'delivery_rate'      => $deliveryRate,
                    'fail_rate'          => $failRate,
                    'reply_rate'         => $replyRate,
                ],
                'by_channel'    => $byChannel,
                'by_status'     => $byStatus,
                'trend'         => $trend,
                'top_campaigns' => $topCampaigns,
                'billing'       => $billing,
            ];

            try {
                cache()->put($cacheKey, $payload, now()->addMinutes(3));
            } catch (\Throwable $ce) {
                // cache indisponible — on continue sans cache
            }

            return response()->json($payload);

        } catch (\Throwable $e) {
            Log::error('Erreur analytics global', [
                'error'     => $e->getMessage(),
                'client_id' => auth()->user()?->client_id,
            ]);
            return response()->json(['message' => 'Erreur lors du chargement des analyses'], 500);
        }
    }

    public function Details(int $id)
    {
        try {
            $user = Auth::user();

            $campaign = Campagnes::where('id', $id)
                ->where('client_id', $user->client_id)
                ->firstOrFail();

            $messages = Messages::with('contact')
                ->where('campagnes_id', $campaign->id)
                ->paginate(50);

            $firstMessage = $messages->first();

            $contacts = $messages->getCollection()
                ->pluck('contact')
                ->filter()
                ->values();

            $spamViolations = SpamViolation::where('campaign_id', $campaign->id)
                ->orderByDesc('score')
                ->get(['action', 'severity', 'reason', 'score', 'created_at']);

            return response()->json([
                'status'          => 'success',
                'data'            => $campaign,
                'message'         => $firstMessage,
                'contacts'        => $contacts,
                'messages'        => $messages,
                'spam_violations' => $spamViolations,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable ou accès non autorisé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur détails campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la récupération de la campagne'], 500);
        }
    }

    public function archive($id)
    {
        $user = Auth::user();
        $campagne = Campagnes::where('id', $id)
            ->where('client_id', $user->client_id)
            ->find($id);

        if (!$campagne) {
            return response()->json(['error' => 'Campagne introuvable ou accès non autorisé'], 404);
        }

        if ($campagne->archived) {
            return response()->json(['error' => 'Cette campagne est déjà archivée'], 400);
        }

        $campagne->update(['archived' => true]);

        return response()->json([
            'success'  => true,
            'message'  => 'Campagne archivée avec succès',
            'campagne' => $campagne,
        ]);
    }

    public function DeArchive($id)
    {
        $user = Auth::user();
        $campagne = Campagnes::where('id', $id)
            ->where('client_id', $user->client_id)
            ->find($id);

        if (!$campagne) {
            return response()->json(['error' => 'Campagne introuvable ou accès non autorisé'], 404);
        }

        if (!$campagne->archived) {
            return response()->json(['error' => 'Cette campagne n\'est pas archivée'], 400);
        }

        $campagne->update(['archived' => false]);

        return response()->json([
            'success'  => true,
            'message'  => 'Campagne désarchivée avec succès',
            'campagne' => $campagne,
        ]);
    }

    public function reject(Request $request, $id)
    {
        try {
            $campaign = Campagnes::where('id', $id)
                ->where('client_id', auth()->user()->client_id)
                ->firstOrFail();

            $campaign->update(['status' => 'rejeter']);
            ActivityLogger::log('campaign.rejected', ['name' => $campaign->name], 'campaign', (int) $id);

            $reason = $request->input('reason', 'Non-conformité avec nos directives');

            // Notifier le créateur de la campagne
            $creator = $campaign->user;
            if ($creator) {
                Mail::to($creator->email)->send(new CampaignRejectionEmail(
                    $campaign->name,
                    $creator->name,
                    $reason,
                    auth()->user()->name,
                    ucfirst(auth()->user()->role),
                    $creator->client->company_name ?? 'SmartSMS',
                    config('mail.from.address')
                ));
            }

            return response()->json([
                'message' => 'Campagne rejetée avec succès',
                'data'    => $campaign,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur rejet campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du rejet de la campagne'], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $campaign = Campagnes::where('id', $id)
                ->where('client_id', auth()->user()->client_id)
                ->firstOrFail();

            if ($campaign->status !== 'attente') {
                return response()->json(['error' => 'Seules les campagnes en attente peuvent être approuvées'], 400);
            }

            $campaign->update(['status' => 'programmer']);

            $this->scheduleCampaign($campaign, [
                'schedule'       => 'now',
                'scheduled_date' => null,
                'scheduled_time' => null,
                'settings'       => ['timezone' => data_get($campaign->settings, 'timezone', 'UTC')],
            ]);

            ActivityLogger::log('campaign.approved', ['name' => $campaign->name], 'campaign', (int) $id);

            return response()->json([
                'message' => 'Campagne approuvée et planifiée',
                'data'    => $campaign,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur approbation campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de l\'approbation'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $campaign = Campagnes::where('id', $id)
                ->where('client_id', $user->client_id)
                ->firstOrFail();

            if ($campaign->status === 'terminer') {
                return response()->json(['error' => 'Une campagne terminée ne peut pas être modifiée'], 403);
            }

            $validatedData = $this->validateCampaignUpdate($request);

            $endDate = $validatedData['schedule'] === 'now'
                ? Carbon::now()
                : Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $validatedData['scheduled_date'] . ' ' . $validatedData['scheduled_time'],
                    $validatedData['settings']['timezone']
                )->setTimezone('UTC');

            $campaign->update([
                'name'     => $validatedData['name'] ?? $campaign->name,
                'region'   => $validatedData['region'] ?? $campaign->region,
                'settings' => ['timezone' => $validatedData['settings']['timezone']],
                'end_date' => $endDate,
            ]);

            $useDefaultReply = (bool) ($validatedData['message']['use_default_reply_url'] ?? true);
            $customReplyUrl  = $validatedData['message']['reply_url'] ?? null;
            $baseContent     = rtrim($validatedData['message']['content'] ?? '');

            $senderName = SenderName::where('client_id', $campaign->client_id)
                ->where('is_active', true)
                ->where('status', 'approved')
                ->latest()
                ->first();

            $signature = $senderName ? "\n-- \n" . $senderName->name : '';

            $messageUpdate = [
                'subject' => $validatedData['message']['subject'] ?? null,
                'media'   => $validatedData['message']['media'] ?? null,
                'cta'     => $validatedData['message']['cta'] ?? null,
            ];

            if ($useDefaultReply) {
                $messageUpdate['content'] = $baseContent . $signature;
                Messages::where('campagnes_id', $campaign->id)->update($messageUpdate);
                DB::statement(
                    "UPDATE messages SET content = CONCAT(TRIM(content), '\n', cta_url) WHERE campagnes_id = ? AND cta_url IS NOT NULL",
                    [$campaign->id]
                );
            } else {
                $contentWithUrl = !empty($customReplyUrl) ? $baseContent . "\n" . $customReplyUrl : $baseContent;
                $messageUpdate['content']     = $contentWithUrl . $signature;
                $messageUpdate['cta_url']     = $customReplyUrl;
                $messageUpdate['reply_token'] = null;
                Messages::where('campagnes_id', $campaign->id)->update($messageUpdate);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Campagne mise à jour avec succès',
                'data'    => ['campaign' => $campaign],
            ]);

        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    protected function validateCampaignUpdate(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'sometimes|string|max:255',
            'region'             => 'sometimes|string|max:255',
            'settings.timezone'  => 'required|string|timezone',
            'message.subject'              => 'nullable|string|max:255',
            'message.content'              => 'required|string|max:65535',
            'message.cta'                  => 'nullable|string|max:100',
            'message.cta_url'              => 'nullable|url|max:255',
            'message.use_default_reply_url'=> 'required|boolean',
            'message.reply_url'            => 'nullable|url|max:255',
            'schedule'          => 'required|in:now,later',
            'scheduled_date'    => 'nullable|required_if:schedule,later|date_format:Y-m-d|after_or_equal:today',
            'scheduled_time'    => 'nullable|required_if:schedule,later|date_format:H:i',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function launchCampaign($id)
    {
        try {
            $user = Auth::user();
            $campaign = Campagnes::where('id', $id)
                ->where('client_id', $user->client_id)
                ->firstOrFail();

            if ($campaign->status === 'terminer') {
                return response()->json(['error' => 'Cette campagne est déjà terminée'], 403);
            }

            $hasMessages = Messages::where('campagnes_id', $campaign->id)->exists();

            // ── Push sans messages (brouillon) : envoi direct via PushService ─
            if (!$hasMessages && $campaign->channel === 'push') {
                $settings   = $campaign->settings ?? [];
                $msgContent = data_get($settings, 'message.content') ?? $campaign->name;
                $msgSubject = data_get($settings, 'message.subject') ?? $campaign->name;

                $tokens = DeviceToken::where('client_id', $campaign->client_id)
                    ->pluck('token')
                    ->toArray();

                if (empty($tokens)) {
                    return response()->json(['error' => 'Aucun appareil enregistré pour cette campagne push.'], 400);
                }

                try {
                    (new PushService())->sendToTokens(
                        $tokens,
                        $msgSubject,
                        $msgContent,
                        ['campaign_id' => (string) $campaign->id],
                    );
                } catch (\Throwable $e) {
                    Log::error('Push broadcast échoué (brouillon)', ['campaign_id' => $campaign->id, 'error' => $e->getMessage()]);
                    return response()->json(['message' => 'Erreur lors de l\'envoi push : ' . $e->getMessage()], 500);
                }

                $campaign->update(['status' => 'terminer']);
                ActivityLogger::log('campaign.launched', ['name' => $campaign->name], 'campaign', (int) $id);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Campagne push lancée avec succès',
                    'data'    => ['campaign' => $campaign],
                ]);
            }
            // ─────────────────────────────────────────────────────────────────

            if (!$hasMessages) {
                return response()->json(['error' => 'Aucun message associé à cette campagne'], 400);
            }

            // ── Vérification du quota SMS avant envoi ────────────────────────
            $messageCount = Messages::where('campagnes_id', $campaign->id)
                ->whereIn('status', ['en_file', 'programme'])
                ->count();

            $subscription = Subscription::where('client_id', $user->client_id)
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->where('end_date', '>', now())
                ->latest()
                ->first();

            if (!$subscription || $subscription->sms_quota === 0) {
                return response()->json([
                    'error' => 'Vous n\'avez aucun crédit SMS disponible. Rechargez votre compte dans la section Abonnements.',
                ], 402);
            }

            $available = $subscription->sms_quota - $subscription->sms_used;

            if ($available <= 0) {
                return response()->json([
                    'error' => 'Votre quota SMS est épuisé (0 SMS restants). Rechargez votre compte pour envoyer des campagnes.',
                ], 402);
            }

            if ($messageCount > 0 && !$subscription->hasSmsAvailable($messageCount)) {
                return response()->json([
                    'error' => "Quota insuffisant : {$available} SMS disponibles, {$messageCount} nécessaires pour cette campagne.",
                ], 402);
            }
            // ────────────────────────────────────────────────────────────────

            SendCampaignMessagesJob::dispatchSync($campaign);
            $campaign->update(['status' => 'programmer']);

            ActivityLogger::log('campaign.launched', ['name' => $campaign->name], 'campaign', (int) $id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Campagne lancée avec succès',
                'data'    => ['campaign' => $campaign],
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campagne introuvable'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lancement campagne', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur lors du lancement'], 500);
        }
    }
}
