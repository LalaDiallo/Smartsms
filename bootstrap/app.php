<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckPlan;
use App\Http\Middleware\DevApiAuth;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS en premier — doit précéder toute autre logique pour inclure les headers sur les erreurs
        $middleware->prepend(HandleCors::class);

        $middleware->alias([
            'CheckPermission' => CheckPermission::class,
            'CheckPlan'       => CheckPlan::class,
            'dev.auth'        => DevApiAuth::class,
        ]);

        // NE PAS appeler statefulApi() ici : le frontend utilise Bearer tokens,
        // pas des cookies de session. statefulApi() activerait le CSRF pour les
        // domaines stateful (localhost:5173), ce qui casserait toutes les mutations.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
