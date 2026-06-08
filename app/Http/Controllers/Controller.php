<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Retourne l'utilisateur authentifié avec le bon type pour l'IDE.
     */
    protected function authUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }
}
