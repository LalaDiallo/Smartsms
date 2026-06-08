<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\OtpCode;
use App\Models\UserSession;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Notifications\CustomResetPassword;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOtpMail;

class LoginController extends Controller
{

    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
        'device_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // 1️⃣ Vérifier l'utilisateur
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Identifiants invalides.',
        ], 401);
    }

    // 2️⃣ Vérifier compte actif ou email vérifié
    if (!$user->hasVerifiedEmail() && $user->status !== 'active') {
        return response()->json([
            'message' => 'Votre compte n\'est pas encore activé. Vérifiez votre email et cliquez sur le lien d\'activation.',
        ], 403);
    }

    // 2b️⃣ Bloquer les comptes suspendus / inactifs explicitement
    if ($user->status === 'suspended') {
        return response()->json([
            'message' => 'Votre compte a été suspendu. Contactez l\'administrateur.',
        ], 403);
    }

    // 3️⃣ Vérifier si session existe AVANT création
    $existingSession = UserSession::where('user_id', $user->id)
        ->where('device_id', $request->device_id)
        ->first();

    // 4️⃣ OTP si nouvel appareil ou 2FA
    if (!$existingSession || $user->two_factor_enabled) {

        $code = rand(100000, 999999);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new LoginOtpMail($code));

        return response()->json([
            'requires_otp' => true,
            'user_id' => $user->id,
        ]);
    }

    // 5️⃣ Créer / MAJ session
    $agent = new Agent();

    UserSession::updateOrCreate(
        [
            'user_id' => $user->id,
            'device_id' => $request->device_id,
        ],
        [
            'device_name' => $agent->device(),
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'ip_address' => $request->ip(),
            'last_activity_at' => now(),
            'is_current' => true,
        ]
    );

    // 6️⃣ Générer token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Connexion réussie.',
        'user' => [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'role'      => $user->role,
            'client_id' => $user->client_id,
            'avatar'    => $user->profil ? asset('storage/' . $user->profil) : null,
        ],
        'token' => $token,
    ], 200);
}


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string',
            'device_id' => 'required',
        ]);

        $otp = OtpCode::where('user_id', $request->user_id)
            ->where('code', $request->code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Code invalide ou expiré'], 422);
        }

        $otp->update(['used_at' => now()]);

        $user = User::findOrFail($request->user_id);

        // 🔐 Enregistrer le device maintenant
        $agent = new Agent();

        UserSession::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $request->device_id,
            ],
            [
                'device_name' => $agent->device(),
                'browser' => $agent->browser(),
                'os' => $agent->platform(),
                'ip_address' => $request->ip(),
                'last_activity_at' => now(),
                'is_current' => true,
            ]
        );

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'client_id' => $user->client_id,
                'avatar'    => $user->profil ? asset('storage/' . $user->profil) : null,
            ],
            'token' => $token,
        ], 200);
    }


    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Aucun utilisateur authentifié.',
                ], 401);
            }

            // Journaliser la déconnexion (pour super_admin avec can_generate_logs)
            if ($user->hasPermission('can_generate_logs')) {
                Log::info('Déconnexion réussie', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip(),
                    'timestamp' => now(),
                ]);
            }

            // Révoquer tous les jetons de l'utilisateur
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la déconnexion.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // app/Http/Controllers/AuthController.php
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => "L'email est requis.",
            'email.email' => "L'email n'est pas valide.",
            'email.exists' => 'Aucun compte trouvé avec cet email.',
        ]);

        try {
            $status = Password::sendResetLink($request->only('email'));

            switch ($status) {
                case Password::RESET_LINK_SENT:
                    return response()->json(['message' => 'Lien de réinitialisation envoyé à votre email.'], 200);
                case Password::RESET_THROTTLED:
                    return response()->json(['message' => 'Trop de tentatives. Veuillez réessayer dans quelques minutes.'], 429);
                default:
                    return response()->json(['message' => "Impossible d'envoyer le lien de réinitialisation. Veuillez réessayer."], 500);
            }
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'envoi du lien de réinitialisation: " . $e->getMessage());
            return response()->json(['message' => "Erreur serveur lors de l'envoi du lien. Contactez le support."], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Réinitialiser le mot de passe
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    // Révoquer tous les jetons existants
                    $user->tokens()->delete();

                    // Journaliser la réinitialisation (pour super_admin avec can_generate_logs)
                    if ($user->hasPermission('peut_gerer_budget')) {
                        Log::info('Mot de passe réinitialisé', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'role' => $user->role,
                            'ip' => request()->ip(),
                            'timestamp' => now(),
                        ]);
                    }
                }
            );

            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => 'Mot de passe réinitialisé avec succès.'], 200)
                : response()->json(['message' => 'Lien de réinitialisation invalide ou expiré.'], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la réinitialisation.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // public function forgotPassword(Request $request)
    // {
    //     // Validation personnalisée
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //     ], [
    //         'email.required' => 'L'email est requis.',
    //         'email.email' => 'L'email n'est pas valide.',
    //         'email.exists' => 'Aucun compte trouvé avec cet email.',
    //     ]);

    //     // Récupérer l'utilisateur
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json(['message' => 'Aucun compte trouvé avec cet email.'], 404);
    //     }

    //     // Ici tu peux mettre ta logique spécifique avant d'envoyer le lien
    //     // Exemple : vérifier si le compte est actif
    //     if (!$user->is_active) {
    //         return response()->json(['message' => 'Votre compte n'est pas actif.'], 403);
    //     }

    //     try {
    //         // Générer le token de réinitialisation
    //         $token = Password::createToken($user);

    //         // Envoyer la notification personnalisée
    //         $user->notify(new ResetPasswordNotification($token));

    //         return response()->json(['message' => 'Lien de réinitialisation envoyé à votre email.'], 200);
    //     } catch (\Exception $e) {
    //         \Log::error('Erreur lors de l\'envoi du lien de réinitialisation: ' . $e->getMessage());
    //         return response()->json(['message' => 'Impossible d'envoyer le lien de réinitialisation. Veuillez réessayer.'], 500);
    //     }
    // }

    // public function resetPassword(Request $request)
    // {
    //     // Validation des données
    //     $validator = Validator::make($request->all(), [
    //         'token' => 'required|string',
    //         'email' => 'required|string|email|max:255|exists:users,email',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     try {
    //         // Réinitialiser le mot de passe
    //         $status = Password::reset(
    //             $request->only('email', 'password', 'password_confirmation', 'token'),
    //             function ($user, $password) {
    //                 $user->forceFill([
    //                     'password' => Hash::make($password),
    //                     'remember_token' => Str::random(60),
    //                 ])->save();

    //                 // Révoquer tous les jetons existants
    //                 $user->tokens()->delete();

    //                 Log::info('Mot de passe réinitialisé', [
    //                         'user_id' => $user->id,
    //                         'email' => $user->email,
    //                         'role' => $user->role,
    //                         'ip' => request()->ip(),
    //                         'timestamp' => now(),
    //                     ]);
    //             }
    //         );

    //         return $status === Password::PASSWORD_RESET
    //             ? response()->json(['message' => 'Mot de passe réinitialisé avec succès.'], 200)
    //             : response()->json(['message' => 'Lien de réinitialisation invalide ou expiré.'], 400);

    //         redirect(config('app.frontend_url'));
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'message' => 'Une erreur est survenue lors de la réinitialisation.',
    //             'error' => $th->getMessage(),
    //         ], 500);
    //     }
    // }
}
