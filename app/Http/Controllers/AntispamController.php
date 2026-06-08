<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Patterns;
use App\Models\Frequence;
use App\Models\SpamRules;
use App\Models\SpamReports;
use Illuminate\Support\Str;
use App\Models\contentRules;
use App\Models\SpamKeywords;
use Illuminate\Http\Request;
use App\Models\senderDomains;
use Illuminate\Validation\Rule;
use App\Models\ComplianceChecks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AntiSpamController extends Controller
{
    // ===================================================================
    // RÈGLES ANTI-SPAM
    // ===================================================================

    /**
     * Liste toutes les règles avec leurs relations
     */
    public function rules(Request $request)
    {
        $query = SpamRules::query()
            ->with(['keywords', 'patterns', 'frequence', 'channels']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom_regle', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('condition', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par sévérité
        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $rules = $query->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $rules->map(function ($rule) {
                return [
                    'id' => $rule->id,
                    'nom_regle' => $rule->nom_regle,
                    'type' => $rule->type,
                    'condition' => $rule->condition,
                    'action' => $rule->action,
                    'status' => $rule->status,
                    'matches' => $rule->matches,
                    'severity' => $rule->severity,
                    'description' => $rule->description,
                    'auto_learn' => $rule->auto_learn,
                    'created_at' => $rule->created_at->toDateString(),
                    'updated_at' => $rule->updated_at->toDateString(),
                    // Relations
                    'keywords' => $rule->keywords->pluck('keyword')->toArray(),
                    'patterns' => $rule->patterns->pluck('pattern')->toArray(),
                    'frequency' => $rule->frequence ? [
                        'limit' => $rule->frequence->frequency_limit,
                        'period' => $rule->frequence->frequency_period,
                    ] : null,
                    'channels' => $rule->channels->pluck('channel')->toArray(),
                ];
            }),
        ]);
    }

    /**
     * Détail d'une règle
     */
    public function showRule(int $id)
    {
        $rule = SpamRules::with(['keywords', 'patterns', 'frequence', 'channels'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rule->id,
                'name' => $rule->nom_regle,
                'type' => $rule->type,
                'condition' => $rule->condition,
                'action' => $rule->action,
                'status' => $rule->status,
                'severity' => $rule->severity,
                'description' => $rule->description,
                'auto_learn' => $rule->auto_learn,
                'channels' => $rule->channels->pluck('channel')->toArray(),
                'keywords' => $rule->keywords->pluck('keyword')->toArray(),
                'patterns' => $rule->patterns->pluck('pattern')->toArray(),
                'frequencyLimit' => $rule->frequence?->frequency_limit,
                'frequencyPeriod' => $rule->frequence?->frequency_period,
            ]
        ]);
    }

    /**
     * Créer une nouvelle règle
     */
    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'nom_regle' => 'required|string|max:255',
            'type' => 'required|in:keyword,pattern,frequency,sender,content',
            'action' => 'required|in:block,quarantine,flag,review',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:sms,email,whatsapp,push',
            'keywords' => 'array|required_if:type,keyword',
            'patterns' => 'array|required_if:type,pattern',
            'frequency_limit' => 'integer|required_if:type,frequency|min:1',
            'frequency_period' => 'in:minute,hour,day|required_if:type,frequency',
            'sender_domains' => 'array|required_if:type,sender',
            'content_rules' => 'array|required_if:type,content',
            'isActive' => 'boolean',
            'autoLearn' => 'boolean',
            'notifications' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, &$rule) {
            $rule = SpamRules::create([
                'nom_regle' => $validated['nom_regle'],
                'type' => $validated['type'],
                'action' => $validated['action'],
                'severity' => $validated['severity'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['isActive'] ?? true ? 'active' : 'inactive',
                'auto_learn' => $validated['autoLearn'] ?? false,
            ]);

            // Channels
            foreach ($validated['channels'] as $channel) {
                Channel::create([
                    'spam_rule_id' => $rule->id,
                    'channel' => $channel,
                ]);
            }

            // Selon le type
            switch ($validated['type']) {
                case 'keyword':
                    foreach ($validated['keywords'] as $keyword) {
                        $kw = strtoupper(trim($keyword));
                        if ($kw === '') continue;
                        SpamKeywords::firstOrCreate([
                            'spam_rule_id' => $rule->id,
                            'keyword'      => $kw,
                        ]);
                    }
                    break;

                case 'pattern':
                    foreach ($validated['patterns'] as $pattern) {
                        Patterns::create([
                            'spam_rule_id' => $rule->id,
                            'pattern' => trim($pattern),
                        ]);
                    }
                    break;

                case 'frequency':
                    Frequence::create([
                        'spam_rule_id' => $rule->id,
                        'frequency_limit' => $validated['frequency_limit'],
                        'frequency_period' => $validated['frequency_period'],
                    ]);
                    break;
                case 'sender':
                    foreach ($validated['sender_domains'] as $domain) {
                        $domain = trim(strtolower($domain));
                        if ($domain) {
                            senderDomains::create([
                                'spam_rule_id' => $rule->id,
                                'domain' => $domain,
                            ]);
                        }
                    }
                    break;

                case 'content':
                    foreach ($validated['content_rules'] as $contentRule) {
                        $contentRule = trim($contentRule);
                        if ($contentRule) {
                            ContentRules::create([
                                'spam_rule_id' => $rule->id,
                                'rule' => $contentRule,
                            ]);
                        }
                    }
                    break;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Règle créée avec succès',
            'data' => $rule
        ], 201);
    }

    /**
     * Mettre à jour une règle
     */
    public function updateRule(Request $request, int $id)
    {
        $rule = SpamRules::findOrFail($id);

        $validated = $request->validate([
            'nom_regle' => 'required|string|max:255',
            'type' => 'required|in:keyword,pattern,frequency,sender,content',
            'action' => 'required|in:block,quarantine,flag,review',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'nullable|string',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:sms,email,whatsapp,push',
            'keywords' => 'array|required_if:type,keyword',
            'patterns' => 'array|required_if:type,pattern',
            'frequency_limit' => 'integer|required_if:type,frequency|min:1',
            'frequency_period' => 'in:minute,hour,day|required_if:type,frequency',
            'sender_domains' => 'array|required_if:type,sender',
            'content_rules' => 'array|required_if:type,content',
            'isActive' => 'boolean',
            'autoLearn' => 'boolean',
        ]);

        DB::transaction(function () use ($rule, $validated) {

            $rule->update([
                'nom_regle' => $validated['nom_regle'],
                'type' => $validated['type'],
                'action' => $validated['action'],
                'severity' => $validated['severity'],
                'description' => $validated['description'] ?? null,
                'status' => isset($validated['isActive']) && $validated['isActive']
                    ? 'active'
                    : 'inactive',
                'auto_learn' => $validated['autoLearn'] ?? false,
            ]);

            // 🔥 Nettoyage COMPLET
            $rule->channels()->delete();
            $rule->keywords()->delete();
            $rule->patterns()->delete();
            $rule->senderDomains()?->delete();
            $rule->contentRules()?->delete();
            if ($rule->frequence) {
                $rule->frequence->delete();
            }

            // Channels
            foreach ($validated['channels'] as $channel) {
                Channel::create([
                    'spam_rule_id' => $rule->id,
                    'channel' => $channel,
                ]);
            }

            // Recréation selon le type
            match ($validated['type']) {
                'keyword' => collect($validated['keywords'])->each(fn ($k) =>
                    SpamKeywords::firstOrCreate([
                        'spam_rule_id' => $rule->id,
                        'keyword'      => strtoupper(trim($k)),
                    ])
                ),

                'pattern' => collect($validated['patterns'])->each(fn ($p) =>
                    Patterns::create([
                        'spam_rule_id' => $rule->id,
                        'pattern' => trim($p),
                    ])
                ),

                'frequency' => Frequence::create([
                    'spam_rule_id' => $rule->id,
                    'frequency_limit' => $validated['frequency_limit'],
                    'frequency_period' => $validated['frequency_period'],
                ]),

                'sender' => collect($validated['sender_domains'])->each(fn ($d) =>
                    SenderDomains::create([
                        'spam_rule_id' => $rule->id,
                        'domain' => strtolower(trim($d)),
                    ])
                ),

                'content' => collect($validated['content_rules'])->each(fn ($r) =>
                    ContentRules::create([
                        'spam_rule_id' => $rule->id,
                        'rule' => trim($r),
                    ])
                ),
            };
        });

        return response()->json([
            'success' => true,
            'message' => 'Règle mise à jour',
            'data' => $rule->fresh([
                'keywords',
                'patterns',
                'frequence',
                'channels',
                'senderDomains',
                'contentRules'
            ])
        ]);
    }

    /**
     * Supprimer une règle
     */
    public function destroyRule(int $id)
    {
        $rule = SpamRules::findOrFail($id);
        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Règle supprimée'
        ]);
    }

    /**
     * Toggle statut actif/inactif
     */
    public function toggleRuleStatus(int $id)
    {
        $rule = SpamRules::findOrFail($id);
        $rule->update([
            'status' => $rule->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'data' => $rule
        ]);
    }

    // ===================================================================
    // RAPPORTS SPAM
    // ===================================================================

    /**
 * Liste paginée des rapports de spam
 * Supporte filtres par status et type + recherche simple
 */
public function reports(Request $request)
{
    $query = SpamReports::query()
        ->with(['rule' => function ($q) {
            $q->select('id', 'nom_regle', 'severity');
        }])
        ->select([
            'id',
            'type',
            'content',
            'sender',
            'recipient',
            'reason',
            'status',
            'risk_score',
            'timestamp',
            'rule_id',
        ]);

    // Filtre par statut
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Filtre par type de canal
    if ($request->filled('type') && $request->type !== 'all') {
        $query->where('type', $request->type);
    }

    // Recherche multi-champs
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function ($q) use ($search) {
            $q->where('sender', 'LIKE', "%{$search}%")
              ->orWhere('recipient', 'LIKE', "%{$search}%")
              ->orWhere('reason', 'LIKE', "%{$search}%")
              ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }

    // Tri par défaut : plus récent en premier
    $query->orderByDesc('timestamp');

    // Pagination (20 par page par défaut, configurable)
    $perPage = $request->input('per_page', 20);
    $reports = $query->paginate($perPage);

    return response()->json([
        'success' => true,
        'data' => $reports->items(),
        'pagination' => [
            'current_page' => $reports->currentPage(),
            'total' => $reports->total(),
            'per_page' => $reports->perPage(),
            'last_page' => $reports->lastPage(),
            'from' => $reports->firstItem(),
            'to' => $reports->lastItem(),
        ]
    ]);
}

    // ===================================================================
    // STATISTIQUES GLOBALES (vue d'ensemble)
    // ===================================================================

    public function stats()
    {
        $totalBlocked     = SpamReports::where('status', 'blocked')->count();
        $totalQuarantined = SpamReports::where('status', 'quarantined')->count();
        $totalFlagged     = SpamReports::where('status', 'flagged')->count();
        $activeRules      = SpamRules::where('status', 'active')->count();

        $avgRisk = SpamReports::avg('risk_score');

        $detectionsToday = SpamReports::whereDate('timestamp', today())->count();

        $lastReport = SpamReports::latest('timestamp')->value('timestamp');

        return response()->json([
            'success' => true,
            'data' => [
                'total_blocked'       => $totalBlocked,
                'total_quarantined'   => $totalQuarantined,
                'total_flagged'       => $totalFlagged,
                'active_rules'        => $activeRules,
                'average_risk_score'  => $avgRisk ? round($avgRisk) : 0,
                'detections_today'    => $detectionsToday,
                'last_updated'        => $lastReport,
            ]
        ]);
    }

    // ===================================================================
    // CONFORMITÉ
    // ===================================================================

    public function compliance()
    {
        $checks = ComplianceChecks::all();

        return response()->json([
            'success' => true,
            'data' => $checks
        ]);
    }

    public function config()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'rule_types' => [
                    ['id' => 'keyword',   'name' => 'Mots-clés',   'description' => 'Détection basée sur des mots ou phrases spécifiques',      'examples' => ['GRATUIT', 'URGENT', 'CLIQUEZ ICI', 'OFFRE LIMITÉE']],
                    ['id' => 'pattern',   'name' => 'Motifs',       'description' => 'Détection par expressions régulières et motifs',            'examples' => ['URLs raccourcies', 'Numéros de téléphone', 'Emails suspects']],
                    ['id' => 'frequency', 'name' => 'Fréquence',    'description' => 'Limitation du nombre de messages par période',              'examples' => ['5 messages/heure', '20 messages/jour', 'Burst detection']],
                    ['id' => 'sender',    'name' => 'Expéditeur',   'description' => 'Vérification de la réputation et authentification',          'examples' => ['Domaines blacklistés', 'SPF/DKIM manquant', 'Réputation IP']],
                    ['id' => 'content',   'name' => 'Contenu',      'description' => 'Analyse du contenu et de la structure du message',           'examples' => ['Ratio texte/liens', 'Caractères spéciaux', 'Encodage suspect']],
                ],
                'action_types' => [
                    ['id' => 'block',      'name' => 'Bloquer',     'description' => 'Empêcher complètement l\'envoi du message',                 'severity' => 'high'],
                    ['id' => 'quarantine', 'name' => 'Quarantaine', 'description' => 'Mettre en attente pour révision manuelle',                  'severity' => 'medium'],
                    ['id' => 'flag',       'name' => 'Signaler',    'description' => 'Marquer comme suspect mais autoriser l\'envoi',             'severity' => 'low'],
                    ['id' => 'review',     'name' => 'Révision',    'description' => 'Envoyer pour révision par l\'équipe',                       'severity' => 'medium'],
                ],
                'channels' => [
                    ['id' => 'sms',      'name' => 'SMS'],
                    ['id' => 'email',    'name' => 'Email'],
                    ['id' => 'whatsapp', 'name' => 'WhatsApp'],
                    ['id' => 'push',     'name' => 'Push'],
                ],
                'severity_levels' => [
                    ['id' => 'low',      'name' => 'Faible'],
                    ['id' => 'medium',   'name' => 'Moyen'],
                    ['id' => 'high',     'name' => 'Élevé'],
                    ['id' => 'critical', 'name' => 'Critique'],
                ],
                'frequency_periods' => [
                    ['id' => 'minute', 'name' => 'Par minute'],
                    ['id' => 'hour',   'name' => 'Par heure'],
                    ['id' => 'day',    'name' => 'Par jour'],
                ],
                'predefined_keywords' => [
                    'promotional' => ['GRATUIT', 'URGENT', 'MAINTENANT', 'CLIQUEZ ICI', 'OFFRE LIMITÉE', 'DERNIÈRE CHANCE', 'FÉLICITATIONS'],
                    'financial'   => ['ARGENT', 'CRÉDIT', 'PRÊT', 'INVESTISSEMENT', 'BITCOIN', 'CRYPTO', 'TRADING'],
                    'suspicious'  => ['VIRUS', 'MALWARE', 'PHISHING', 'SCAM', 'ARNAQUE', 'SUSPECT', 'DANGER'],
                    'medical'     => ['MÉDICAMENT', 'VIAGRA', 'PILULE', 'TRAITEMENT', 'GUÉRISON', 'MIRACLE'],
                ],
                'predefined_patterns' => [
                    'urls'   => [
                        ['pattern' => 'bit\\.ly\\/[a-zA-Z0-9]+',                                           'description' => 'URLs Bitly'],
                        ['pattern' => 'tinyurl\\.com\\/[a-zA-Z0-9]+',                                      'description' => 'URLs TinyURL'],
                        ['pattern' => 'http:\\/\\/[0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+',                     'description' => 'URLs avec IP'],
                    ],
                    'phones' => [
                        ['pattern' => '\\+?[0-9]{10,15}',  'description' => 'Numéros de téléphone'],
                        ['pattern' => '0[1-9][0-9]{8}',    'description' => 'Numéros locaux'],
                    ],
                    'emails' => [
                        ['pattern' => '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}', 'description' => 'Adresses email'],
                        ['pattern' => '@(gmail|yahoo|hotmail)\\.com',                      'description' => 'Emails gratuits'],
                    ],
                ],
                'content_rules_templates' => [
                    'Ratio liens/texte > 50%',
                    'Plus de 5 caractères spéciaux consécutifs',
                    'Texte entièrement en majuscules',
                    'Encodage suspect détecté',
                ],
            ],
        ]);
    }
}
