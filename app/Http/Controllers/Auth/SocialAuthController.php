<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\BillingCycle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'facebook', 'github', 'linkedin-openid'];

    /**
     * Redirige vers le fournisseur OAuth.
     * GET /auth/{provider}/redirect
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, self::PROVIDERS)) {
            return response()->json(['message' => 'Fournisseur non supporté.'], 400);
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Traite le callback OAuth et retourne un token Sanctum.
     * GET /auth/{provider}/callback
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, self::PROVIDERS)) {
            return $this->redirectWithError('Fournisseur non supporté.');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            return $this->redirectWithError("Échec de l'authentification via {$provider}.");
        }

        $email = $socialUser->getEmail();

        if (!$email) {
            return $this->redirectWithError("Aucune adresse email retournée par {$provider}.");
        }

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

            // Chercher un utilisateur existant
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Email inconnu → redirection vers l'inscription avec données pré-remplies
                $nameParts = explode(' ', $socialUser->getName() ?? '', 2);
                $params = http_build_query([
                    'view'       => 'register',
                    'email'      => $email,
                    'first_name' => $nameParts[0] ?? '',
                    'last_name'  => $nameParts[1] ?? '',
                    'avatar'     => $socialUser->getAvatar() ?? '',
                    'provider'   => str_replace('-openid', '', $provider),
                ]);
                return redirect("{$frontendUrl}/?{$params}");
            }

            // Utilisateur existant → mise à jour avatar si absent
            if (!$user->avatar && $socialUser->getAvatar()) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }

            // Créer le token Sanctum
            $token = $user->createToken('social-auth')->plainTextToken;

            $userData = urlencode(json_encode([
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'avatar'=> $user->avatar,
            ]));

            return redirect("{$frontendUrl}/auth/callback?token={$token}&user={$userData}");

        } catch (\Throwable $e) {
            \Log::error('SocialAuth callback error', ['error' => $e->getMessage(), 'provider' => $provider]);
            return $this->redirectWithError("Erreur lors de la connexion.");
        }
    }

    private function redirectWithError(string $message): \Illuminate\Http\RedirectResponse
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        return redirect("{$frontendUrl}/auth/callback?error=" . urlencode($message));
    }
}
