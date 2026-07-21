<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zone;
use App\Models\Contacts;
use App\Models\Campagnes;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\PermissionHelper;
use App\Mail\AccountDeletionMail;
use Illuminate\Support\Facades\DB;
use App\Mail\AccountActivationMail;
use App\Mail\AccountSuspensionMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountReactivationMail;

class TeamController extends Controller
{
    // Rôles que peut créer/gérer un admin secondaire (responsable_regional) dans sa zone
    private const ZONE_ADMIN_ASSIGNABLE_ROLES = ['operator', 'manager', 'developer'];

    public function index()
    {
        $user = Auth::user();

        $users = User::where('client_id', $user->client_id)
            ->when($user->zone_id, fn ($q) => $q->where('zone_id', $user->zone_id))
            ->with(['campaigns' => fn ($q) => $q->withCount('messages'), 'permissions', 'zone'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'role' => $user->role, // Doit correspondre à 'admin', 'manager', 'operator', 'viewer'
                    'status' => $user->status, // Doit correspondre à 'active', 'inactive', 'pending', 'suspended'
                    'zone' => $user->zone ? ['id' => $user->zone->id, 'name' => $user->zone->name] : null,
                    'avatar' => $user->profil ?? null, // Utiliser profil comme avatar
                    'joinedAt' => $user->created_at->toDateString(),
                    'lastActive' => $user->last_login_at ? $user->last_login_at->toDateString() : 'Jamais',
                    // dans le map
                    'permissions' => $user->permissions
                        ? collect(PermissionHelper::getPermissionLabels())
                            ->filter(fn($label, $key) => $user->permissions->{$key} ?? false)
                            ->values()
                            ->all()
                        : [],
                    'campaignsCreated' => $user->campaigns->count(),
                    'totalSent' => $user->campaigns->sum('messages_count'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $users // Retourne un tableau d'utilisateurs
        ], 200);
    }

    public function store(Request $request)
    {
        $creator = auth()->user();

        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin,manager,operator,developer,gouvernement,responsable_regional,observateur',
            'phone' => 'nullable|string|max:20',
            'zone_id' => [
                'required_if:role,responsable_regional',
                'nullable',
                Rule::exists('zones', 'id')->where('client_id', $creator->client_id),
            ],
        ]);

        // Un admin secondaire (responsable_regional) ne peut créer que des membres
        // locaux à sa propre zone, avec un rôle limité.
        if ($creator->zone_id && !in_array($validated['role'], self::ZONE_ADMIN_ASSIGNABLE_ROLES, true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Vous ne pouvez créer que des opérateurs, managers ou développeurs dans votre zone.',
            ], 403);
        }

        try {
            // Mot de passe temporaire aléatoire — envoyé par email ci-dessous.
            // Un mot de passe codé en dur identique pour tous les nouveaux comptes
            // serait une porte d'entrée triviale avant que l'utilisateur ne le change.
            $tempPassword = Str::password(14);

            $newZoneId = $creator->zone_id
                ? $creator->zone_id
                : ($validated['role'] === 'responsable_regional' ? $validated['zone_id'] : null);

            // Création de l'utilisateur
            $user = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($tempPassword),
                'role'              => $validated['role'],
                'client_id'         => $creator->client_id,
                'zone_id'           => $newZoneId,
                'status'            => 'active',
                'email_verified_at' => now(),
                'activation_token'  => Str::random(60),
                'phone'             => $validated['phone'] ?? null,
            ]);

            // Synchroniser dans la table contacts (status = employes)
            $nameParts = explode(' ', $user->name, 2);
            Contacts::updateOrCreate(
                ['client_id' => $user->client_id, 'email' => $user->email],
                [
                    'first_name'        => $nameParts[0],
                    'last_name'         => $nameParts[1] ?? '',
                    'phone'             => $user->phone ?? '',
                    'status'            => 'employes',
                    'preferred_channel' => 'sms',
                ]
            );

            Mail::to($user->email)->send(new AccountActivationMail($user, $tempPassword));

            ActivityLogger::log('team.member_added', [
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ], 'user', $user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur créé avec succès',
                'data' => $user
            ],201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $th->getMessage()
            ],500);
        }
    }

    public function update(Request $request, $id)
    {
        $auth = auth()->user();

        // Scope au client (et à la zone si l'auteur est un admin secondaire) pour
        // éviter toute modification cross-client / cross-zone.
        $user = User::where('id', $id)
            ->where('client_id', $auth->client_id)
            ->when($auth->zone_id, fn ($q) => $q->where('zone_id', $auth->zone_id))
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Utilisateur introuvable ou accès non autorisé.',
            ], 404);
        }

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,manager,operator,developer,gouvernement,responsable_regional,observateur',
            'phone' => 'nullable|string|max:20',
            'zone_id' => [
                'required_if:role,responsable_regional',
                'nullable',
                Rule::exists('zones', 'id')->where('client_id', $auth->client_id),
            ],
        ]);

        if ($auth->zone_id && !in_array($validated['role'], self::ZONE_ADMIN_ASSIGNABLE_ROLES, true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Vous ne pouvez assigner que les rôles opérateur, manager ou développeur.',
            ], 403);
        }

        try {
            $before = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];

            $newZoneId = $auth->zone_id
                ? $auth->zone_id
                : ($validated['role'] === 'responsable_regional' ? $validated['zone_id'] : null);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'zone_id' => $newZoneId,
                'phone' => $validated['phone'] ?? null,
            ]);

            // Mettre à jour le contact associé
            $nameParts = explode(' ', $validated['name'], 2);
            Contacts::updateOrCreate(
                ['client_id' => $user->client_id, 'email' => $validated['email']],
                [
                    'first_name' => $nameParts[0],
                    'last_name'  => $nameParts[1] ?? '',
                    'phone'      => $validated['phone'] ?? '',
                    'status'     => 'employes',
                ]
            );

            ActivityLogger::log('team.member_updated', [
                'before' => $before,
                'after'  => ['name' => $user->name, 'email' => $user->email, 'role' => $user->role],
            ], 'user', $user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur mis à jour avec succès',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $auth = Auth::user();

            // Scope au client (et à la zone si admin secondaire) pour éviter la suppression cross-client/zone
            $user = User::where('id', $id)
                ->where('client_id', $auth->client_id)
                ->when($auth->zone_id, fn ($q) => $q->where('zone_id', $auth->zone_id))
                ->first();

            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Utilisateur introuvable ou accès non autorisé.',
                ], 404);
            }

            // Empêcher l'auto-suppression
            if ($user->id === $auth->id) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
                ], 403);
            }

            ActivityLogger::log('team.member_deleted', [
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ], 'user', $user->id);

            $email    = $user->email;
            $clientId = $user->client_id;
            $user->delete();

            // Retirer le contact de type employes
            Contacts::where('client_id', $clientId)
                ->where('email', $email)
                ->where('status', 'employes')
                ->delete();

            try {
                Mail::to($email)->send(new AccountDeletionMail($user));
            } catch (\Throwable) {
                // L'email d'information est optionnel
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Utilisateur supprimé avec succès',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function toggleSuspend(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $auth = Auth::user();

        // Scope au client (et à la zone si admin secondaire) — auparavant aucun scoping,
        // ce qui permettait de suspendre/réactiver n'importe quel utilisateur cross-client.
        $user = User::where('id', $id)
            ->where('client_id', $auth->client_id)
            ->when($auth->zone_id, fn ($q) => $q->where('zone_id', $auth->zone_id))
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Utilisateur introuvable ou accès non autorisé.',
            ], 404);
        }

        try {
            if ($user->status === 'suspended') {
                $user->status = 'active';
                $user->save();

                Mail::to($user->email)->send(new AccountReactivationMail($user));

                ActivityLogger::log('team.member_reactivated', [
                    'name'  => $user->name,
                    'email' => $user->email,
                ], 'user', $user->id);

                $message = 'Utilisateur réactivé avec succès';
            } else {
                $user->status = 'suspended';
                $user->save();

                Mail::to($user->email)->send(new AccountSuspensionMail($user, $validated['reason']));

                ActivityLogger::log('team.member_suspended', [
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'reason' => $validated['reason'] ?? null,
                ], 'user', $user->id);

                $message = 'Utilisateur suspendu avec succès';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du changement de statut',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function role()
    {
        $auth = Auth::user();

        // Récupérer tous les rôles et permissions
        // Inclut super_admin pour l'affichage, mais marqué non-assignable
        $roles = DB::table('roles_permissions')->get();

        // Définir les infos personnalisées par rôle
        $roleDetails = [
            'super_admin' => [
                'name'        => 'Super Administrateur',
                'description' => 'Accès total à toutes les fonctionnalités de la plateforme',
                'color'       => 'text-amber-600 dark:text-amber-400',
                'icon'        => 'Crown',
                'assignable'  => false,
            ],
            'admin' => [
                'name' => 'Administrateur',
                'description' => 'Gestion complète des utilisateurs et paramètres',
                'color' => 'text-purple-600 dark:text-purple-400',
                'icon' => 'Shield',
            ],
            'manager' => [
                'name' => 'Gestionnaire',
                'description' => 'Création et suivi des campagnes',
                'color' => 'text-blue-600 dark:text-blue-400',
                'icon' => 'UserCheck',
            ],
            'operator' => [
                'name' => 'Opérateur',
                'description' => 'Exécution des campagnes et gestion des retours',
                'color' => 'text-emerald-600 dark:text-emerald-400',
                'icon' => 'UserCheck',
            ],
            'developer' => [
                'name' => 'Développeur',
                'description' => 'Accès API et intégrations',
                'color' => 'text-orange-600 dark:text-orange-400',
                'icon' => 'Code',
            ],
            'gouvernement' => [
                'name' => 'Gouvernement / Initiative Publique',
                'description' => "Envoi d'alertes nationales et reporting",
                'color' => 'text-yellow-600 dark:text-yellow-400',
                'icon' => 'Flag',
            ],
            'responsable_regional' => [
                'name' => 'Responsable Régional',
                'description' => 'Supervision des campagnes locales',
                'color' => 'text-teal-600 dark:text-teal-400',
                'icon' => 'MapPin',
            ],
            // ✅ Nouveau rôle ajouté
            'observateur' => [
                'name' => 'Observateur',
                'description' => 'Accès en lecture seule aux données et rapports',
                'color' => 'text-gray-600 dark:text-gray-400',
                'icon' => 'Eye',
            ],
        ];

        $labels = PermissionHelper::getPermissionLabels();

        // Précharger tous les compteurs en une seule requête GROUP BY
        $memberCounts = DB::table('users')
            ->where('client_id', $auth->client_id)
            ->whereIn('role', $roles->pluck('role'))
            ->selectRaw('role, COUNT(*) as cnt')
            ->groupBy('role')
            ->pluck('cnt', 'role');

        // Construire le retour pour chaque rôle
        $rolesWithMembers = $roles->map(function ($role) use ($roleDetails, $auth, $labels, $memberCounts) {
            // Récupérer les permissions activées (true)
            $permissions = collect($role)
                ->except(['id', 'role', 'created_at', 'updated_at'])
                ->filter(fn($value) => $value === 1)
                ->keys()
                ->map(fn($perm) => $labels[$perm] ?? $perm)
                ->toArray();

            $memberCount = $memberCounts[$role->role] ?? 0;

            // Détails personnalisés ou défaut
            $defaultRoleDetails = [
                'name' => ucfirst($role->role),
                'description' => 'Rôle non configuré',
                'color' => 'text-gray-600 dark:text-gray-400',
                'icon' => 'User',
            ];

            $details = $roleDetails[$role->role] ?? $defaultRoleDetails;

            return [
                'id'          => $role->role,
                'name'        => $details['name'],
                'description' => $details['description'],
                'permissions' => $permissions,
                'color'       => $details['color'],
                'icon'        => $details['icon'],
                'memberCount' => $memberCount,
                'assignable'  => $details['assignable'] ?? true,
            ];
        });

        return response()->json([
            'data' => $rolesWithMembers,
            'roles'=>$roles,
            'labels' => $labels
        ], 200);
    }

    public function matrix()
    {
        // Récupérer toutes les lignes sauf super_admin
        $rolesPermissions = DB::table('roles_permissions')
            ->where('role', '!=', 'super_admin')
            ->get();

        // Récupérer le mapping colonnes => labels
        $allPermissions = \App\Helpers\PermissionHelper::getPermissionLabels();

        $result = [
            'roles' => $rolesPermissions->pluck('role'),
            'permissions' => [],
        ];

        // Ici clé = colonne BDD, valeur = label FR
        foreach ($allPermissions as $column => $label) {
            $row = [
                'permission' => $label, // ce qu'on affiche côté React
                'roles' => [],
            ];

            foreach ($rolesPermissions as $role) {
                $row['roles'][$role->role] = (bool) $role->$column; // on utilise le vrai nom de colonne
            }

            $result['permissions'][] = $row;
        }

        return response()->json($result);
    }

}
