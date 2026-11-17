<?php

namespace App\Policies;

use App\Models\Evento;
use App\Models\User;

class EventoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Evento $evento): bool
    {
        return $user->isAdminGeral()
            || $user->pertenceACongregacao($evento->congregacao_id);
    }

    public function create(User $user): bool
    {
        return $user->podeGerenciarEventos();
    }

    public function update(User $user, Evento $evento): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        return $user->isLiderCongregacao()
            && $user->pertenceACongregacao($evento->congregacao_id);
    }

    public function delete(User $user, Evento $evento): bool
    {
        return $user->isAdminGeral();
    }
}