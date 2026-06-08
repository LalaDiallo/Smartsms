<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;

class CheckPlan
{
    /**
     * Hiérarchie des plans (ordre croissant de fonctionnalités)
     */
    private const HIERARCHY = [
        'freemium'   => 0,
        'starter'    => 1,
        'pro'        => 2,
        'enterprise' => 3,
    ];

    /**
     * Vérifie que le client a au moins le plan requis.
     *
     * Usage dans les routes :
     *   ->middleware('CheckPlan:starter')
     *   ->middleware('CheckPlan:pro')
     *   ->middleware('CheckPlan:enterprise')
     */
    public function handle(Request $request, Closure $next, string $minPlan = 'starter')
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // super_admin et admin bypass — l'admin est propriétaire du compte
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return $next($request);
        }

        // Récupérer l'abonnement actif du client
        $subscription = Subscription::where('client_id', $user->client_id)
            ->where('status', 'active')
            ->with('plan')
            ->latest()
            ->first();

        $currentSlug  = $subscription?->plan?->slug ?? 'freemium';
        $currentLevel = self::HIERARCHY[$currentSlug] ?? 0;
        $requiredLevel = self::HIERARCHY[$minPlan] ?? 1;

        if ($currentLevel >= $requiredLevel) {
            return $next($request);
        }

        $planNames = [
            'starter'    => 'Starter (250 000 GNF/mois)',
            'pro'        => 'Pro (1 200 000 GNF/mois)',
            'enterprise' => 'Enterprise (4 500 000 GNF/mois)',
        ];

        $planLabel = $planNames[$minPlan] ?? $minPlan;

        return response()->json([
            'error'        => 'plan_required',
            'message'      => "Cette fonctionnalité nécessite le plan {$planLabel}.",
            'required_plan'=> $minPlan,
            'current_plan' => $currentSlug,
            'upgrade_url'  => '/subscriptions',
        ], 403);
    }
}
