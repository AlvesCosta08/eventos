<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\DTOs\CreateUserDTO;
use App\Models\User;

class CreateUserAction
{
    public function execute(CreateUserDTO $dto): User
    {
        // O cast 'password' => 'hashed' no Model User já faz o Hash::make automaticamente
        return User::create([
            'name'           => $dto->name,
            'email'          => $dto->email,
            'password'       => $dto->password,
            'role'           => $dto->role,
            'congregacao_id' => $dto->congregacaoId,
        ]);
    }
}