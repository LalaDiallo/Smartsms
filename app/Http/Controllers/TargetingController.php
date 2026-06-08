<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\SegmentRule;
use App\Models\Contacts;
use App\Models\SegmentAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TargetingController extends Controller
{
    /**
     * Liste des segments (groupes)
     */
    public function index(Request $request)
    {
        $segments = Groupe::where('client_id', $request->user()->client_id)
            ->withCount('contacts')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($segments);
    }

    /**
     * Création d'un segment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:static,dynamic',
            'description' => 'nullable|string',
        ]);

        $segment = Groupe::create([
            'name'        => $validated['name'],
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? null,
            'client_id'   => $request->user()->client_id,
            'created_by'  => $request->user()->id,
        ]);

        return response()->json($segment, 201);
    }

    /**
     * Détails d'un segment
     */
    public function show(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->with(['rules', 'contacts'])
            ->firstOrFail();

        return response()->json($segment);
    }

    /**
     * Ajout de contacts (segment statique)
     */
    public function attachContacts(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        abort_if($segment->type !== 'static', 400, 'Segment non statique');

        $validated = $request->validate([
            'contact_ids' => 'required|array',
        ]);

        $segment->contacts()->syncWithoutDetaching($validated['contact_ids']);

        return response()->json(['message' => 'Contacts ajoutés']);
    }

    /**
     * Suppression d'un contact d'un segment
     */
    public function detachContact(Request $request, $id, $contactId)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        $segment->contacts()->detach($contactId);

        return response()->json(['message' => 'Contact retiré']);
    }

    /**
     * Récupérer les règles d'un segment dynamique
     */
    public function getRules(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->with('rules')
            ->firstOrFail();

        return response()->json($segment->rules);
    }

    /**
     * Ajout ou mise à jour des règles (segment dynamique)
     */
    public function saveRules(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        abort_if($segment->type !== 'dynamic', 400, 'Segment non dynamique');

        $validated = $request->validate([
            'rules' => 'required|array',
        ]);

        DB::transaction(function () use ($segment, $validated, $request) {
            $oldRules = $segment->rules()->get()->toArray();

            $segment->rules()->delete();

            foreach ($validated['rules'] as $rule) {
                SegmentRule::create([
                    'groupe_id' => $segment->id,
                    'field'     => $rule['field'],
                    'operator'  => $rule['operator'],
                    'value'     => $rule['value'],
                    'logical'   => $rule['logical'] ?? 'AND',
                ]);
            }

            SegmentAudit::create([
                'groupe_id' => $segment->id,
                'user_id'   => $request->user()->id,
                'action'    => 'update_rules',
                'old_value' => $oldRules,
                'new_value' => $validated['rules'],
            ]);
        });

        return response()->json(['message' => 'Règles enregistrées']);
    }

    /**
     * Prévisualisation du ciblage (simulation)
     */
    public function preview(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->with('rules')
            ->firstOrFail();

        $query = $this->applyRules(
            Contacts::where('client_id', $request->user()->client_id)
                    ->where('status', '!=', 'NotInsert'),
            $segment->rules
        );

        return response()->json([
            'estimated_contacts' => $query->count(),
        ]);
    }

    /**
     * Mise à jour du nom / description
     */
    public function update(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $segment->update($validated);

        return response()->json($segment->fresh());
    }

    /**
     * Suppression d'un segment
     */
    public function destroy(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        $segment->delete();

        return response()->json(['message' => 'Segment supprimé']);
    }

    /**
     * Contacts du segment (paginés).
     * Statique  : membres de la table pivot.
     * Dynamique : contacts filtrés par les règles en temps réel.
     */
    public function contacts(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->with('rules')
            ->firstOrFail();

        if ($segment->type === 'static') {
            $contacts = $segment->contacts()
                ->where('status', '!=', 'NotInsert')
                ->paginate(20);
        } else {
            $contacts = $this->applyRules(
                Contacts::where('client_id', $request->user()->client_id)
                    ->where('status', '!=', 'NotInsert'),
                $segment->rules
            )->paginate(20);
        }

        return response()->json($contacts);
    }

    /**
     * Résoudre les IDs de contacts d'un segment dynamique (pour les campagnes).
     */
    public function resolve(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->with('rules')
            ->firstOrFail();

        if ($segment->type === 'static') {
            $ids = $segment->contacts()->pluck('contacts.id');
        } else {
            $ids = $this->applyRules(
                Contacts::where('client_id', $request->user()->client_id)
                    ->where('status', '!=', 'NotInsert'),
                $segment->rules
            )->pluck('id');
        }

        return response()->json(['contact_ids' => $ids, 'count' => $ids->count()]);
    }

    /**
     * Statistiques d'un segment : campagnes utilisées, taux de livraison moyen.
     */
    public function stats(Request $request, $id)
    {
        $segment = Groupe::where('id', $id)
            ->where('client_id', $request->user()->client_id)
            ->firstOrFail();

        // Campagnes ayant utilisé ce groupe
        $campaigns = DB::table('campagnes')
            ->where('client_id', $request->user()->client_id)
            ->where(function ($q) use ($segment) {
                $q->where('groupe_id', $segment->id)
                  ->orWhereRaw("JSON_CONTAINS(groupes_ids, ?)", [json_encode($segment->id)]);
            })
            ->select('id', 'name', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $campaignIds = $campaigns->pluck('id');

        $msgStats = DB::table('messages')
            ->whereIn('campagnes_id', $campaignIds)
            ->selectRaw('COUNT(*) as total, SUM(status = "delivered") as delivered, SUM(status = "failed") as failed')
            ->first();

        $total     = (int) ($msgStats->total ?? 0);
        $delivered = (int) ($msgStats->delivered ?? 0);

        return response()->json([
            'campaigns_count'  => $campaigns->count(),
            'messages_sent'    => $total,
            'delivery_rate'    => $total > 0 ? round(($delivered / $total) * 100, 1) : 0,
            'recent_campaigns' => $campaigns,
        ]);
    }

    // Colonnes autorisées pour les règles de segment (doit correspondre à la table contacts)
    private const ALLOWED_FIELDS = [
        'region', 'status', 'preferred_channel', 'timezone', 'engagement_score', 'is_spammer',
    ];

    private const ALLOWED_OPERATORS = ['=', '!=', '>', '<', '>=', '<=', 'IN', 'NOT IN', 'LIKE'];

    /**
     * Applique les règles d'un segment dynamique à une query Eloquent.
     */
    private function applyRules($query, $rules)
    {
        foreach ($rules as $rule) {
            $field    = $rule->field ?? $rule['field'] ?? null;
            $operator = strtoupper($rule->operator ?? $rule['operator'] ?? '=');
            $value    = $rule->value ?? $rule['value'] ?? '';
            $logical  = strtoupper($rule->logical ?? $rule['logical'] ?? 'AND');

            // Sécurité : ignorer les champs non autorisés
            if (!in_array($field, self::ALLOWED_FIELDS)) continue;
            if (!in_array($operator, self::ALLOWED_OPERATORS)) continue;

            $method = $logical === 'OR' ? 'orWhere' : 'where';

            if ($operator === 'IN' || $operator === 'NOT IN') {
                $vals   = is_string($value) ? json_decode($value, true) : (array) $value;
                $fn     = $operator === 'IN' ? $method . 'In' : $method . 'NotIn';
                $query->$fn($field, $vals);
            } elseif ($operator === 'LIKE') {
                $query->$method($field, 'LIKE', '%' . $value . '%');
            } else {
                $query->$method($field, $operator, $value);
            }
        }

        return $query;
    }
}
