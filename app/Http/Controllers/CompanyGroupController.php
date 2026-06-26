<?php

namespace App\Http\Controllers;

use App\Mail\CompanyGroupInvitationMail;
use App\Models\AppNotification;
use App\Models\Campagnes;
use App\Models\Clients;
use App\Models\CompanyGroup;
use App\Models\CompanyGroupBranch;
use App\Models\Messages;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            ->withCount(['branches' => fn ($q) => $q->where('status', 'active')])
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

    // ─── Invitations de branches ────────────────────────────────────────────────

    /**
     * Inviter un client SmartSMS existant à rejoindre le groupe comme branche.
     * Le rattachement n'est effectif qu'après acceptation par l'admin du client invité.
     * POST /company-groups/{id}/branches/invite
     */
    public function inviteBranch(Request $request, int $id)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        $validated = $request->validate([
            'email'     => 'required|email',
            'zone_name' => 'required|string|max:100',
            'zone_type' => 'nullable|in:region,city,sector',
        ]);

        $client = Clients::where('email', $validated['email'])->first();

        if (!$client) {
            // Message volontairement vague : ne pas confirmer/infirmer l'existence
            // d'un compte SmartSMS pour cet email (énumération de comptes par un
            // client Enterprise authentifié testant des adresses au hasard).
            return response()->json([
                'message' => "Si un compte SmartSMS existe avec cet email, une invitation lui a été envoyée.",
            ], 202);
        }

        if ($client->id == $group->owner_client_id) {
            return response()->json(['message' => 'Le client propriétaire ne peut pas être une branche'], 422);
        }

        $alreadyLinked = CompanyGroupBranch::where('group_id', $group->id)
            ->where('client_id', $client->id)
            ->exists();

        if ($alreadyLinked) {
            return response()->json(['message' => 'Cette entreprise fait déjà partie de ce groupe ou a déjà été invitée.'], 422);
        }

        $token = Str::random(60);

        $branch = CompanyGroupBranch::create([
            'group_id'              => $group->id,
            'client_id'             => $client->id,
            'zone_name'             => $validated['zone_name'],
            'zone_type'             => $validated['zone_type'] ?? 'region',
            'status'                => 'pending',
            'invitation_token'      => $token,
            'invitation_expires_at' => now()->addDays(7),
        ]);

        try {
            Mail::to($client->email)->send(new CompanyGroupInvitationMail($branch, $group));

            AppNotification::create([
                'user_id'   => null,
                'client_id' => $client->id,
                'type'      => 'info',
                'title'     => "Invitation à rejoindre un groupe d'entreprise",
                'body'      => "{$group->ownerClient?->company_name} vous invite à rejoindre le groupe « {$group->name} ».",
                'link'      => "/group-invitations/{$token}",
            ]);
        } catch (\Throwable $e) {
            // Si l'email échoue, l'invité n'a aucun moyen de savoir qu'il est invité :
            // on annule la création plutôt que de laisser une invitation fantôme.
            $branch->delete();
            report($e);
            return response()->json(['message' => "L'email d'invitation n'a pas pu être envoyé. Vérifiez la configuration mail et réessayez."], 502);
        }

        return response()->json($branch->load('client'), 201);
    }

    /**
     * Détails publics (côté invité) d'une invitation en attente.
     * GET /company-groups/invitations/{token}
     */
    public function getInvitation(string $token)
    {
        $branch = CompanyGroupBranch::where('invitation_token', $token)
            ->with(['group.ownerClient'])
            ->first();

        if (!$branch) {
            return response()->json(['message' => 'Invitation introuvable.'], 404);
        }

        if ($branch->status !== 'pending') {
            return response()->json(['message' => 'Cette invitation a déjà été traitée.'], 409);
        }

        if ($branch->invitation_expires_at && $branch->invitation_expires_at->isPast()) {
            return response()->json(['message' => 'Cette invitation a expiré.'], 410);
        }

        if (Auth::user()->client_id !== $branch->client_id) {
            return response()->json(['message' => 'Cette invitation ne vous concerne pas.'], 403);
        }

        return response()->json([
            'group' => [
                'name'        => $branch->group->name,
                'description' => $branch->group->description,
                'industry'    => $branch->group->industry,
                'owner_name'  => $branch->group->ownerClient?->company_name,
            ],
            'zone_name'  => $branch->zone_name,
            'zone_type'  => $branch->zone_type,
            'expires_at' => $branch->invitation_expires_at,
        ]);
    }

    /**
     * Accepter une invitation — seul l'admin du client invité peut le faire.
     * POST /company-groups/invitations/{token}/accept
     */
    public function acceptInvitation(string $token)
    {
        $branch = CompanyGroupBranch::where('invitation_token', $token)->first();

        if (!$branch) {
            return response()->json(['message' => 'Invitation introuvable.'], 404);
        }

        if ($branch->status !== 'pending') {
            return response()->json(['message' => 'Cette invitation a déjà été traitée.'], 409);
        }

        if ($branch->invitation_expires_at && $branch->invitation_expires_at->isPast()) {
            return response()->json(['message' => 'Cette invitation a expiré.'], 410);
        }

        if (Auth::user()->client_id !== $branch->client_id) {
            return response()->json(['message' => 'Cette invitation ne vous concerne pas.'], 403);
        }

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => "Seul l'administrateur de votre compte peut accepter cette invitation."], 403);
        }

        $branch->update([
            'status'                => 'active',
            'invitation_token'      => null,
            'invitation_expires_at' => null,
        ]);

        return response()->json([
            'message' => 'Invitation acceptée.',
            'branch'  => $branch->fresh()->load('client'),
        ]);
    }

    /**
     * Refuser une invitation — supprime la demande de rattachement.
     * POST /company-groups/invitations/{token}/decline
     */
    public function declineInvitation(string $token)
    {
        $branch = CompanyGroupBranch::where('invitation_token', $token)->first();

        if (!$branch) {
            return response()->json(['message' => 'Invitation introuvable.'], 404);
        }

        if ($branch->status !== 'pending') {
            return response()->json(['message' => 'Cette invitation a déjà été traitée.'], 409);
        }

        if (Auth::user()->client_id !== $branch->client_id) {
            return response()->json(['message' => 'Cette invitation ne vous concerne pas.'], 403);
        }

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => "Seul l'administrateur de votre compte peut répondre à cette invitation."], 403);
        }

        $branch->delete();

        return response()->json(['message' => 'Invitation refusée.']);
    }

    // ─── Gestion des branches ──────────────────────────────────────────────────

    /**
     * Modifier une branche (active ou invitation en attente).
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
            'zone_name' => 'sometimes|string|max:100',
            'zone_type' => 'nullable|in:region,city,sector',
            'status'    => 'nullable|in:active,suspended',
        ]);

        $branch->update($validated);

        return response()->json($branch->fresh()->load('client'));
    }

    /**
     * Retirer une branche du groupe, ou annuler une invitation en attente.
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
     * Tableau de bord consolidé d'un groupe (branches actives uniquement).
     * GET /company-groups/{id}/dashboard
     */
    public function dashboard(int $id)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->with('branches.client')
            ->firstOrFail();

        $activeBranches  = $group->branches->where('status', 'active');
        $pendingBranches = $group->branches->where('status', 'pending');

        $clientIds   = $activeBranches->pluck('client_id')->toArray();
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

        // ── Performance par branche active ────────────────────────────────────
        $branchPerformance = $activeBranches->map(function ($branch) {
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
                'id'             => $branch->id,
                'zone_name'      => $branch->zone_name,
                'zone_type'      => $branch->zone_type,
                'client_name'    => $branch->client?->company_name,
                'status'         => $branch->status,
                'sms_sent'       => $t,
                'sms_delivered'  => $d,
                'delivery_rate'  => $t > 0 ? round(($d / $t) * 100, 1) : 0,
                'sms_remaining'  => $sub ? max(0, $sub->sms_quota - $sub->sms_used) : 0,
                'campaigns'      => Campagnes::where('client_id', $branch->client_id)->count(),
            ];
        })->values();

        // ── Invitations en attente ────────────────────────────────────────────
        $pendingInvitations = $pendingBranches->map(fn ($branch) => [
            'id'           => $branch->id,
            'zone_name'    => $branch->zone_name,
            'zone_type'    => $branch->zone_type,
            'client_name'  => $branch->client?->company_name,
            'client_email' => $branch->client?->email,
            'invited_at'   => $branch->created_at,
            'expires_at'   => $branch->invitation_expires_at,
        ])->values();

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
                'branches_count'   => $activeBranches->count(),
            ],
            'branches'            => $branchPerformance,
            'pending_invitations' => $pendingInvitations,
            'trend'               => $trend,
        ]);
    }

    /**
     * Détail d'une branche active : stats + évolution 30 jours propres à cette branche.
     * GET /company-groups/{id}/branches/{branchId}
     */
    public function branchDetail(int $id, int $branchId)
    {
        $group = CompanyGroup::where('id', $id)
            ->where('owner_client_id', Auth::user()->client_id)
            ->firstOrFail();

        $branch = CompanyGroupBranch::where('id', $branchId)
            ->where('group_id', $group->id)
            ->where('status', 'active')
            ->with('client')
            ->firstOrFail();

        $campaignIds = Campagnes::where('client_id', $branch->client_id)->pluck('id');

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

        $sub = Subscription::where('client_id', $branch->client_id)
            ->where('status', 'active')->first();

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
            'branch' => [
                'id'          => $branch->id,
                'zone_name'   => $branch->zone_name,
                'zone_type'   => $branch->zone_type,
                'client_name' => $branch->client?->company_name,
                'status'      => $branch->status,
            ],
            'stats' => [
                'sms_sent'      => $t,
                'sms_delivered' => $d,
                'sms_failed'    => $f,
                'delivery_rate' => $t > 0 ? round(($d / $t) * 100, 1) : 0,
                'sms_quota'     => (int) ($sub->sms_quota ?? 0),
                'sms_remaining' => $sub ? max(0, $sub->sms_quota - $sub->sms_used) : 0,
                'campaigns'     => $campaignIds->count(),
            ],
            'trend' => $trend,
        ]);
    }
}
