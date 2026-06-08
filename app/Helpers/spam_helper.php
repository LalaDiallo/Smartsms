<?php

use App\Models\SpamRules;
use App\Models\SpamReports;
use App\Models\SpamViolation;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

if (!function_exists('analyze_campaign_spam')) {

    function analyze_campaign_spam(
        int $clientId,
        string $content,
        string $channel,
        ?int $campaignId = null
    ): array {

        $totalScore = 0;
        $violations = [];

        $rules = SpamRules::with([
                'keywords',
                'patterns',
                'contentRules',
                'channels'
            ])
            ->where('status', 'active')
            ->get();

        foreach ($rules as $rule) {

            // 🎯 Canal concerné ? (le champ s'appelle 'channel' pas 'name')
            if ($rule->channels->isNotEmpty()) {
                if (
                    !$rule->channels
                        ->pluck('channel')
                        ->map(fn ($c) => strtolower($c))
                        ->contains(strtolower($channel))
                ) {
                    continue;
                }
            }

            $matched = false;
            $reason = null;

            // 🔑 Mots-clés
            foreach ($rule->keywords as $keyword) {
                if (stripos($content, $keyword->keyword) !== false) {
                    $matched = true;
                    $reason = "Mot interdit détecté : {$keyword->keyword}";
                    break;
                }
            }

            // 🧬 Regex (le champ s'appelle 'pattern' pas 'regex')
            if (!$matched) {
                foreach ($rule->patterns as $pattern) {
                    $regex = $pattern->pattern ?? $pattern->regex ?? null;
                    if ($regex && @preg_match('/' . $regex . '/i', $content)) {
                        $matched = true;
                        $reason = "Pattern suspect détecté : {$regex}";
                        break;
                    }
                }
            }

            // 📏 Contenu
            if (!$matched) {
                foreach ($rule->contentRules as $cr) {
                    if ($cr->type === 'min_length' && strlen($content) < $cr->value) {
                        $matched = true;
                        $reason = "Message trop court";
                        break;
                    }
                }
            }

            if (!$matched) continue;

            // 🔢 Score
            $score = match ($rule->severity) {
                'low' => 10,
                'medium' => 30,
                'high' => 60,
                'critical' => 100,
                default => 0,
            };

            $totalScore += $score;

            // 💾 Enregistrement violation
            $violations[] = SpamViolation::create([
                'client_id'    => $clientId,
                'campaign_id'  => $campaignId,
                'spam_rule_id' => $rule->id,
                'channel'      => $channel,
                'action'       => $rule->action,
                'severity'     => $rule->severity,
                'reason'       => $reason,
                'score'        => $score,
            ]);

            // 📋 Rapport visible dans le menu anti-spam
            $statusMap = [
                'block'      => 'blocked',
                'quarantine' => 'quarantined',
                'flag'       => 'flagged',
                'review'     => 'reviewed',
            ];
            $user   = auth()->user();
            $sender = $user?->client?->company_name ?? $user?->name ?? 'Inconnu';

            SpamReports::create([
                'type'       => $channel,
                'content'    => mb_substr($content, 0, 500),
                'sender'     => $sender,
                'recipient'  => $campaignId ? "Campagne #{$campaignId}" : 'N/A',
                'reason'     => $reason,
                'status'     => $statusMap[$rule->action] ?? 'flagged',
                'risk_score' => $score,
                'timestamp'  => now(),
                'rule_id'    => $rule->id,
            ]);

            // Incrémenter le compteur de matches de la règle
            $rule->increment('matches');

            // 🚫 Blocage immédiat
            if ($rule->action === 'block') {
                throw ValidationException::withMessages([
                    'message.content' =>
                        "Campagne bloquée (anti-spam) : {$rule->nom_regle} – {$reason}",
                ]);
            }
        }

        return [
            'score' => $totalScore,
            'violations' => $violations,
        ];
    }
}
