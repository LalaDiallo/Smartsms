<?php

namespace App\Http\Controllers;

use App\Models\SenderName;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SenderNameController extends Controller
{
    // Liste des sender names du client connecté
    public function index()
    {
        $senderNames = SenderName::where('client_id', Auth::user()->client_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $senderNames]);
    }

    // Demande d'un nouveau sender name custom
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $clientId = Auth::user()->client_id;

        $exists = SenderName::where('client_id', $clientId)
            ->where('name', $request->name)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce sender name existe déjà ou est en cours de validation.',
            ], 422);
        }

        $sender = SenderName::create([
            'client_id'  => $clientId,
            'name'       => $request->name,
            'status'     => 'pending',
            'is_active'  => false,
            'is_default' => false,
        ]);

        return response()->json([
            'message' => 'Demande de sender name envoyée. En attente d\'approbation.',
            'data'    => $sender,
        ], 201);
    }

    // Choisir un sender name approuvé comme défaut (désactive les autres)
    public function activate($id)
    {
        $clientId = Auth::user()->client_id;

        $sender = SenderName::where('id', $id)
            ->where('client_id', $clientId)
            ->where('status', 'approved')
            ->firstOrFail();

        DB::transaction(function () use ($sender, $clientId) {
            // Retirer défaut et actif de tous les autres
            SenderName::where('client_id', $clientId)
                ->where('id', '!=', $sender->id)
                ->update(['is_default' => false, 'is_active' => false]);

            // Définir celui-ci comme défaut actif
            $sender->update(['is_active' => true, 'is_default' => true]);
        });

        return response()->json([
            'message' => "Sender name \"{$sender->name}\" défini comme sender par défaut.",
            'data'    => $sender->fresh(),
        ]);
    }

    // ── ADMIN ──────────────────────────────────────────────────────────────

    // Liste toutes les demandes (admin)
    public function adminIndex()
    {
        $senderNames = SenderName::with('client')
            ->where('is_default', false)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $senderNames]);
    }

    // Approuver + basculer en sender name par défaut
    public function approve($id)
    {
        $sender = SenderName::findOrFail($id);

        DB::transaction(function () use ($sender) {
            // 1. Retirer le statut défaut et actif de tous les autres sender names du client
            SenderName::where('client_id', $sender->client_id)
                ->where('id', '!=', $sender->id)
                ->update(['is_default' => false, 'is_active' => false]);

            // 2. Approuver et définir comme nouveau défaut
            $sender->update([
                'status'       => 'approved',
                'approved_at'  => now(),
                'approved_by'  => Auth::id(),
                'status_motif' => null,
                'is_active'    => true,
                'is_default'   => true,
            ]);
        });

        return response()->json([
            'message' => "Sender name \"{$sender->name}\" approuvé et défini comme sender par défaut.",
            'data'    => $sender->fresh(),
        ]);
    }

    // Rejeter
    public function reject(Request $request, $id)
    {
        $sender = SenderName::findOrFail($id);

        if ($sender->is_default) {
            return response()->json(['message' => 'Le sender name par défaut ne peut pas être rejeté.'], 403);
        }

        $request->validate(['reason' => 'nullable|string|max:500']);

        $sender->update([
            'status'       => 'rejected',
            'status_motif' => $request->reason,
        ]);

        return response()->json([
            'message' => "Sender name \"{$sender->name}\" rejeté.",
            'data'    => $sender,
        ]);
    }
}
