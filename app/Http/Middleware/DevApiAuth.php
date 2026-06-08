<?php

namespace App\Http\Middleware;

use App\Models\DeveloperApiKey;
use Closure;
use Illuminate\Http\Request;

class DevApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $serviceId   = $request->header('X-Service-ID');
        $secretToken = $request->header('X-Secret-Token');

        if (!$serviceId || !$secretToken) {
            return response()->json([
                'error' => 'missing_credentials',
                'message' => 'Les headers X-Service-ID et X-Secret-Token sont requis.',
            ], 401);
        }

        $apiKey = DeveloperApiKey::where('service_id', $serviceId)
            ->where('secret_token', hash('sha256', $secretToken))
            ->where('is_active', true)
            ->with('client')
            ->first();

        if (!$apiKey) {
            return response()->json([
                'error'   => 'invalid_credentials',
                'message' => 'Clé API invalide, désactivée ou introuvable.',
            ], 401);
        }

        $apiKey->update(['last_used_at' => now()]);

        $request->merge(['_dev_api_key' => $apiKey]);
        $request->merge(['_dev_client'  => $apiKey->client]);

        return $next($request);
    }
}
