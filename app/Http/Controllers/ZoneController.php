<?php

namespace App\Http\Controllers;

use App\Models\Campagnes;
use App\Models\Messages;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    /**
     * Liste des zones du client connecté.
     * GET /zones
     */
    public function index()
    {
        $clientId = Auth::user()->client_id;

        $zones = Zone::where('client_id', $clientId)
            ->withCount(['users', 'campagnes'])
            ->orderBy('name')
            ->get();

        return response()->json($zones);
    }

    /**
     * Créer une zone.
     * POST /zones
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|in:region,city,sector',
        ]);

        $clientId = Auth::user()->client_id;

        $exists = Zone::where('client_id', $clientId)->where('name', $validated['name'])->exists();
        if ($exists) {
            return response()->json(['message' => 'Une zone avec ce nom existe déjà.'], 422);
        }

        $zone = Zone::create([
            'client_id' => $clientId,
            'name'      => $validated['name'],
            'type'      => $validated['type'] ?? 'region',
            'status'    => 'active',
        ]);

        return response()->json($zone, 201);
    }

    /**
     * Modifier une zone.
     * PUT /zones/{id}
     */
    public function update(Request $request, int $id)
    {
        $zone = Zone::where('id', $id)
            ->where('client_id', Auth::user()->client_id)
            ->firstOrFail();

        $validated = $request->validate([
            'name'   => 'sometimes|string|max:100',
            'type'   => 'nullable|in:region,city,sector',
            'status' => 'nullable|in:active,suspended',
        ]);

        $zone->update($validated);

        return response()->json($zone->fresh());
    }

    /**
     * Supprimer une zone (les utilisateurs/campagnes/contacts qui y étaient
     * rattachés ne sont pas supprimés, juste détachés — zone_id repasse à null).
     * DELETE /zones/{id}
     */
    public function destroy(int $id)
    {
        $zone = Zone::where('id', $id)
            ->where('client_id', Auth::user()->client_id)
            ->firstOrFail();

        $zone->delete();

        return response()->json(['message' => 'Zone supprimée']);
    }

    /**
     * Tableau de bord d'une zone : stats + évolution 30 jours.
     * GET /zones/{id}/dashboard
     */
    public function dashboard(int $id)
    {
        $zone = Zone::where('id', $id)
            ->where('client_id', Auth::user()->client_id)
            ->firstOrFail();

        $campaignIds = Campagnes::where('zone_id', $zone->id)->pluck('id');

        $stats = Messages::whereIn('campagnes_id', $campaignIds)
            ->selectRaw('
                COUNT(*) as total,
                SUM(status IN ("sent","delivered")) as sent,
                SUM(status = "delivered") as delivered,
                SUM(status = "failed") as failed
            ')->first();

        $t = (int) ($stats->total ?? 0);
        $d = (int) ($stats->delivered ?? 0);
        $f = (int) ($stats->failed ?? 0);

        $trend = Messages::whereIn('campagnes_id', $campaignIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(status = "delivered") as delivered,
                SUM(status = "failed") as failed
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'zone' => [
                'id'     => $zone->id,
                'name'   => $zone->name,
                'type'   => $zone->type,
                'status' => $zone->status,
            ],
            'stats' => [
                'sms_sent'      => $t,
                'sms_delivered' => $d,
                'sms_failed'    => $f,
                'delivery_rate' => $t > 0 ? round(($d / $t) * 100, 1) : 0,
                'campaigns'     => $campaignIds->count(),
                'contacts'      => $zone->contacts()->count(),
                'members'       => $zone->users()->count(),
            ],
            'trend' => $trend,
        ]);
    }
}
