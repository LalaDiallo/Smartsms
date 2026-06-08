<?php

namespace App\Helpers;

use App\Models\Logs;
use App\Models\AppNotification;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Correspondance action → notification.
     * Format : [type, title, body_template]
     * Placeholders dans body : {name}, {email}, {reason}, {plan}, {count}, {role}
     */
    /**
     * Correspondance action → nom du NotificationSetting.
     * Si l'action est listée ici, on vérifie si l'utilisateur l'a désactivée.
     */
    private static array $notifySettingMap = [
        'campaign.created'   => 'Nouvelles campagnes',
        'campaign.launched'  => 'Nouvelles campagnes',
        'campaign.completed' => 'Campagnes terminées',
        'contact.created'    => 'Nouveaux contacts',
        'contact.imported'   => 'Nouveaux contacts',
    ];

    private static array $actionMap = [

        // ── Équipe ───────────────────────────────────────────────────────────
        'team.member_added'       => ['success', 'Nouveau membre ajouté',    'Un nouveau membre ({name}) a rejoint l\'équipe avec le rôle {role}.'],
        'team.member_updated'     => ['info',    'Membre modifié',            'Les informations de {name} ont été mises à jour.'],
        'team.member_deleted'     => ['warning', 'Membre supprimé',           '{name} a été retiré de l\'équipe.'],
        'team.member_suspended'   => ['warning', 'Membre suspendu',           '{name} a été suspendu. Raison : {reason}.'],
        'team.member_reactivated' => ['success', 'Membre réactivé',           '{name} a été réactivé avec succès.'],
        'team.role_updated'       => ['info',    'Rôle mis à jour',           'Le rôle de {name} a changé.'],

        // ── Campagnes ────────────────────────────────────────────────────────
        'campaign.created'        => ['success', 'Campagne créée',            'La campagne « {name} » a été créée.'],
        'campaign.launched'       => ['success', 'Campagne lancée',           'La campagne « {name} » est en cours d\'envoi.'],
        'campaign.completed'      => ['success', 'Campagne terminée',         'La campagne « {name} » a été envoyée avec succès.'],
        'campaign.rejected'       => ['error',   'Campagne rejetée',          'La campagne « {name} » a été rejetée (quota insuffisant).'],
        'campaign.approved'       => ['success', 'Campagne approuvée',        'La campagne « {name} » a été approuvée.'],
        'campaign.deleted'        => ['warning', 'Campagne supprimée',        'La campagne « {name} » a été supprimée.'],
        'campaign.archived'       => ['info',    'Campagne archivée',         'La campagne « {name} » a été archivée.'],

        // ── Contacts ─────────────────────────────────────────────────────────
        'contact.created'         => ['success', 'Contact ajouté',           'Le contact {name} a été ajouté.'],
        'contact.updated'         => ['info',    'Contact modifié',           'Le contact {name} a été modifié.'],
        'contact.deleted'         => ['warning', 'Contact supprimé',          'Le contact {name} a été supprimé.'],
        'contact.imported'        => ['success', 'Contacts importés',         '{count} contacts ont été importés avec succès.'],

        // ── Abonnements ──────────────────────────────────────────────────────
        'subscription.created'    => ['success', 'Abonnement souscrit',       'Vous êtes abonné au plan {plan}.'],
        'subscription.cancelled'  => ['warning', 'Abonnement résilié',        'Votre abonnement {plan} a été résilié.'],
        'subscription.renewed'    => ['success', 'Abonnement renouvelé',      'Votre abonnement {plan} a été renouvelé.'],
        'subscription.topup'      => ['success', 'Crédits SMS rechargés',     '{count} crédits SMS ont été ajoutés à votre compte.'],
        'subscription.expired'    => ['error',   'Abonnement expiré',         'Votre abonnement {plan} a expiré.'],

        // ── Paramètres ───────────────────────────────────────────────────────
        'settings.password'       => ['success', 'Mot de passe modifié',      'Votre mot de passe a été changé avec succès.'],
        'settings.profile'        => ['info',    'Profil mis à jour',         'Vos informations de profil ont été mises à jour.'],
        'settings.2fa_enabled'    => ['success', 'Double authentification activée', 'La 2FA a été activée sur votre compte.'],
        'settings.2fa_disabled'   => ['warning', 'Double authentification désactivée', 'La 2FA a été désactivée sur votre compte.'],

        // ── Sécurité ─────────────────────────────────────────────────────────
        'auth.login'              => ['info',    'Connexion',                 'Nouvelle connexion depuis {ip}.'],
        'auth.logout'             => ['info',    'Déconnexion',               'Vous avez été déconnecté.'],
        'auth.failed'             => ['error',   'Tentative de connexion échouée', 'Tentative de connexion échouée depuis {ip}.'],

        // ── API / Dev ────────────────────────────────────────────────────────
        'api.key_created'         => ['success', 'Clé API créée',            'Une nouvelle clé API a été générée.'],
        'api.key_deleted'         => ['warning', 'Clé API supprimée',         'Une clé API a été révoquée.'],

        // ── Entreprise ───────────────────────────────────────────────────────
        'client.suspended'        => ['warning', 'Compte client suspendu',    'Le compte de {name} a été suspendu.'],
        'client.activated'        => ['success', 'Compte client réactivé',    'Le compte de {name} a été réactivé.'],
        'client.terminated'       => ['error',   'Compte client résilié',     'Le compte de {name} a été résilié.'],
    ];

    public static function log(
        string  $action,
        array   $details       = [],
        ?string $resourceType  = null,
        ?int    $resourceId    = null
    ): void {
        $user = Auth::user();

        // 1. Créer le log d'audit
        Logs::create([
            'user_id'       => $user?->id,
            'client_id'     => $user?->client_id,
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'details'       => $details,
            'ip_address'    => request()->ip(),
        ]);

        // 2. Créer la notification associée
        if (isset(self::$actionMap[$action])) {
            // Vérifier si ce type de notification est désactivé pour ce client
            $notifyName = self::$notifySettingMap[$action] ?? null;
            if ($notifyName && $user?->client_id) {
                $disabled = NotificationSetting::where('client_id', $user->client_id)
                    ->where('name', $notifyName)
                    ->where('enabled', false)
                    ->exists();
                if ($disabled) return;
            }

            [$type, $title, $bodyTemplate] = self::$actionMap[$action];

            $body = self::interpolate($bodyTemplate, array_merge($details, [
                'ip' => request()->ip(),
            ]));

            AppNotification::create([
                'user_id'       => $user?->id,
                'client_id'     => $user?->client_id,
                'type'          => $type,
                'title'         => $title,
                'body'          => $body,
                'action'        => $action,
                'resource_type' => $resourceType,
                'resource_id'   => $resourceId,
                'link'          => self::resolveLink($action, $resourceType, $resourceId),
                'read'          => false,
            ]);
        }
    }

    /** Remplace les placeholders {key} par les valeurs du tableau $data */
    private static function interpolate(string $template, array $data): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($m) use ($data) {
            return $data[$m[1]] ?? '';
        }, $template);
    }

    /** Génère un lien frontend selon l'action et la ressource */
    private static function resolveLink(string $action, ?string $resourceType, ?int $resourceId): ?string
    {
        if (str_starts_with($action, 'campaign') && $resourceId) return "/campaigns/{$resourceId}";
        if (str_starts_with($action, 'contact')  && $resourceId) return "/contacts/{$resourceId}";
        if (str_starts_with($action, 'team'))    return '/team';
        if (str_starts_with($action, 'subscription')) return '/subscriptions';
        if (str_starts_with($action, 'settings')) return '/settings';
        if (str_starts_with($action, 'api'))     return '/developer';
        if (str_starts_with($action, 'client'))  return '/enterprise';
        return null;
    }
}
