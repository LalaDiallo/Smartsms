<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckClientStatus
{
    /**
     * Bloque l'accès à l'API si l'entreprise du client connecté est
     * suspendue ou résiliée (cf. boutons Suspendre/Résilier de l'admin).
     * super_admin n'a pas de client_id → toujours autorisé.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->client_id) {
            return $next($request);
        }

        $client = $user->client;

        if ($client?->status === 'suspended') {
            return response()->json([
                'error'         => 'client_suspended',
                'message'       => "Le compte de votre entreprise a été suspendu. Contactez le support pour le réactiver.",
                'client_status' => 'suspended',
            ], 403);
        }

        if ($client?->status === 'resilie') {
            return response()->json([
                'error'         => 'client_terminated',
                'message'       => "Le compte de votre entreprise a été résilié. Contactez le support pour le réactiver.",
                'client_status' => 'resilie',
            ], 403);
        }

        return $next($request);
    }
}
