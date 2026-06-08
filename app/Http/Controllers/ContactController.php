<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Messages;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $client = Auth::user()->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $query = Contacts::where('client_id', $client->id)
            ->where('status', '!=', 'NotInsert');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $contacts = $query->orderByDesc('created_at')->get();

        return response()->json(['contacts' => $contacts]);
    }

    public function show(int $id)
    {
        $client = Auth::user()->client;

        $contact = Contacts::with(['messages', 'responses', 'groupes'])
            ->where('client_id', $client->id)
            ->findOrFail($id);

        $campaigns = $contact->messages()
            ->whereNotNull('campagnes_id')
            ->with('campaign')
            ->get()
            ->pluck('campaign')
            ->unique('id')
            ->values();

        return response()->json([
            'contact'   => $contact,
            'campaigns' => $campaigns,
            'messages'  => $contact->messages,
            'responses' => $contact->responses,
            'groupes'   => $contact->groupes,
        ]);
    }

    public function store(Request $request)
    {
        $client = Auth::user()->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20|unique:contacts,phone',
            'email'             => 'nullable|email|max:255|unique:contacts,email',
            'region'            => 'nullable|string|max:255',
            'preferred_channel' => 'required|in:email,sms,whatsapp,push',
            'gender'            => 'nullable|in:male,female,other',
            'age'               => 'nullable|integer|min:1|max:120',
            'language'          => 'nullable|string|max:10',
            'country'           => 'nullable|string|max:100',
            'is_spammer'        => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data             = $validator->validated();
        $data['client_id']= $client->id;
        $data['status']   = 'active';

        // Si le contact existe déjà en NotInsert (créé via import campagne),
        // on le "promeut" en contact actif au lieu de rejeter avec une erreur doublon
        $existing = Contacts::where('client_id', $client->id)
            ->where('status', 'NotInsert')
            ->where(function ($q) use ($data) {
                if (!empty($data['phone'])) $q->orWhere('phone', $data['phone']);
                if (!empty($data['email'])) $q->orWhere('email', $data['email']);
            })
            ->first();

        if ($existing) {
            $existing->update(array_merge($data, ['status' => 'active']));
            ActivityLogger::log('contact.created', ['name' => trim($data['first_name'] . ' ' . $data['last_name'])], 'contact', $existing->id);
            return response()->json(['contact' => $existing->fresh()], 201);
        }

        $contact = Contacts::create($data);

        ActivityLogger::log('contact.created', ['name' => trim($data['first_name'] . ' ' . $data['last_name'])], 'contact', $contact->id);

        return response()->json(['contact' => $contact], 201);
    }

    public function update(Request $request, int $id)
    {
        $client  = Auth::user()->client;
        $contact = Contacts::where('client_id', $client->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'nullable|email|unique:contacts,email,' . $id,
            'phone'             => 'nullable|string|unique:contacts,phone,' . $id,
            'region'            => 'nullable|string|max:255',
            'preferred_channel' => 'nullable|in:email,sms,whatsapp,push',
            'gender'            => 'nullable|in:male,female,other',
            'age'               => 'nullable|integer|min:1|max:120',
            'language'          => 'nullable|string|max:10',
            'country'           => 'nullable|string|max:100',
            'is_spammer'        => 'boolean',
            'status'            => 'nullable|in:active,inactive,employes',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contact->update($validator->validated());

        ActivityLogger::log('contact.updated', ['name' => $contact->first_name . ' ' . $contact->last_name], 'contact', $contact->id);

        return response()->json(['contact' => $contact]);
    }

    public function destroy(int $id)
    {
        $client  = Auth::user()->client;
        $contact = Contacts::where('client_id', $client->id)->findOrFail($id);

        $name = $contact->first_name . ' ' . $contact->last_name;
        $contact->delete();

        ActivityLogger::log('contact.deleted', ['name' => $name], 'contact', $id);

        return response()->json(['message' => 'Contact supprimé avec succès']);
    }

    public function bulkStore(Request $request)
    {
        $client = Auth::user()->client;

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $request->validate([
            'contacts'                  => 'required|array|min:1|max:500',
            'contacts.*.first_name'     => 'required|string|max:255',
            'contacts.*.last_name'      => 'required|string|max:255',
            'contacts.*.phone'          => 'nullable|string|max:20',
            'contacts.*.email'          => 'nullable|email|max:255',
            'contacts.*.preferred_channel' => 'nullable|in:email,sms,whatsapp,push',
        ]);

        $now      = now();
        $clientId = $client->id;
        $toInsert = collect($request->contacts)->map(fn ($c) => array_merge($c, [
            'client_id'         => $clientId,
            'status'            => 'active',
            'preferred_channel' => $c['preferred_channel'] ?? 'sms',
            'created_at'        => $now,
            'updated_at'        => $now,
        ]))->toArray();

        collect($toInsert)->chunk(500)->each(fn ($chunk) => Contacts::insert($chunk->toArray()));

        return response()->json([
            'message' => count($toInsert) . ' contact(s) importé(s) avec succès',
        ], 201);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $user = Auth::user();
        $file = $request->file('file');
        $expectedHeaders = ['first_name', 'last_name', 'phone', 'email', 'region'];
        $errors = [];
        $contactsToInsert = [];

        $content = file_get_contents($file->getRealPath());
        if (!mb_check_encoding($content, 'UTF-8')) {
            return response()->json(['message' => 'Le fichier doit être encodé en UTF-8.'], 422);
        }

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return response()->json(['message' => 'Impossible d\'ouvrir le fichier.'], 500);
        }

        $header = fgetcsv($handle, 0, ',');
        if ($header !== $expectedHeaders) {
            fclose($handle);
            return response()->json([
                'message' => 'En-tête invalide. Attendu : ' . implode(',', $expectedHeaders),
            ], 422);
        }

        $lineNumber = 1;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $lineNumber++;
            $data = array_combine($expectedHeaders, array_pad($row, count($expectedHeaders), null));

            $validator = Validator::make($data, [
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'phone'      => 'required|string|max:20|regex:/^\+\d{7,15}$/',
                'email'      => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                $errors[] = "Ligne {$lineNumber} : " . implode(', ', $validator->errors()->all());
                continue;
            }

            $contactsToInsert[] = [
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'phone'             => $data['phone'],
                'email'             => $data['email'] ?: null,
                'region'            => $data['region'] ?: null,
                'preferred_channel' => 'sms',
                'client_id'         => $user->client_id,
                'status'            => 'active',
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }
        fclose($handle);

        // Dédupliquer par téléphone et email existants
        if (!empty($contactsToInsert)) {
            $phones = array_filter(array_column($contactsToInsert, 'phone'));
            $emails = array_filter(array_column($contactsToInsert, 'email'));

            $existing = Contacts::where('client_id', $user->client_id)
                ->where(function ($q) use ($phones, $emails) {
                    if ($phones) $q->whereIn('phone', $phones);
                    if ($emails) $q->orWhereIn('email', $emails);
                })
                ->get(['phone', 'email'])
                ->flatMap(fn ($c) => array_filter([$c->phone, $c->email]))
                ->flip()
                ->toArray();

            // Récupérer les contacts NotInsert existants pour pouvoir les promouvoir
            $notInsertByPhone = Contacts::where('client_id', $user->client_id)
                ->where('status', 'NotInsert')
                ->whereIn('phone', array_filter(array_column($contactsToInsert, 'phone')))
                ->get()->keyBy('phone');

            $contactsToPromote = [];

            $contactsToInsert = array_filter($contactsToInsert, function ($c) use ($existing, $notInsertByPhone, &$errors, &$contactsToPromote) {
                $phone = $c['phone'] ?? null;
                $email = $c['email'] ?? null;

                // Contact NotInsert → on le promeut en active (pas un vrai doublon)
                if ($phone && isset($notInsertByPhone[$phone])) {
                    $contactsToPromote[] = ['id' => $notInsertByPhone[$phone]->id, 'data' => $c];
                    return false; // ne pas ré-insérer
                }

                // Vrai doublon (contact déjà actif/inactif) → erreur
                if (isset($existing[$phone]) || ($email && isset($existing[$email]))) {
                    $errors[] = "Contact {$c['first_name']} {$c['last_name']} ({$phone}) existe déjà.";
                    return false;
                }

                return true;
            });

            // Promouvoir les NotInsert en active
            foreach ($contactsToPromote as $promo) {
                Contacts::where('id', $promo['id'])->update(array_merge($promo['data'], ['status' => 'active']));
            }
        }

        if (!empty($contactsToInsert)) {
            DB::transaction(function () use ($contactsToInsert) {
                collect($contactsToInsert)->chunk(500)->each(
                    fn ($chunk) => Contacts::insert($chunk->toArray())
                );
            });
        }

        $imported = count($contactsToInsert);

        return response()->json([
            'message'  => "{$imported} contact(s) importé(s) avec succès.",
            'imported' => $imported,
            'errors'   => $errors,
        ], empty($errors) ? 200 : 422);
    }

    public function export(Request $request)
    {
        $client = Auth::user()->client;

        $ids = $request->input('ids');

        $query = Contacts::where('client_id', $client->id);

        if ($ids && is_array($ids)) {
            $query->whereIn('id', $ids);
        }

        $contacts = $query->get(['first_name', 'last_name', 'email', 'phone', 'region', 'status']);

        $csv  = "Prénom,Nom,Email,Téléphone,Région,Statut\n";
        foreach ($contacts as $contact) {
            $csv .= implode(',', [
                $contact->first_name,
                $contact->last_name,
                $contact->email  ?? '',
                $contact->phone  ?? '',
                $contact->region ?? '',
                $contact->status,
            ]) . "\n";
        }

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function sendSmsToContact(Request $request)
    {
        $client = Auth::user()->client;

        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'content'    => 'required|string|max:200',
            'channel'    => 'required|in:sms,whatsapp,email,push',
            'subject'    => 'nullable|required_if:channel,email|string|max:255',
        ]);

        $contact = Contacts::where('client_id', $client->id)
            ->findOrFail($request->contact_id);

        $status  = 'sent';
        $errMsg  = null;

        switch ($request->channel) {

            case 'sms':
                if (empty($contact->phone)) {
                    return response()->json(['message' => 'Ce contact n\'a pas de numéro de téléphone.'], 422);
                }
                try {
                    app(\App\Services\OrangeSmsService::class)->send($contact->phone, $request->content);
                } catch (\Throwable $e) {
                    Log::error('sendSmsToContact : échec SMS', [
                        'contact_id' => $contact->id,
                        'phone'      => $contact->phone,
                        'error'      => $e->getMessage(),
                    ]);
                    $status = 'failed';
                    $errMsg = 'Échec de l\'envoi du SMS. Vérifiez le numéro ou votre solde Orange.';
                }
                break;

            case 'email':
                if (empty($contact->email)) {
                    return response()->json(['message' => 'Ce contact n\'a pas d\'adresse email.'], 422);
                }
                try {
                    Mail::raw($request->content, function ($mail) use ($contact, $request) {
                        $mail->to($contact->email)
                             ->subject($request->subject ?? config('app.name') . ' — Message');
                    });
                } catch (\Throwable $e) {
                    Log::error('sendSmsToContact : échec email', [
                        'contact_id' => $contact->id,
                        'email'      => $contact->email,
                        'error'      => $e->getMessage(),
                    ]);
                    $status = 'failed';
                    $errMsg = 'Échec de l\'envoi de l\'email. Vérifiez la configuration SMTP.';
                }
                break;

            case 'whatsapp':
                if (empty($contact->phone)) {
                    return response()->json(['message' => 'Ce contact n\'a pas de numéro de téléphone.'], 422);
                }
                try {
                    app(\App\Services\WhatsAppService::class)->send($contact->phone, $request->content);
                } catch (\Throwable $e) {
                    Log::error('sendSmsToContact : échec WhatsApp', [
                        'contact_id' => $contact->id,
                        'phone'      => $contact->phone,
                        'error'      => $e->getMessage(),
                    ]);
                    $status = 'failed';
                    $errMsg = $e->getMessage();
                }
                break;

            case 'push':
                // TODO: intégrer Firebase FCM ou autre provider push
                Log::info('sendSmsToContact : Push non encore intégré', [
                    'contact_id' => $contact->id,
                ]);
                $status = 'pending';
                break;
        }

        $message = Messages::create([
            'campagnes_id' => null,
            'contact_id'   => $contact->id,
            'content'      => $request->content,
            'sent_at'      => now(),
            'status'       => $status,
            'channel'      => $request->channel,
            'subject'      => $request->subject ?? null,
        ]);

        if ($status === 'failed') {
            return response()->json([
                'message' => $errMsg,
                'data'    => $message,
            ], 502);
        }

        if ($status === 'pending') {
            return response()->json([
                'message' => 'Canal ' . $request->channel . ' non encore intégré — message enregistré.',
                'data'    => $message,
            ]);
        }

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data'    => $message,
        ]);
    }
}
