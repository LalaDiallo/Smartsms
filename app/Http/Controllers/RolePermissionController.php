<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RolesPermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Notifications\PermissionChangedNotification;
use App\Helpers\PermissionHelper;

class RolePermissionController extends Controller
{
    public function updateRole(Request $request, User $user)
    {
        $caller = auth()->user();

        if (!$caller?->hasPermission('peut_attribuer_permissions')) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        // Un admin (non super_admin) ne peut gérer que les utilisateurs de SA PROPRE
        // entreprise — sans ce contrôle, "admin" passe CheckPermission pour n'importe
        // quel rôle (bypass) et pourrait changer le rôle d'un utilisateur d'une AUTRE entreprise.
        if ($caller->role !== 'super_admin' && $user->client_id !== $caller->client_id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:super_admin,admin,manager,operator,developer,gouvernement,responsable_regional,observateur',
        ]);

        $user->update(['role' => $validated['role']]);

        if ($user->hasPermission('peut_generer_journaux_audit')) {
            Log::info('Rôle mis à jour', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $validated['role'],
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
        }

        $user->notify(new PermissionChangedNotification($validated['role'], $user->getAllPermissions(), Carbon::now()));

        return response()->json(['message' => 'Rôle mis à jour avec succès.'], 200);
    }


     public function getAllPermissions()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

         // récupère la ligne roles_permissions
        $rolePermissions = DB::table('roles_permissions')
            ->where('role', $user->role)
            ->first();

        if (!$rolePermissions) {
            return response()->json(['permissions' => (object) []]);
        }

        $labels = PermissionHelper::getPermissionLabels();

        $permissions = [];
        foreach ($labels as $key => $label) {
            $permissions[$key] = (bool) ($rolePermissions->$key ?? false);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'client_id' => $user->client_id,
            ],
            'permissions' => $permissions,
        ], 200);
    }

    public function getRolePermissions(string $role)
    {
        $permissions = DB::table('roles_permissions')
            ->where('role', $role)
            ->first();

        if (!$permissions) {
            return [];
        }

        $result = [];
        foreach ($permissions as $key => $value) {
            if (str_starts_with($key, 'peut_') && $value) {
                $result[] = $key;
            }
        }

        return $result;
    }

}
