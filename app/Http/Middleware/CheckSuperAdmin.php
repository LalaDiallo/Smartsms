<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuperAdmin
{
    /**
     * Restreint une route aux super_admin uniquement.
     *
     * À utiliser (au lieu de CheckPermission) sur les routes qui gèrent les
     * données d'AUTRES entreprises (liste/suspension/résiliation de clients,
     * approbation de sender names/brandings d'autres tenants, etc.) :
     * CheckPermission laisse passer tout utilisateur de rôle "admin" sans
     * vérifier son client_id, ce qui permettrait à l'admin d'une entreprise
     * de gérer les données d'une AUTRE entreprise.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if ($user->role !== 'super_admin') {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return $next($request);
    }
}
