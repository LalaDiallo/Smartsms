<?php

namespace App\Http\Controllers;

use App\Models\SenderName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    // Demande d'un nouveau sender name avec formulaire complet
    public function store(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:11',
            'type_client'             => 'required|in:particulier,entreprise',
            // Particulier
            'sous_type'               => 'nullable|in:particulier,etudiant,freelance',
            'nom_complet'             => 'required_if:type_client,particulier|nullable|string|max:255',
            'cni_numero'              => 'required_if:type_client,particulier|nullable|string|max:100',
            'categorie_sender'        => 'required|nullable|in:projet_startup,freelance_service,evenementiel,test_developpement,institutionnel,promotionnel,transactionnel,operationnel',
            'adresse'                 => 'nullable|string|max:500',
            // Entreprise
            'raison_sociale'          => 'required_if:type_client,entreprise|nullable|string|max:255',
            'forme_juridique'         => 'nullable|string|max:100',
            'rccm'                    => 'required_if:type_client,entreprise|nullable|string|max:100',
            'nif'                     => 'required_if:type_client,entreprise|nullable|string|max:100',
            'nom_responsable'         => 'required_if:type_client,entreprise|nullable|string|max:255',
            'sender_name_alt1'        => 'nullable|string|max:11',
            'sender_name_alt2'        => 'nullable|string|max:11',
            'marque_deposee'          => 'nullable|in:oui,non',
            'numero_depot'            => 'nullable|string|max:100',
            // Communs
            'telephone'               => 'nullable|string|max:20',
            'email'                   => 'nullable|email|max:255',
            // Pièces jointes (max 5 Mo chacune)
            'piece_identite'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_domicile'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_activite'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_sms_samples'       => 'required_if:type_client,particulier|nullable|file|mimes:pdf,doc,docx|max:5120',
            'piece_rccm'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_nif'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_statuts'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_marque'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Engagement particulier (déclaration unique — 10 points)
            'engagement_declaration_perso' => $request->type_client === 'particulier' ? ['required', 'accepted'] : ['nullable'],
            // Engagement entreprise (déclaration unique — 7 points)
            'engagement_declaration_ent'   => $request->type_client === 'entreprise'  ? ['required', 'accepted'] : ['nullable'],
        ]);

        $clientId = Auth::user()->client_id;

        $exists = SenderName::where('client_id', $clientId)
            ->where('name', $request->name)
            ->whereIn('status', ['pending_document', 'pending', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce sender name existe déjà ou est en cours de validation.',
            ], 422);
        }

        // Stocker les pièces jointes
        $pieces = [];
        foreach (['piece_identite', 'piece_domicile', 'piece_activite', 'piece_sms_samples', 'piece_rccm', 'piece_nif', 'piece_statuts', 'piece_marque'] as $field) {
            if ($request->hasFile($field)) {
                $pieces[$field] = $request->file($field)->store(
                    'sender-documents/' . $clientId,
                    'public'
                );
            }
        }

        $metadata = [
            'type_client'      => $request->type_client,
            'sous_type'        => $request->sous_type,
            'nom_complet'      => $request->nom_complet,
            'adresse'          => $request->adresse,
            'cni_numero'       => $request->cni_numero,
            'categorie_sender' => $request->categorie_sender,
            'raison_sociale'   => $request->raison_sociale,
            'forme_juridique'  => $request->forme_juridique,
            'rccm'             => $request->rccm,
            'nif'              => $request->nif,
            'nom_responsable'  => $request->nom_responsable,
            'sender_name_alt1' => $request->sender_name_alt1,
            'sender_name_alt2' => $request->sender_name_alt2,
            'marque_deposee'   => $request->marque_deposee,
            'numero_depot'     => $request->numero_depot,
            'telephone'        => $request->telephone,
            'email'            => $request->email,
            'pieces_jointes'   => $pieces,
            'engagements'      => $request->type_client === 'particulier'
                ? ['declaration_perso' => true]
                : ['declaration_ent'   => true],
        ];

        $sender = SenderName::create([
            'client_id'  => $clientId,
            'name'       => $request->name,
            'metadata'   => $metadata,
            // pending_document = formulaire signé pas encore uploadé
            'status'     => 'pending_document',
            'is_active'  => false,
            'is_default' => false,
        ]);

        return response()->json([
            'message' => 'Demande créée. Veuillez télécharger, signer et uploader le formulaire.',
            'data'    => $sender,
        ], 201);
    }

    // Upload du formulaire signé (étape 2)
    public function uploadDocument(Request $request, int $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $clientId = Auth::user()->client_id;

        $sender = SenderName::where('id', $id)
            ->where('client_id', $clientId)
            ->where('status', 'pending_document')
            ->firstOrFail();

        $documentPath = $request->file('document')->store(
            'sender-documents/' . $clientId,
            'public'
        );

        $sender->update([
            'document_path' => $documentPath,
            'status'        => 'pending',
        ]);

        return response()->json([
            'message' => 'Document uploadé. Votre demande est maintenant en attente de validation.',
            'data'    => $sender->fresh(),
        ]);
    }

    // Choisir un sender name approuvé comme défaut (désactive les autres)
    public function activate(int $id)
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
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $senderNames]);
    }

    // Approuver + basculer en sender name par défaut
    public function approve(int $id)
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
    public function reject(Request $request, int $id)
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

    public function downloadPdf(int $id)
    {
        $user   = Auth::user();
        $sender = SenderName::where('id', $id)
            ->where('client_id', $user->client_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $ref       = 'SN-' . now()->format('Y') . '-' . str_pad($sender->id, 6, '0', STR_PAD_LEFT);
        $verifyUrl = config('app.url') . '/verify/' . hash_hmac('sha256', $ref, config('app.key'));
        $qrBase64  = base64_encode(QrCode::format('png')->size(120)->generate($verifyUrl));
        $clientName = $user->client?->company_name ?? $user->client?->name ?? '—';

        $pdf = Pdf::loadView('pdf.sender-name-attestation', [
            'senderName' => $sender,
            'ref'        => $ref,
            'qrBase64'   => $qrBase64,
            'clientName' => $clientName,
        ])->setPaper('a4', 'portrait');

        $filename = 'attestation-sender-name-' . \Illuminate\Support\Str::slug($sender->name) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
