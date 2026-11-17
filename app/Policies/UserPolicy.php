<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminGeral();
    }

    public function view(User $user, User $alvo): bool
    {
        return $user->isAdminGeral();
    }

    public function create(User $user): bool
    {
        return $user->isAdminGeral();
    }

    public function update(User $user, User $alvo): bool
    {
        return $user->isAdminGeral();
    }

    public function delete(User $user, User $alvo): bool
    {
        return $user->isAdminGeral() && $user->id !== $alvo->id;
    }
}