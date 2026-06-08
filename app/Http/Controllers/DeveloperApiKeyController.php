<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DeveloperApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperApiKeyController extends Controller
{
    /**
     * Liste des clés de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $keys = DeveloperApiKey::where('client_id', $request->user()->client_id)
            ->orderByDesc('created_at')
            ->get([
                'id',
                'name',
                'service_id',
                'webhook_url',
                'is_active',
                'last_used_at',
                'created_at',
            ]);

        return response()->json($keys);
    }

    /**
     * Création d'une nouvelle clé
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'webhook_url' => 'nullable|url|max:500',
        ]);

        // Génération
        $plainToken = Str::random(40);
        $serviceId  = 'svc_' . Str::random(16);
        $user = auth()->user();
        $clientId = $user->client_id;

        $apiKey = DeveloperApiKey::create([
            'client_id'      => $clientId,
            'name'         => $validated['name'],
            'service_id'   => $serviceId,
            'secret_token' => hash('sha256', $plainToken),
            'webhook_url'  => $validated['webhook_url'] ?? null,
            'is_active'    => true,
        ]);

        // ⚠️ On retourne le token UNE SEULE FOIS
        return response()->json([
            'id'           => $apiKey->id,
            'name'         => $apiKey->name,
            'service_id'   => $serviceId,
            'secret_token' => $plainToken,
            'webhook_url'  => $apiKey->webhook_url,
            'created_at'   => $apiKey->created_at,
            'last_used_at' => null,
            'is_active'    => true,
        ], 201);
    }

    /**
     * Régénération du secret token
     */
    public function regenerate(Request $request, $id)
    {

        $user = auth()->user();
        $clientId = $user->client_id;

        $apiKey = DeveloperApiKey::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $plainToken = Str::random(40);

        $apiKey->update([
            'secret_token' => hash('sha256', $plainToken),
            'last_used_at' => null,
        ]);

        return response()->json([
            'message'      => 'Clé régénérée avec succès',
            'service_id'   => $apiKey->service_id,
            'secret_token' => $plainToken, // affiché une seule fois
        ]);
    }

    /**
     * Activation / désactivation
     */
    public function toggle(Request $request, $id)
    {
        $apiKey = DeveloperApiKey::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        $apiKey->update([
            'is_active' => ! $apiKey->is_active,
        ]);

        return response()->json([
            'message'   => 'Statut mis à jour',
            'is_active' => $apiKey->is_active,
        ]);
    }

    /**
     * Suppression définitive
     */
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        $clientId = $user->client_id;

        $apiKey = DeveloperApiKey::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $apiKey->delete();

        return response()->json([
            'message' => 'Clé supprimée avec succès',
        ]);
    }
}
