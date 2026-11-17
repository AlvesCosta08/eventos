<?php

namespace App\Domain\Users\Actions;

use App\Models\User;

class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        }
        $user->update($data);
        return $user->fresh();
    }
}