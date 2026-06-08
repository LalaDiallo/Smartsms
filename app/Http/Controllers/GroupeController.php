<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\GroupeContact;

class GroupeController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Valider les données
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'contactIds' => 'array',
                'contactIds.*' => [
                    Rule::exists('contacts', 'id')->where('client_id', auth()->user()->client_id),
                ],
            ]);

            // Créer le groupe
            $groupe = Groupe::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'client_id'   => auth()->user()->client_id,
                'created_by'  => auth()->id(),
            ]);

            // Attacher les contacts si fournis
            if (!empty($validated['contactIds'])) {
                $groupe->contacts()->attach($validated['contactIds']);
            }

            // Charger les contacts pour la réponse
            $groupe->load('contacts:id');

            // Formater la réponse pour correspondre au frontend
            $response = [
                'id' => $groupe->id,
                'name' => $groupe->name,
                'contactIds' => $groupe->contacts->pluck('id')->toArray(),
                'createdAt' => $groupe->created_at->toISOString(),
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création du groupe',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $clientId = auth()->user()->client_id;

            // Filtrer les groupes qui ont au moins un contact du même client
            $groupeclient = Groupe::whereHas('contacts', function ($query) use ($clientId) {
                $query->where('contacts.client_id', $clientId);
            })->with(['contacts' => function ($query) use ($clientId) {
                $query->where('contacts.client_id', $clientId);
            }])->get();

            // Charger les groupes avec les IDs des contacts
            $groupes = $groupeclient->map(function ($groupe) {
                return [
                    'id' => $groupe->id,
                    'name' => $groupe->name,
                    'contactIds' => $groupe->contacts->pluck('id')->toArray(),
                    'createdAt' => $groupe->created_at->toISOString(),
                ];
            });

            return response()->json(['groups' => $groupes], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des groupes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $group = Groupe::findOrFail($id);
        $group->contacts()->detach(); // Supprime les associations
        $group->delete();
        return response()->json(['message' => 'Groupe supprimé avec succès']);
    }
}
