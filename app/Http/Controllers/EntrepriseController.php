<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ClientActionMail;
use App\Models\Clients;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EntrepriseController extends Controller
{
    /**
     * Récupère la liste des clients avec données réelles calculées :
     * - monthly_revenue  = prix de l'abonnement actif (pas le champ statique)
     * - total_spent      = somme de tous les abonnements payés
     * - messages_sent    = count réel depuis la table messages
     * - active_subscription = objet avec quota SMS, expiration, statut
     */
    public function clients(Request $request)
    {
        // Cache uniquement sur la liste complète (sans filtre actif)
        $hasFilter = $request->filled('search') || $request->filled('status') || $request->filled('plan');
        $cacheKey  = 'enterprise:clients';

        if (!$hasFilter) {
            $cached = cache()->get($cacheKey);
            if ($cached) {
                return response()->json($cached);
            }
        }

        $query = Clients::query()->with('plan');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('industry', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan') && $request->plan !== 'all') {
            $query->whereHas('plan', fn($q) => $q->where('name', 'LIKE', "%{$request->plan}%"));
        }

        $query->orderBy('joined_at', 'desc');
        $clients    = $query->get();
        $clientIds  = $clients->pluck('id');

        // ── 1. Abonnements actifs (MRR réel) ─────────────────────────────────
        $activeSubs = Subscription::whereIn('client_id', $clientIds)
            ->where('status', 'active')
            ->with('plan:id,name,slug')
            ->orderByDesc('created_at')
            ->get()
            ->keyBy('client_id');   // 1 abonnement actif par client max

        // ── 2. Total dépensé = somme de tous les abonnements ─────────────────
        $totalSpent = Subscription::whereIn('client_id', $clientIds)
            ->select('client_id', DB::raw('SUM(price) as total'))
            ->groupBy('client_id')
            ->pluck('total', 'client_id');

        // ── 3. Messages envoyés réels (via campagnes → messages) ──────────────
        $msgCounts = DB::table('messages')
            ->join('campagnes', 'campagnes.id', '=', 'messages.campagnes_id')
            ->whereIn('campagnes.client_id', $clientIds)
            ->whereNull('campagnes.deleted_at')
            ->select('campagnes.client_id', DB::raw('COUNT(messages.id) as cnt'))
            ->groupBy('campagnes.client_id')
            ->pluck('cnt', 'client_id');

        // ── Enrichissement de chaque client ───────────────────────────────────
        $enriched = $clients->map(function ($client) use ($activeSubs, $totalSpent, $msgCounts) {
            $sub  = $activeSubs->get($client->id);
            $data = $client->toArray();

            // Données financières réelles
            $data['monthly_revenue'] = $sub ? (float) $sub->price : 0;
            $data['total_spent']     = (float) ($totalSpent->get($client->id) ?? 0);
            $data['messages_sent']   = (int)   ($msgCounts->get($client->id) ?? 0);

            // Abonnement actif détaillé pour le panneau client et l'Analytics
            $data['active_subscription'] = $sub ? [
                'id'         => $sub->id,
                'status'     => $sub->status,
                'end_date'   => $sub->end_date,
                'sms_quota'  => $sub->sms_quota,
                'sms_used'   => $sub->sms_used,
                'auto_renew' => $sub->auto_renew,
                'plan_name'  => $sub->plan?->name ?? $sub->plan?->slug,
                'plan_slug'  => $sub->plan?->slug,
                'price'      => $sub->price,
            ] : null;

            return $data;
        });

        $result = ['success' => true, 'data' => $enriched, 'total' => $enriched->count()];

        // Mettre en cache uniquement si pas de filtre — 2 minutes
        if (!$hasFilter) {
            cache()->put($cacheKey, $result, now()->addMinutes(2));
        }

        return response()->json($result);
    }

    /**
     * Récupère la liste des plans avec leurs fonctionnalités
     * Utilisé par l'onglet Plans
     */
    public function plans()
    {
        // Plans changent rarement → cache 10 minutes
        $plans = cache()->remember('enterprise:plans', now()->addMinutes(10), function () {
            return Plan::with('features')
                ->where('is_active', true)
                ->get()
                ->map(function ($plan) {
                    return [
                        'id'                    => $plan->id,
                        'name'                  => $plan->name,
                        'price'                 => $plan->price,
                        'currency'              => $plan->currency,
                        'included_sms_volume'   => $plan->included_sms_volume,
                        'overuse_price_per_sms' => $plan->overuse_price_per_sms,
                        'limitations'           => $plan->limitations,
                        'clientsCount'          => Clients::where('plan_id', $plan->id)->count(),
                        'features'              => $plan->features->map(fn ($f) => [
                            'id'      => $f->id,
                            'feature' => $f->feature,
                            'enabled' => $f->enabled,
                        ]),
                    ];
                });
        });

        return response()->json(['success' => true, 'data' => $plans]);
    }

    /**
     * Détails d'un client spécifique (pour le modal)
     */
    public function showClient(int $id)
    {
        $client = Clients::with('plan')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $client,
        ]);
    }

    public function sendClientActionEmail(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'action'    => 'required|in:contact,suspend,activate,terminate',
            'subject'   => 'required|string|max:255',
            'message'   => 'required|string',
        ]);

        $client = Clients::findOrFail($validated['client_id']);

        Mail::to($client->email)->send(new ClientActionMail(
            $client,
            $validated['action'],
            $validated['subject'],
            $validated['message']
        ));

        $data = ['last_activity' => now()];

        switch ($validated['action']) {
            case 'suspend':
                $data['status'] = 'suspended';
                break;
            case 'activate':
                $data['status'] = 'active';
                break;
            case 'terminate':
                $data['status'] = 'resilie';
                break;
        }

        $client->update($data);

        // Invalider le cache clients après modification
        cache()->forget('enterprise:clients');

        return response()->json([
            'success' => true,
            'message' => 'Email envoyé et statut mis à jour avec succès',
            'status'  => $data['status'] ?? $client->status,
        ]);
    }
}
