<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Campagnes;
use App\Models\Contacts;
use App\Models\Messages;
use App\Models\Clients;
use App\Models\Branding;
use App\Models\SenderName;
use App\Models\Subscription;
use App\Services\OrangeSmsService;

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

    public function charts()
    {
        $user   = auth()->user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['evolution' => [], 'weekly' => [], 'by_channel' => [], 'violations' => 0]);
        }

        $campaignIds = Campagnes::where('client_id', $client->id)->pluck('id');

        // ── Évolution sur 6 mois (sms/whatsapp/email) ───────────────────────
        $monthLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $monthlyRows = Messages::whereIn('campagnes_id', $campaignIds)
            ->whereIn('status', ['sent', 'delivered'])
            ->where('created_at', '>=', $months->first())
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, channel, COUNT(*) as total')
            ->groupBy('y', 'm', 'channel')
            ->get();

        $evolution = $months->map(function ($month) use ($monthlyRows, $monthLabels) {
            $rowsForMonth = $monthlyRows->where('y', $month->year)->where('m', $month->month);
            return [
                'name'     => $monthLabels[$month->month - 1],
                'sms'      => (int) $rowsForMonth->where('channel', 'sms')->sum('total'),
                'whatsapp' => (int) $rowsForMonth->where('channel', 'whatsapp')->sum('total'),
                'email'    => (int) $rowsForMonth->where('channel', 'email')->sum('total'),
            ];
        })->values();

        // ── Performance des 7 derniers jours (envoyés/livrés/échoués) ───────
        $dayLabels = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->startOfDay());

        $dailyRows = Messages::whereIn('campagnes_id', $campaignIds)
            ->where('created_at', '>=', $days->first())
            ->selectRaw('DATE(created_at) as d, status, COUNT(*) as total')
            ->groupBy('d', 'status')
            ->get();

        $weekly = $days->map(function ($day) use ($dailyRows, $dayLabels) {
            $rowsForDay = $dailyRows->where('d', $day->toDateString());
            return [
                'name'      => $dayLabels[$day->dayOfWeek],
                'delivered' => (int) $rowsForDay->where('status', 'delivered')->sum('total'),
                'sent'      => (int) $rowsForDay->whereIn('status', ['sent', 'queued'])->sum('total'),
                'failed'    => (int) $rowsForDay->where('status', 'failed')->sum('total'),
            ];
        })->values();

        // ── Répartition par canal ────────────────────────────────────────────
        $channelColors = ['sms' => '#3B82F6', 'whatsapp' => '#10B981', 'email' => '#8B5CF6', 'push' => '#F59E0B'];
        $channelRows = Messages::whereIn('campagnes_id', $campaignIds)
            ->selectRaw('channel, COUNT(*) as total')
            ->groupBy('channel')
            ->get();

        $byChannel = $channelRows->map(fn ($row) => [
            'name'  => ucfirst($row->channel),
            'value' => (int) $row->total,
            'color' => $channelColors[$row->channel] ?? '#6B7280',
        ])->values();

        // ── Violations (messages spam + campagnes rejetées) ────────────────
        $spamMessages    = Messages::whereIn('campagnes_id', $campaignIds)->where('status', 'spam')->count();
        $rejectedCampaigns = Campagnes::where('client_id', $client->id)->where('status', 'rejeter')->count();

        return response()->json([
            'evolution'  => $evolution,
            'weekly'     => $weekly,
            'by_channel' => $byChannel,
            'violations' => $spamMessages + $rejectedCampaigns,
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
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd   = now()->endOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd   = now()->subMonthNoOverflow()->endOfMonth();

        $totalMessagesSent = Messages::whereIn('status', ['sent', 'delivered'])->count();
        $messagesThisMonth = Messages::whereIn('status', ['sent', 'delivered'])
            ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
            ->count();
        $messagesLastMonth = Messages::whereIn('status', ['sent', 'delivered'])
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
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
        $revenueTotal = (float) Subscription::where('payment_status', 'paid')->sum('price');

        // ── Clients récents ───────────────────────────────────────────────────
        $recentClients = Clients::with(['subscriptions' => fn($q) =>
                $q->where('status', 'active')->with('plan')->latest()->limit(1)
            ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['id', 'company_name', 'contact_name', 'email', 'status', 'created_at', 'industry']);

        // ── Distribution abonnements par plan ─────────────────────────────────
        $subsByPlan = DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('subscriptions.status', 'active')
            ->selectRaw('subscription_plans.name, COUNT(*) as cnt')
            ->groupBy('subscription_plans.name')
            ->pluck('cnt', 'name');

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

    /**
     * Solde des contrats SMS Orange — pour alerter le super_admin avant rupture.
     * GET /api/admin/orange-sms-balance
     */
    public function orangeSmsBalance()
    {
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $lowBalanceThreshold = 5000;
        $expiringSoonDays    = 7;

        try {
            $contracts = cache()->remember('orange_sms_contracts', now()->addMinutes(15), function () {
                return app(OrangeSmsService::class)->getContracts();
            });
        } catch (\Throwable $e) {
            Log::warning('Orange SMS balance: impossible de récupérer les contrats', ['error' => $e->getMessage()]);
            return response()->json([
                'available'  => false,
                'message'    => "Impossible de contacter l'API Orange (réseau ou identifiants).",
                'contracts'  => [],
            ]);
        }

        $needsAttention = false;
        $alerts         = [];

        $formatted = collect($contracts)->map(function ($contract) use (&$needsAttention, &$alerts, $lowBalanceThreshold, $expiringSoonDays) {
            $units      = (int) ($contract['availableUnits'] ?? 0);
            $status     = $contract['status'] ?? 'UNKNOWN';
            $expiration = $contract['expirationDate'] ?? null;
            $country    = $contract['country'] ?? '?';

            $isExpired      = $status !== 'ACTIVE';
            $isLow          = $units < $lowBalanceThreshold;
            $expiresSoon    = false;

            if ($expiration) {
                $daysLeft    = now()->diffInDays(\Carbon\Carbon::parse($expiration), false);
                $expiresSoon = $daysLeft >= 0 && $daysLeft <= $expiringSoonDays;
            }

            if ($isExpired) {
                $needsAttention = true;
                $alerts[] = "Contrat Orange SMS ({$country}) expiré — rechargez immédiatement.";
            } elseif ($isLow) {
                $needsAttention = true;
                $alerts[] = "Solde SMS Orange ({$country}) faible : {$units} unités restantes.";
            } elseif ($expiresSoon) {
                $needsAttention = true;
                $alerts[] = "Contrat Orange SMS ({$country}) expire bientôt ({$expiration}).";
            }

            return [
                'country'          => $country,
                'offer_name'       => $contract['offerName'] ?? null,
                'status'           => $status,
                'available_units'  => $units,
                'expiration_date'  => $expiration,
                'is_low'           => $isLow,
                'is_expired'       => $isExpired,
                'expires_soon'     => $expiresSoon,
            ];
        })->values();

        return response()->json([
            'available'       => true,
            'needs_attention' => $needsAttention,
            'alerts'          => $alerts,
            'contracts'       => $formatted,
        ]);
    }
}
