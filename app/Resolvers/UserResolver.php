<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\UserResolver as UserResolverContract;

class UserResolver implements UserResolverContract
{
    /**
     * Resolve o ID do usuário autenticado.
     */
    public static function resolve()
    {
        // Força o uso do guard 'web' e verifica se está autenticado
        return Auth::guard('web')->check() ? Auth::guard('web')->id() : null;
    }
}