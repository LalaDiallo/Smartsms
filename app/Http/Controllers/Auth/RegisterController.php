<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Clients;
use App\Models\SenderName;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\BillingCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\AccountActivationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email|unique:clients,email',
            'phone'      => 'required|string|max:20|unique:users,phone|unique:clients,phone',
            'company'    => 'required|string|max:255',
            'city'       => 'required|string|max:255',
            'country'    => 'required|string|max:255',
            'industry'   => 'required|string|max:255',
            'password'   => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/[A-Z]/',    // au moins une majuscule
                'regex:/[0-9]/',    // au moins un chiffre
            ],
            'consent'    => 'required|accepted',
        ], [
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule et un chiffre.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $client = Clients::create([
                    'company_name' => $request->company,
                    'contact_name' => $request->first_name . ' ' . $request->last_name,
                    'email'        => $request->email,
                    'phone'        => $request->phone,
                    'city'         => $request->city,
                    'country'      => $request->country,
                    'industry'     => $request->industry,
                    'status'       => 'essaie',
                    'joined_at'    => now(),
                    'last_activity'=> now(),
                    'satisfaction' => 0,
                    'plan_id'      => 1,
                ]);

                $activationToken = Str::random(60);

                $user = User::create([
                    'name'             => $request->first_name . ' ' . $request->last_name,
                    'email'            => $request->email,
                    'client_id'        => $client->id,
                    'role'             => 'admin',
                    'phone'            => $request->phone,
                    'password'         => Hash::make($request->password),
                    'activation_token' => $activationToken,
                    'status'           => 'inactive',
                ]);

                Mail::to($user->email)->send(new AccountActivationMail($user));

                // Sender name par défaut = code numérique unique "SMS XXXXXX"
                // Garanti unique dans la table sender_names
                do {
                    $code = 'SMS ' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                } while (SenderName::where('name', $code)->exists());

                SenderName::create([
                    'client_id'  => $client->id,
                    'name'       => $code,
                    'status'     => 'approved',
                    'is_active'  => true,
                    'is_default' => true,
                    'approved_at'=> now(),
                ]);

                $client->notificationSettings()->createMany([
                    ['name' => 'Nouvelles campagnes',   'description' => 'Email lors de la création d\'une campagne', 'enabled' => true],
                    ['name' => 'Rapports hebdomadaires','description' => 'Résumé des performances chaque lundi',      'enabled' => true],
                    ['name' => 'Campagnes terminées',   'description' => 'Notification quand une campagne se termine','enabled' => true],
                    ['name' => 'Nouveaux contacts',     'description' => 'Notification pour chaque nouveau contact',  'enabled' => true],
                ]);

                // Abonnement Freemium offert automatiquement à l'inscription
                // 50 SMS + 1 mois d'essai gratuit
                $freemiumPlan   = SubscriptionPlan::where('slug', 'freemium')->first();
                $monthlyCycle   = BillingCycle::where('months', 1)->first();

                if ($freemiumPlan && $monthlyCycle) {
                    Subscription::create([
                        'client_id'            => $client->id,
                        'subscription_plan_id' => $freemiumPlan->id,
                        'billing_cycle_id'     => $monthlyCycle->id,
                        'start_date'           => now(),
                        'end_date'             => now()->addMonth(),
                        'next_billing_date'    => now()->addMonth(),
                        'status'               => 'active',
                        'auto_renew'           => false,
                        'price'                => 0,
                        'currency'             => 'GNF',
                        'sms_quota'            => $freemiumPlan->sms_included_monthly,
                        'sms_used'             => 0,
                    ]);
                }

                return response()->json([
                    'message' => 'Inscription réussie. Veuillez vérifier votre email pour activer votre compte.',
                    'user' => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'role'  => $user->role,
                    ],
                ], 201);
            });

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'inscription.',
            ], 500);
        }
    }

    public function activate(string $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect(config('app.frontend_url') . '/login?error=invalid_token');
        }

        $user->update([
            'activation_token'  => null,
            'email_verified_at' => now(),
            'status'            => 'active',
        ]);

        return redirect(config('app.frontend_url') . '/login?activated=1');
    }
}
