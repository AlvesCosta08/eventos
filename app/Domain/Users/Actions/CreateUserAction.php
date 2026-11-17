<?php

namespace App\Domain\Users\Actions;

use App\Models\User;

class CreateUserAction
{
    public function execute(array $data): User
    {
        // O cast 'password' => 'hashed' no model já faz o Hash::make
        return User::create($data);
    }
}