<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Campagnes;
use App\Models\Contacts;
use App\Models\Messages;
use App\Models\Clients;
use App\Models\Branding;
use App\Models\SenderName;
use App\Models\Subscription;

class DashController extends Controller
{
    public function Auth()
    {
        $user   = auth()->user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['user' => $user, 'stats' => null]);
        }

        // Campagnes — toujours filtrer par client_id (migration appliquée)
        $campaignIds = Campagnes::where('client_id', $client->id)->pluck('id');

        $activeCampaigns = Campagnes::where('client_id', $client->id)
            ->whereIn('status', ['programmer', 'attente'])
            ->where('archived', false)
            ->count();

        $totalCampaigns = Campagnes::where('client_id', $client->id)->count();

        $recentCampaigns = Campagnes::where('client_id', $client->id)
            ->select(['id', 'name', 'status', 'channel', 'start_date', 'archived'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Contacts
        $totalContacts = Contacts::where('client_id', $client->id)
            ->where('status', '!=', 'NotInsert')
            ->count();

        // Messages — une seule requête agrégée
        $messageStats = Messages::whereIn('campagnes_id', $campaignIds)
            ->selectRaw('COUNT(*) as total, SUM(status = "delivered") as delivered, SUM(status IN ("sent","delivered")) as sent')
            ->first();

        $messagesTotal     = (int) ($messageStats->total     ?? 0);
        $messagesDelivered = (int) ($messageStats->delivered ?? 0);
        $messagesSent      = (int) ($messageStats->sent      ?? 0);
        $deliveryRate = $messagesTotal > 0
            ? round(($messagesDelivered / $messagesTotal) * 100, 1)
            : 0;

        // Abonnement actif
        $subscription = Subscription::where('client_id', $client->id)
            ->where('status', 'active')
            ->select(['sms_quota', 'sms_used', 'end_date'])
            ->first();

        return response()->json([
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'client_id' => $user->client_id,
                'avatar'    => $user->profil,
            ],
            'stats' => [
                'campaigns_active'  => $activeCampaigns,
                'campaigns_total'   => $totalCampaigns,
                'contacts_total'    => $totalContacts,
                'messages_sent'     => $messagesSent,
                'delivery_rate'     => $deliveryRate,
            ],
            'recent_campaigns' => $recentCampaigns,
            'subscription'     => $subscription,
        ]);
    }

    public function adminDashboard()
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // ── Clients ──────────────────────────────────────────────────────────
        $clientsTotal    = Clients::count();
        $clientsByStatus = Clients::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Abonnements ───────────────────────────────────────────────────────
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $mrr = (float) Subscription::where('status', 'active')->sum('price');

        // ── Approbations en attente ───────────────────────────────────────────
        $pendingSenderNames = SenderName::where('status', 'pending')->count();
        $pendingBrandings   = Branding::where('status', 'pending')->count();

        // ── Messages plateforme ───────────────────────────────────────────────
        $totalMessagesSent   = Messages::whereIn('status', ['sent', 'delivered'])->count();
        $messagesThisMonth   = Messages::whereIn('status', ['sent', 'delivered'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $messagesLastMonth   = Messages::whereIn('status', ['sent', 'delivered'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // ── Utilisateurs plateforme ───────────────────────────────────────────
        $usersTotal        = \App\Models\User::whereNotNull('client_id')->count();
        $usersNewThisMonth = \App\Models\User::whereNotNull('client_id')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Nouveaux clients ce mois ──────────────────────────────────────────
        $clientsNewThisMonth = Clients::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $clientsLastMonth    = Clients::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // ── Chiffre d'affaires total ──────────────────────────────────────────
        $revenueTotal = (float) Subscription::sum('price');

        // ── Clients récents ───────────────────────────────────────────────────
        $recentClients = Clients::with(['subscriptions' => fn($q) =>
                $q->where('status', 'active')->with('plan')->latest()->limit(1)
            ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['id', 'company_name', 'contact_name', 'email', 'status', 'created_at', 'industry']);

        // ── Distribution abonnements par plan ─────────────────────────────────
        $subsByPlan = Subscription::where('status', 'active')
            ->with('plan:id,name,slug')
            ->get()
            ->groupBy(fn($s) => $s->plan?->name ?? 'Inconnu')
            ->map->count();

        // ── Sender names en attente ───────────────────────────────────────────
        $pendingSenderNamesList = SenderName::where('status', 'pending')
            ->with('client:id,company_name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'client_id', 'name', 'created_at']);

        // ── Brandings en attente ──────────────────────────────────────────────
        $pendingBrandingsList = Branding::where('status', 'pending')
            ->with('client:id,company_name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'client_id', 'brand_name', 'created_at']);

        return response()->json([
            'stats' => [
                'clients_total'        => $clientsTotal,
                'clients_active'       => $clientsByStatus['active']    ?? 0,
                'clients_trial'        => $clientsByStatus['essaie']    ?? 0,
                'clients_suspended'    => $clientsByStatus['suspended'] ?? 0,
                'active_subscriptions' => $activeSubscriptions,
                'mrr'                  => $mrr,
                'pending_approvals'    => $pendingSenderNames + $pendingBrandings,
                'pending_sender_names' => $pendingSenderNames,
                'pending_brandings'    => $pendingBrandings,
                'total_messages_sent'   => $totalMessagesSent,
                'messages_this_month'   => $messagesThisMonth,
                'messages_last_month'   => $messagesLastMonth,
                'users_total'           => $usersTotal,
                'users_new_this_month'  => $usersNewThisMonth,
                'clients_new_this_month'=> $clientsNewThisMonth,
                'clients_last_month'    => $clientsLastMonth,
                'revenue_total'         => $revenueTotal,
            ],
            'subscriptions_by_plan'  => $subsByPlan,
            'recent_clients'         => $recentClients,
            'pending_sender_names'   => $pendingSenderNamesList,
            'pending_brandings'      => $pendingBrandingsList,
        ]);
    }
}
