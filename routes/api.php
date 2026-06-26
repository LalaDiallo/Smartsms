<?php

use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AntispamController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\SenderNameController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TargetingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\DeveloperApiKeyController;
use App\Http\Controllers\DevApiController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CompanyGroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\ZoneController;

// ─── Authentification publique (rate-limitée) ──────────────────────────────
Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/auth/verify-otp', [LoginController::class, 'verifyOtp']);
    Route::post('/reset-password', [LoginController::class, 'resetPassword']);
    Route::post('/password/email', [LoginController::class, 'forgotPassword']);
});

// ─── OAuth Social (pas de rate-limit strict — redirects navigateur) ─────────
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook|github|linkedin-openid');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook|github|linkedin-openid');

// Activation de compte (lien email → redirection frontend)
Route::get('/activate/{token}', [RegisterController::class, 'activate'])->name('activation');

// ─── Routes protégées — toujours accessibles même si le client est suspendu/résilié
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [LoginController::class, 'logout']);
});

// ─── Routes protégées — bloquées si l'entreprise est suspendue ou résiliée ──
Route::middleware(['auth:sanctum', 'CheckClientStatus'])->group(function () {

    // Permissions & rôles
    Route::put('/users/{user}/role', [RolePermissionController::class, 'updateRole'])
        ->middleware('CheckPermission:peut_attribuer_permissions');
    Route::get('/me', [RolePermissionController::class, 'getAllPermissions']);

    // ── Anti-spam ──────────────────────────────────────────────────────────
    Route::prefix('anti-spam')->group(function () {
        Route::get('/rules',              [AntispamController::class, 'rules']);
        Route::get('/rules/{id}',         [AntispamController::class, 'showRule']);
        Route::post('/rules',             [AntispamController::class, 'storeRule']);
        Route::put('/rules/{id}',         [AntispamController::class, 'updateRule']);
        Route::delete('/rules/{id}',      [AntispamController::class, 'destroyRule']);
        Route::put('/rules/{id}/toggle',  [AntispamController::class, 'toggleRuleStatus']);
        Route::get('/reports',            [AntispamController::class, 'reports']);
        Route::get('/compliance',         [AntispamController::class, 'compliance']);
        Route::get('/stats',              [AntispamController::class, 'stats']);
        Route::get('/config',             [AntispamController::class, 'config']);
    });

    // ── Tokens push (FCM) ─────────────────────────────────────────────────
    Route::post('/device-tokens',   [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);

    // ── Abonnements ────────────────────────────────────────────────────────
    Route::prefix('subscriptions')->group(function () {
        // Routes statiques (doivent être déclarées AVANT les routes paramétrées)
        Route::get('/plans',                                    [SubscriptionController::class, 'index']);
        Route::put('/plans/{id}',                               [SubscriptionController::class, 'updatePlan']);              // super_admin
        Route::put('/plans/{planId}/extra-sms/{tierId}',        [SubscriptionController::class, 'updateExtraTier']);         // super_admin
        Route::put('/billing-cycles/{id}',                      [SubscriptionController::class, 'updateBillingCycle']);      // super_admin
        Route::get('/long-term',                 [SubscriptionController::class, 'longTerm']);
        Route::post('/pricing',                  [SubscriptionController::class, 'pricing']);
        Route::post('/subscribe',                [SubscriptionController::class, 'subscribe']);
        Route::post('/upgrade-preview',          [SubscriptionController::class, 'upgradePreview']);
        Route::get('/current',                   [SubscriptionController::class, 'current']);
        Route::get('/active/{clientId}',         [SubscriptionController::class, 'active']);
        Route::get('/invoices/client/{clientId}',[SubscriptionController::class, 'clientInvoices']);

        // Routes paramétrées par ID
        Route::put('/{id}',         [SubscriptionController::class, 'update']);   // toggle auto_renew
        Route::delete('/{id}',      [SubscriptionController::class, 'cancel']);   // désabonnement
        Route::post('/{id}/cancel', [SubscriptionController::class, 'cancel']);   // alias POST (rétrocompat)
    });

    // ── Contacts ───────────────────────────────────────────────────────────
    Route::get('/contacts',        [ContactController::class, 'index'])
        ->middleware('CheckPermission:peut_voir_contacts');
    Route::get('/contacts/{id}',   [ContactController::class, 'show'])
        ->middleware('CheckPermission:peut_voir_contacts');
    Route::post('/contacts',       [ContactController::class, 'store'])
        ->middleware('CheckPermission:peut_gerer_contacts');
    Route::put('/contacts/{id}',   [ContactController::class, 'update'])
        ->middleware('CheckPermission:peut_gerer_contacts');
    Route::delete('/contacts/{id}',[ContactController::class, 'destroy'])
        ->middleware('CheckPermission:peut_gerer_contacts');
    Route::post('/contacts/bulk',  [ContactController::class, 'bulkStore'])
        ->middleware('CheckPermission:peut_gerer_contacts');
    Route::post('/import-contacts',[ContactController::class, 'import'])
        ->middleware(['CheckPermission:peut_gerer_contacts', 'throttle:10,1']); // max 10 imports/minute
    Route::post('/export-contacts',[ContactController::class, 'export'])
        ->middleware(['CheckPermission:peut_voir_contacts', 'throttle:20,1']);
    Route::post('/send-sms',       [ContactController::class, 'sendSmsToContact'])
        ->middleware('CheckPermission:peut_envoyer_campagne');

    // ── Campagnes ──────────────────────────────────────────────────────────
    Route::get('/analytics/global',        [CampagneController::class, 'globalAnalytics']); // données scopées par client
    Route::get('/campagnes',               [CampagneController::class, 'index'])
        ->middleware('CheckPermission:peut_voir_campagnes');
    Route::post('/campagnes',              [CampagneController::class, 'store'])
        ->middleware('CheckPermission:peut_creer_campagne');
    Route::get('/campagnes/{id}',          [CampagneController::class, 'Details'])
        ->middleware('CheckPermission:peut_voir_detail_campagnes');
    Route::put('/campagnes/{id}',          [CampagneController::class, 'update'])
        ->middleware('CheckPermission:peut_modifier_details_mineurs_campagne');
    Route::delete('/campagnes/{id}',       [CampagneController::class, 'destroy'])
        ->middleware('CheckPermission:peut_supprimer_campagne');
    Route::post('/campagnes/bulk-delete',  [CampagneController::class, 'bulkDelete'])
        ->middleware('CheckPermission:peut_supprimer_campagne');
    Route::get('/campagnes/{id}/statistics',[CampagneController::class, 'statistics'])
        ->middleware('CheckPermission:peut_voir_analytiques');
    Route::post('/campagnes/{id}/archive',   [CampagneController::class, 'archive'])
        ->middleware('CheckPermission:peut_gerer_campagne');
    Route::post('/campagnes/{id}/dearchive', [CampagneController::class, 'DeArchive'])
        ->middleware('CheckPermission:peut_gerer_campagne');
    Route::patch('/campagnes/{id}/lancer',   [CampagneController::class, 'launchCampaign'])
        ->middleware('CheckPermission:peut_envoyer_campagne');
    Route::post('/campagnes/{id}/approuver', [CampagneController::class, 'approve'])
        ->middleware('CheckPermission:peut_approuver_campagne');
    Route::patch('/campagnes/{id}/rejeter',  [CampagneController::class, 'reject'])
        ->middleware('CheckPermission:peut_approuver_campagne');

    // Audience & groupes pour campagnes
    Route::get('/groupes/contact',   [CampagneController::class, 'groupesEtContacts'])
        ->middleware('CheckPermission:peut_voir_campagnes');
    Route::get('/contact/audience',  [CampagneController::class, 'contact'])
        ->middleware('CheckPermission:peut_voir_campagnes');

    Route::post('/chatbot', [ChatbotController::class, 'ask']);

    // ── Groupes ────────────────────────────────────────────────────────────
    Route::get('/groupes',         [GroupeController::class, 'index'])
        ->middleware('CheckPermission:peut_voir_campagnes');
    Route::post('/groupes',        [GroupeController::class, 'store'])
        ->middleware('CheckPermission:peut_gerer_contacts');
    Route::delete('/groupes/{id}', [GroupeController::class, 'destroy'])
        ->middleware('CheckPermission:peut_gerer_contacts');

    // ── Équipe ─────────────────────────────────────────────────────────────
    // IMPORTANT : les routes statiques doivent être déclarées AVANT /team/{id}
    // pour éviter que "role" soit capturé comme paramètre {id}

    // Lecture — accessible à tout membre ayant accès au menu équipe
    Route::get('/team',                      [TeamController::class, 'index'])
        ->middleware('CheckPermission:peut_modifier_utilisateur');
    Route::get('/team/role',                 [TeamController::class, 'role']);   // liste de référence
    Route::get('/team/roles',                [TeamController::class, 'role']);   // liste de référence
    Route::get('/permissions/matrix',        [TeamController::class, 'matrix'])
        ->middleware('CheckPermission:peut_attribuer_permissions');

    // Écriture équipe — plan Starter minimum
    Route::post('/team',                     [TeamController::class, 'store'])
        ->middleware(['CheckPermission:peut_creer_utilisateur', 'CheckPlan:starter']);
    Route::put('/team/{id}',                 [TeamController::class, 'update'])
        ->middleware(['CheckPermission:peut_modifier_utilisateur', 'CheckPlan:starter']);
    Route::delete('/team/{id}',              [TeamController::class, 'destroy'])
        ->middleware(['CheckPermission:peut_supprimer_utilisateur', 'CheckPlan:starter']);
    Route::post('/team/{id}/toggle-suspend', [TeamController::class, 'toggleSuspend'])
        ->middleware(['CheckPermission:peut_modifier_utilisateur', 'CheckPlan:starter']);

    // ── Zones (agences internes / admins secondaires) ───────────────────────
    Route::get('/zones',                [ZoneController::class, 'index'])
        ->middleware('CheckPermission:peut_modifier_utilisateur');
    Route::post('/zones',               [ZoneController::class, 'store'])
        ->middleware(['CheckPermission:peut_creer_utilisateur', 'CheckPlan:starter']);
    Route::put('/zones/{id}',           [ZoneController::class, 'update'])
        ->middleware(['CheckPermission:peut_modifier_utilisateur', 'CheckPlan:starter']);
    Route::delete('/zones/{id}',        [ZoneController::class, 'destroy'])
        ->middleware(['CheckPermission:peut_supprimer_utilisateur', 'CheckPlan:starter']);
    Route::get('/zones/{id}/dashboard', [ZoneController::class, 'dashboard'])
        ->middleware('CheckPermission:peut_modifier_utilisateur');

    // ── Logs d'activité ────────────────────────────────────────────────────
    Route::get('/logs',                  [LogController::class, 'index']);
    Route::get('/logs/actions',          [LogController::class, 'actions']);
    Route::get('/logs/team-activities',  [LogController::class, 'teamActivities']);

    // ── Templates ──────────────────────────────────────────────────────────
    Route::get('/templates',          [TemplateController::class, 'index'])
        ->middleware('CheckPermission:peut_personnaliser_contenu');
    Route::post('/templates',         [TemplateController::class, 'store'])
        ->middleware('CheckPermission:peut_creer_campagne');
    Route::put('/templates/{id}',     [TemplateController::class, 'update'])
        ->middleware('CheckPermission:peut_modifier_details_mineurs_campagne');
    Route::delete('/templates/{id}',  [TemplateController::class, 'destroy'])
        ->middleware('CheckPermission:peut_supprimer_campagne');
    Route::put('/toggle-favorite/{id}', [TemplateController::class, 'toggleFavorite'])
        ->middleware('CheckPermission:peut_personnaliser_contenu');

    // ── Entreprise / Clients (super_admin uniquement) ──────────────────────
    // CheckSuperAdmin (pas CheckPermission) : ces routes gèrent les données
    // d'AUTRES entreprises — CheckPermission laisse passer tout rôle "admin"
    // sans vérifier son client_id, ce qui permettrait à l'admin d'une
    // entreprise de gérer les données d'une autre entreprise.
    Route::get('/entreprise',          [EntrepriseController::class, 'clients'])
        ->middleware('CheckSuperAdmin');
    Route::get('/entreprise/{id}',     [EntrepriseController::class, 'showClient'])
        ->middleware('CheckSuperAdmin');
    Route::get('/clients',             [EntrepriseController::class, 'index'])
        ->middleware('CheckSuperAdmin');
    Route::get('/clients/{id}',        [EntrepriseController::class, 'show'])
        ->middleware('CheckSuperAdmin');
    Route::delete('/clients/{id}',     [EntrepriseController::class, 'destroy'])
        ->middleware('CheckSuperAdmin');
    Route::put('/clients/{id}/suspend',[EntrepriseController::class, 'suspend'])
        ->middleware('CheckSuperAdmin');
    Route::post('/clients/{client}/send-email', [EntrepriseController::class, 'sendActionEmail'])
        ->name('clients.send-email');
    Route::post('/email/send-client-action', [EntrepriseController::class, 'sendClientActionEmail'])
        ->middleware('CheckSuperAdmin');

    // Plans (utilise le système unifié SubscriptionPlan)
    Route::get('/plans', [SubscriptionController::class, 'index']);

    // ── Dashboard ──────────────────────────────────────────────────────────
    Route::get('/dashboard/auth',  [DashController::class, 'Auth']);         // auth Sanctum suffit
    Route::get('/dashboard/charts', [DashController::class, 'charts']);       // données des graphiques
    Route::get('/admin/dashboard', [DashController::class, 'adminDashboard']); // super_admin uniquement
    Route::get('/admin/orange-sms-balance', [DashController::class, 'orangeSmsBalance']); // super_admin uniquement

    // ── Notifications header ───────────────────────────────────────────────
    Route::get('/header/notifications', function (Request $request) {
        $user = $request->user();

        $rows = \App\Models\AppNotification::where(function ($q) use ($user) {
                // Notifications personnelles
                $q->where('user_id', $user->id);
                // Notifications système du compte (quota, etc.) visibles aux admins uniquement
                if (in_array($user->role, ['admin', 'super_admin']) && $user->client_id) {
                    $q->orWhere(fn ($q2) => $q2->whereNull('user_id')->where('client_id', $user->client_id));
                }
            })
            ->latest()
            ->limit(30)
            ->get();

        $notifs = $rows->map(fn ($n) => [
            'id'    => $n->id,
            'type'  => $n->type,
            'title' => $n->title,
            'body'  => $n->body,
            'link'  => $n->link,
            'time'  => $n->created_at->diffForHumans(),
            'read'  => (bool) $n->read,
        ]);

        return response()->json([
            'notifications' => $notifs,
            'unread_count'  => $rows->where('read', false)->count(),
        ]);
    });

    // Marquer une notification comme lue
    Route::post('/header/notifications/{id}/read', function (Request $request, int $id) {
        \App\Models\AppNotification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->update(['read' => true]);
        return response()->json(['ok' => true]);
    });

    // Marquer toutes comme lues
    Route::post('/header/notifications/read-all', function (Request $request) {
        \App\Models\AppNotification::where('user_id', $request->user()->id)
            ->update(['read' => true]);
        return response()->json(['ok' => true]);
    });

    Route::get('/header/me', function (Request $request) {
        $user   = $request->user();
        $client = $user->client;
        $sub    = $client ? \App\Models\Subscription::where('client_id', $client->id)
            ->where('status', 'active')->with('plan')->latest()->first() : null;

        return response()->json([
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'role'         => $user->role,
            'avatar'       => $user->profil ?? $user->avatar,
            'company'      => $client?->company_name,
            'sms_remaining'=> $sub ? max(0, $sub->sms_quota - $sub->sms_used) : null,
            'sms_quota'    => $sub?->sms_quota,
            'plan_name'    => $sub?->plan?->name,
            'plan_slug'    => $sub?->plan?->slug ?? 'freemium',
        ]);
    });

    // ── Paramètres utilisateur ─────────────────────────────────────────────
    Route::get('/notify',                   [SettingController::class, 'index'])
        ->middleware('CheckPermission:peut_configurer_parametres_globaux');
    Route::get('/user-data',                [SettingController::class, 'UserData']);
    Route::post('/profile/picture',          [SettingController::class, 'updateProfilePicture']);
    Route::put('/profile/{id}',             [SettingController::class, 'updateProfile']);
    Route::put('/notify/{id}',              [SettingController::class, 'updateNotificationSettings']);
    Route::put('/security/password/{id}',   [SettingController::class, 'updatePassword']);
    Route::put('/security/two-factor',      [SettingController::class, 'updateTwoFactorAuthentication']);
    Route::put('/integrations',             [SettingController::class, 'updateIntegrations']);
    Route::delete('/sessions',              [SettingController::class, 'destroySession']);
    Route::post('/auth/enable-2fa',         [SettingController::class, 'enable2FA']);
    Route::post('/auth/disable-2fa',        [SettingController::class, 'disable2FA']);

    // Sessions
    Route::get('/auth/sessions', function (Request $request) {
        return $request->user()->sessions()->orderByDesc('last_activity_at')->get();
    });
    Route::delete('/auth/sessions/{id}', function (int $id) {
        $session = UserSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $session->delete();
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Session fermée avec succès']);
    });

    // ── Branding ───────────────────────────────────────────────────────────
    Route::get('/brandings/client',           [BrandingController::class, 'show']);
    Route::post('/brandings',                 [BrandingController::class, 'store']);
    // CheckSuperAdmin : ces 4 routes approuvent/rejettent/listent le branding
    // de N'IMPORTE QUEL client — pas un CheckPermission (bypassable par tout "admin").
    Route::get('/brandings',                  [BrandingController::class, 'index'])
        ->middleware('CheckSuperAdmin');
    Route::post('/brandings/{id}/approve',    [BrandingController::class, 'approve'])
        ->middleware('CheckSuperAdmin');
    Route::post('/brandings/{id}/reject',     [BrandingController::class, 'reject'])
        ->middleware('CheckSuperAdmin');
    Route::put('/brandings/{id}',             [BrandingController::class, 'update'])
        ->middleware('CheckSuperAdmin');
    Route::post('/brandings/activate/{id}',   [BrandingController::class, 'activate']);

    // ── Sender Names ──────────────────────────────────────────────────────
    Route::get('/sender-names',                    [SenderNameController::class, 'index']);
    Route::post('/sender-names',                   [SenderNameController::class, 'store']);
    Route::post('/sender-names/{id}/activate',     [SenderNameController::class, 'activate']);
    // CheckSuperAdmin : ces 3 routes listent/approuvent/rejettent les demandes
    // de N'IMPORTE QUEL client — pas un CheckPermission (bypassable par tout "admin").
    Route::get('/sender-names/admin',              [SenderNameController::class, 'adminIndex'])
        ->middleware('CheckSuperAdmin');
    Route::post('/sender-names/{id}/approve',      [SenderNameController::class, 'approve'])
        ->middleware('CheckSuperAdmin');
    Route::post('/sender-names/{id}/reject',       [SenderNameController::class, 'reject'])
        ->middleware('CheckSuperAdmin');

    // ── Clés API développeur — plan Pro minimum ────────────────────────────
    Route::middleware('CheckPlan:pro')->group(function () {
        Route::get('/api-keys',                      [DeveloperApiKeyController::class, 'index']);
        Route::post('/api-keys',                     [DeveloperApiKeyController::class, 'store']);
        Route::put('/api-keys/regenerate/{id}',      [DeveloperApiKeyController::class, 'regenerate']);
        Route::patch('/api-keys/{id}/toggle',        [DeveloperApiKeyController::class, 'toggle']);
        Route::delete('/api-keys/{id}',              [DeveloperApiKeyController::class, 'destroy']);
    });

    // ── Paiements ─────────────────────────────────────────────────────────────
    Route::prefix('payments')->group(function () {
        Route::post('/initiate',          [PaymentController::class, 'initiate']);
        Route::post('/verify',            [PaymentController::class, 'verify']);
        Route::get('/history',            [PaymentController::class, 'history']);
        // LengoPay
        Route::post('/lengopay/initiate', [PaymentController::class, 'lengoInitiate']);
        Route::post('/lengopay/verify',   [PaymentController::class, 'lengoVerify']);
    });

    // ── Segments / Ciblage — plan Pro minimum ─────────────────────────────
    Route::get('/segments',                              [TargetingController::class, 'index'])
        ->middleware('CheckPlan:pro');
    Route::post('/segments',                             [TargetingController::class, 'store']);
    Route::get('/segments/{id}',                         [TargetingController::class, 'show']);
    Route::put('/segments/{id}',                         [TargetingController::class, 'update']);
    Route::delete('/segments/{id}',                      [TargetingController::class, 'destroy']);
    Route::post('/segments/{id}/contacts',               [TargetingController::class, 'attachContacts']);
    Route::delete('/segments/{id}/contacts/{contactId}', [TargetingController::class, 'detachContact']);
    Route::get('/segments/{id}/rules',                   [TargetingController::class, 'getRules']);
    Route::post('/segments/{id}/rules',                  [TargetingController::class, 'saveRules']);
    Route::get('/segments/{id}/preview',                 [TargetingController::class, 'preview']);
    Route::get('/segments/{id}/contacts',                [TargetingController::class, 'contacts']);
    Route::get('/segments/{id}/stats',                   [TargetingController::class, 'stats']);
    Route::get('/segments/{id}/resolve',                 [TargetingController::class, 'resolve']);

    // ── Groupes d'entreprise — plan Enterprise uniquement (côté propriétaire) ──
    Route::middleware('CheckPlan:enterprise')->group(function () {
        Route::get('/company-groups',                                   [CompanyGroupController::class, 'index']);
        Route::post('/company-groups',                                  [CompanyGroupController::class, 'store']);
        Route::get('/company-groups/{id}',                              [CompanyGroupController::class, 'show']);
        Route::put('/company-groups/{id}',                              [CompanyGroupController::class, 'update']);
        Route::delete('/company-groups/{id}',                           [CompanyGroupController::class, 'destroy']);
        Route::get('/company-groups/{id}/dashboard',                    [CompanyGroupController::class, 'dashboard']);
        Route::get('/company-groups/{id}/branches/{branchId}',          [CompanyGroupController::class, 'branchDetail']);
        Route::post('/company-groups/{id}/branches/invite',             [CompanyGroupController::class, 'inviteBranch']);
        Route::put('/company-groups/{id}/branches/{branchId}',          [CompanyGroupController::class, 'updateBranch']);
        Route::delete('/company-groups/{id}/branches/{branchId}',       [CompanyGroupController::class, 'removeBranch']);
    });

    // ── Invitations de groupe — côté invité, aucun plan requis ────────────────
    Route::get('/company-groups/invitations/{token}',          [CompanyGroupController::class, 'getInvitation']);
    Route::post('/company-groups/invitations/{token}/accept',  [CompanyGroupController::class, 'acceptInvitation']);
    Route::post('/company-groups/invitations/{token}/decline', [CompanyGroupController::class, 'declineInvitation']);
});

// ── Simulation paiement (désactivé en production) ────────────────────────────
Route::get('/payments/simulate/confirm', [PaymentController::class, 'simulateConfirm']);

// ── Webhooks paiement (routes publiques — appelées par les providers) ────────
Route::prefix('payments/webhooks')->group(function () {
    Route::post('/card',   [PaymentController::class, 'webhookCard']);
    Route::post('/orange', [PaymentController::class, 'webhookOrange']);
    Route::post('/wave',   [PaymentController::class, 'webhookWave']);
    Route::post('/mtn',    [PaymentController::class, 'webhookMtn']);
});

// ── LengoPay callback (public — appelé par LengoPay après le paiement) ───────
Route::post('/payments/lengopay/callback', [PaymentController::class, 'lengoCallback']);

// ── Annulation de paiement (appelé via return depuis page /payment/cancel) ───
Route::middleware('auth:sanctum')->post('/payments/lengopay/cancel', function (\Illuminate\Http\Request $request) {
    $user = $request->user();

    // Expirer toutes les subscriptions pending non payées
    $expired = \App\Models\Subscription::where('client_id', $user->client_id)
        ->where('status', 'pending')
        ->where('payment_status', 'pending')
        ->update(['status' => 'expired', 'payment_status' => 'cancelled']);

    // Annuler aussi les topups pending non payés
    \App\Models\SmsTopupPayment::where('client_id', $user->client_id)
        ->where('status', 'pending')
        ->update(['status' => 'cancelled']);

    cache()->forget("subscription:current:{$user->client_id}");

    \Illuminate\Support\Facades\Log::info('LengoPay: paiement annulé par l\'utilisateur', [
        'client_id' => $user->client_id,
        'expired'   => $expired,
    ]);

    return response()->json(['message' => 'Paiement annulé', 'expired' => $expired]);
});

// ── Documentation (accessible via Sanctum) ───────────────────────────────────
Route::get('/dev/v1/docs', [DevApiController::class, 'docs'])
    ->middleware('auth:sanctum');

// ── Developer Public API (authentification par clé API) ───────────────────────
Route::prefix('dev/v1')->middleware(['dev.auth', 'throttle:60,1'])->group(function () {
    Route::post('/sms/send', [DevApiController::class, 'sendSms']);
    Route::post('/sms/bulk', [DevApiController::class, 'sendBulk']);
    Route::get('/balance',   [DevApiController::class, 'balance']);
});
