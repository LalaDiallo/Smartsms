<?php

namespace App\Http\Controllers;

use App\Models\Campagnes;
use App\Models\Clients;
use App\Models\CompanyGroup;
use App\Models\CompanyGroupBranch;
use App\Models\Messages;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyGroupController extends Controller
{
    // ─── Mes groupes (admin général) ──────────────────────────────────────────

    /**
     * Liste des groupes dont le client connecté est propriétaire.
     * GET /company-groups
     */
    public function index()
    {
        $clientId = Auth::user()->client_id;

        $groups = CompanyGroup::where('owner_client_id', $clientId)
            ->withCount('branches')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($groups);
    }

    /**
     * Créer un groupe d'entreprise.
     * POST /company-groups
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'industry'    => 'nullable|string|max:100',
        ]);

        $group = CompanyGroup::create([
            ...$validated,
            'owner_client_id' => Auth::user()->client_id,
            'status'          => 'active',
        ]);

        return response()->json($group, 201);
    }

    /**
     * Détails d'un groupe avec ses branches.
     * GET /company-groups/{id}
     */
    public function show(int $id)
    {
        $clientId = Auth::user()->client_id;

        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', $clientId)
            ->with(['branches.client'])
            ->firstOrFail();

        return response()->json($group);
    }

    /**
     * Modifier un groupe.
     * PUT /company-groups/{id}
     */
    public function update(Request $request, int $id)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'industry'    => 'nullable|string|max:100',
            'status'      => 'nullable|in:active,suspended',
        ]);

        $group->update($validated);

        return response()->json($group->fresh());
    }

    /**
     * Supprimer un groupe.
     * DELETE /company-groups/{id}
     */
    public function destroy(int $id)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        $group->delete();

        return response()->json(['message' => 'Groupe supprimé']);
    }

    // ─── Gestion des branches ──────────────────────────────────────────────────

    /**
     * Ajouter une branche (client existant) au groupe.
     * POST /company-groups/{id}/branches
     */
    public function addBranch(Request $request, int $id)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        $validated = $request->validate([
            'client_id'            => 'required|integer|exists:clients,id',
            'zone_name'            => 'required|string|max:100',
            'zone_type'            => 'nullable|in:region,city,sector',
            'sms_quota_allocated'  => 'nullable|integer|min:0',
        ]);

        // Empêcher d'ajouter le propre client propriétaire
        if ($validated['client_id'] == $group->owner_client_id) {
            return response()->json(['message' => 'Le client propriétaire ne peut pas être une branche'], 422);
        }

        $branch = CompanyGroupBranch::create([
            'group_id'            => $group->id,
            'client_id'           => $validated['client_id'],
            'zone_name'           => $validated['zone_name'],
            'zone_type'           => $validated['zone_type'] ?? 'region',
            'sms_quota_allocated' => $validated['sms_quota_allocated'] ?? 0,
            'status'              => 'active',
        ]);

        return response()->json($branch->load('client'), 201);
    }

    /**
     * Modifier une branche.
     * PUT /company-groups/{id}/branches/{branchId}
     */
    public function updateBranch(Request $request, int $id, int $branchId)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        $branch = CompanyGroupBranch::where('id', $branchId)
            ->where('group_id', $group->id)
            ->firstOrFail();

        $validated = $request->validate([
            'zone_name'           => 'sometimes|string|max:100',
            'zone_type'           => 'nullable|in:region,city,sector',
            'sms_quota_allocated' => 'nullable|integer|min:0',
            'status'              => 'nullable|in:active,suspended',
        ]);

        $branch->update($validated);

        return response()->json($branch->fresh()->load('client'));
    }

    /**
     * Retirer une branche du groupe.
     * DELETE /company-groups/{id}/branches/{branchId}
     */
    public function removeBranch(int $id, int $branchId)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        CompanyGroupBranch::where('id', $branchId)
            ->where('group_id', $group->id)
            ->firstOrFail()
            ->delete();

        return response()->json(['message' => 'Branche retirée']);
    }

    // ─── Dashboard consolidé ───────────────────────────────────────────────────

    /**
     * Tableau de bord consolidé d'un groupe.
     * GET /company-groups/{id}/dashboard
     */
    public function dashboard(int $id)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->with('branches')
            ->firstOrFail();

        $clientIds = $group->branches->pluck('client_id')->toArray();
        $clientIds[] = $group->owner_client_id; // inclure le client propriétaire

        // ── KPIs globaux ──────────────────────────────────────────────────────
        $totalCampaigns = Campagnes::whereIn('client_id', $clientIds)->count();
        $activeCampaigns = Campagnes::whereIn('client_id', $clientIds)
            ->whereIn('status', ['programmer', 'attente'])->count();

        $msgStats = Messages::whereIn('campagnes_id',
            Campagnes::whereIn('client_id', $clientIds)->pluck('id')
        )->selectRaw('
            COUNT(*) as total,
            SUM(status IN ("sent","delivered")) as sent,
            SUM(status = "delivered") as delivered,
            SUM(status = "failed") as failed
        ')->first();

        $total     = (int) ($msgStats->total ?? 0);
        $sent      = (int) ($msgStats->sent ?? 0);
        $delivered = (int) ($msgStats->delivered ?? 0);
        $failed    = (int) ($msgStats->failed ?? 0);

        // SMS quota global
        $quotaStats = Subscription::whereIn('client_id', $clientIds)
            ->where('status', 'active')
            ->selectRaw('SUM(sms_quota) as total_quota, SUM(sms_used) as total_used')
            ->first();

        // ── Performance par branche ───────────────────────────────────────────
        $branchPerformance = $group->branches->map(function ($branch) {
            $campaignIds = Campagnes::where('client_id', $branch->client_id)->pluck('id');

            $stats = Messages::whereIn('campagnes_id', $campaignIds)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(status IN ("sent","delivered")) as sent,
                    SUM(status = "delivered") as delivered
                ')->first();

            $t = (int) ($stats->total ?? 0);
            $d = (int) ($stats->delivered ?? 0);

            $sub = Subscription::where('client_id', $branch->client_id)
                ->where('status', 'active')->first();

            return [
                'branch_id'      => $branch->id,
                'zone_name'      => $branch->zone_name,
                'zone_type'      => $branch->zone_type,
                'client_name'    => $branch->client?->company_name,
                'status'         => $branch->status,
                'sms_sent'       => $t,
                'sms_delivered'  => $d,
                'delivery_rate'  => $t > 0 ? round(($d / $t) * 100, 1) : 0,
                'sms_remaining'  => $sub ? max(0, $sub->sms_quota - $sub->sms_used) : 0,
                'quota_allocated'=> $branch->sms_quota_allocated,
                'campaigns'      => Campagnes::where('client_id', $branch->client_id)->count(),
            ];
        });

        // ── Évolution 30 jours ────────────────────────────────────────────────
        $trend = Messages::whereIn('campagnes_id',
            Campagnes::whereIn('client_id', $clientIds)->pluck('id')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(status = "delivered") as delivered')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'group'      => [
                'id'   => $group->id,
                'name' => $group->name,
            ],
            'kpis' => [
                'total_campaigns'  => $totalCampaigns,
                'active_campaigns' => $activeCampaigns,
                'total_sent'       => $sent,
                'total_delivered'  => $delivered,
                'total_failed'     => $failed,
                'delivery_rate'    => $sent > 0 ? round(($delivered / $sent) * 100, 1) : 0,
                'total_quota'      => (int) ($quotaStats->total_quota ?? 0),
                'total_used'       => (int) ($quotaStats->total_used ?? 0),
                'branches_count'   => $group->branches->count(),
            ],
            'branches'   => $branchPerformance,
            'trend'      => $trend,
        ]);
    }

    /**
     * Chercher des clients existants à ajouter comme branche.
     * GET /company-groups/search-clients?q=...
     */
    public function searchClients(Request $request)
    {
        $q = $request->query('q', '');
        $groupId = $request->query('group_id');

        $existingIds = $groupId
            ? CompanyGroupBranch::where('group_id', $groupId)->pluck('client_id')->toArray()
            : [];

        $clients = Clients::where(function ($query) use ($q) {
                $query->where('company_name', 'LIKE', "%{$q}%")
                      ->orWhere('contact_name', 'LIKE', "%{$q}%")
                      ->orWhere('email', 'LIKE', "%{$q}%");
            })
            ->whereNotIn('id', $existingIds)
            ->limit(15)
            ->get(['id', 'company_name', 'contact_name', 'email', 'status']);

        return response()->json($clients);
    }
}
