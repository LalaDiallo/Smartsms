<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\BrandingRequestMail;
use App\Mail\BrandingStatusMail;
use App\Models\Branding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class BrandingController extends Controller
{
    /**
     * 🔹 Récupérer le branding actif d'un client
     */
    public function show()
    {
        $user = auth()->user();

        if (!$user || !$user->client_id) {
            return response()->json([]);
        }

        $brandings = Branding::where('client_id', $user->client_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($brandings); // ✅ tableau
    }
    /**
     * 🔹 Soumettre une demande de branding (CLIENT)
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name'     => 'required|string|max:255',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048', // ← changement important
            'primary_color'  => 'nullable|string|max:7', // ex: #rrggbb
            'secondary_color'=> 'nullable|string|max:7',
            'accent_color'   => 'nullable|string|max:7',
            'font_family'    => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:2000',
        ]);

        $data = [
            'client_id'       => auth()->user()->client_id, // ou auth('client')->id() selon ton guard
            'brand_name'      => $validated['brand_name'],
            'primary_color'   => $validated['primary_color'] ?? null,
            'secondary_color' => $validated['secondary_color'] ?? null,
            'accent_color'    => $validated['accent_color'] ?? null,
            'font_family'     => $validated['font_family'] ?? null,
            'description'     => $validated['description'] ?? null,
            'status'          => 'pending',
            'is_active'       => false, // par défaut, pas actif tant qu'il n'est pas approuvé
        ];

        // Gestion de l'upload du logo
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = $request->file('logo')->store('brandings/logos', 'public');
            $data['logo'] = $path; // ou Storage::url($path) si tu veux l'URL complète
        }

        $branding = Branding::create($data);

        $superAdmins = User::where('role', 'super_admin')->get();

        foreach ($superAdmins as $admin) {
            Mail::to($admin->email)->send(
                new BrandingRequestMail($branding)
            );
        }

        return response()->json([
            'message'  => 'Demande de branding envoyée avec succès.',
            'branding' => $branding,
        ], 201);
    }

    /**
     * 🔹 Liste des demandes (ADMIN)
     */
    public function index()
    {
        $brandings = Branding::with('client')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($brandings);
    }

    /**
     * ✅ Approuver une demande (ADMIN)
     */
    public function approve($id)
    {
        $branding = Branding::findOrFail($id);

        $branding->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Branding approuvé avec succès.',
        ]);
    }

    /**
     * ❌ Refuser une demande (ADMIN)
     */
    public function reject($id)
    {
        $branding = Branding::findOrFail($id);

        if ($branding->is_default) {
            return response()->json(['message' => 'Le sender name par défaut ne peut pas être refusé.'], 403);
        }

        $branding->update(['status' => 'rejected']);

        return response()->json(['message' => 'Branding refusé.']);
    }


    public function update(Request $request, $id)
    {
        $branding = Branding::findOrFail($id);

        $validated = $request->validate([
            'status'  => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:1000', // ou required_if:status,rejected
        ]);

        $branding->update([
            'status' => $validated['status'],
            'status_motif' => $validated['comment'],
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
            'approved_by' => auth()->id(),
        ]);

        if ($branding->user && $branding->user->email) {
            Mail::to($branding->user->email)
            ->send(new BrandingStatusMail($branding));
        }

        return response()->json([
            'message' => 'Statut du branding mis à jour avec succès.',
            'branding' => $branding,
        ]);
    }

    public function activate($id)
    {
        $branding = Branding::where('id', $id)
            ->where('client_id', auth()->user()->client_id)
            ->where('status', 'approved')
            ->firstOrFail();

        DB::transaction(function () use ($branding) {
            // Désactiver tous les autres
            Branding::where('client_id', $branding->client_id)
                ->where('id', '!=', $branding->id)
                ->update(['is_active' => false]);

            // Activer celui-ci
            $branding->update(['is_active' => true]);
        });

        return response()->json(['message' => 'Branding activé']);
    }

    public function downloadPdf(int $id)
    {
        $user     = auth()->user();
        $branding = Branding::where('id', $id)
            ->where('client_id', $user->client_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $ref        = 'BR-' . now()->format('Y') . '-' . str_pad($branding->id, 6, '0', STR_PAD_LEFT);
        $verifyUrl  = config('app.url') . '/verify/' . hash_hmac('sha256', $ref, config('app.key'));
        $qrBase64   = base64_encode(QrCode::format('png')->size(120)->generate($verifyUrl));
        $clientName = $user->client?->company_name ?? $user->client?->name ?? '—';

        $pdf = Pdf::loadView('pdf.branding-certificate', [
            'branding'   => $branding,
            'ref'        => $ref,
            'qrBase64'   => $qrBase64,
            'clientName' => $clientName,
        ])->setPaper('a4', 'portrait');

        $filename = 'certificat-branding-' . Str::slug($branding->brand_name) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
