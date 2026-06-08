<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    /**
     * Enregistre ou met à jour le token FCM de l'appareil connecté.
     * POST /api/device-tokens
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token'    => 'required|string',
            'platform' => 'sometimes|in:web,android,ios',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        DeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'token' => $validated['token']],
            [
                'client_id'    => $user->client_id,
                'platform'     => $validated['platform'] ?? 'web',
                'user_agent'   => $request->userAgent(),
                'last_used_at' => now(),
            ]
        );

        return response()->json(['message' => 'Token enregistré'], 201);
    }

    /**
     * Supprime le token FCM (déconnexion / désabonnement aux push).
     * DELETE /api/device-tokens
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate(['token' => 'required|string']);

        DeviceToken::where('user_id', Auth::id())
            ->where('token', $validated['token'])
            ->delete();

        return response()->json(['message' => 'Token supprimé']);
    }
}
