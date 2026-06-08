<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RolesPermissions;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
    // public function handle(Request $request, Closure $next, $permission = null)
    // {
    //     $user = auth()->user();

    //     if ($user && $user->hasPermission($permission)) {
    //         return $next($request);
    //     }elseif($request->is('register') || $request->is('login'))
    //     {
    //         return $next($request);
    //     }

    //     abort(403, 'Unauthorized action.');
    // }

    public function handle(Request $request, Closure $next, $permission = null)
{
    // ✅ Autoriser les fichiers publics (images, logos, etc.)
    if ($request->is('storage/*')) {
        return $next($request);
    }

    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Non authentifié.'
        ], 401);
    }

    // super_admin bypasse toutes les vérifications de permissions
    if ($user->role === 'super_admin') {
        return $next($request);
    }

    if (!$permission) {
        return $next($request);
    }

    // admin bypass toutes les vérifications de permissions (propriétaire du compte)
    if ($user->role === 'admin') {
        return $next($request);
    }

    $permissions = RolesPermissions::where('role', $user->role)->first();

    if (!$permissions || !$permissions->$permission) {
        return response()->json([
            'status' => 'error',
            'message' => 'Accès non autorisé.'
        ], 403);
    }

    return $next($request);
}

}
