<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\DTOs\UpdateUserDTO;
use App\Models\User;

class UpdateUserAction
{
    public function execute(User $user, UpdateUserDTO $dto): User
    {
        $data = [
            'name'           => $dto->name,
            'email'          => $dto->email,
            'role'           => $dto->role,
            'congregacao_id' => $dto->congregacaoId,
        ];

        // O cast 'password' => 'hashed' no Model User já faz o Hash::make.
        // Só incluímos a senha no array de update se ela foi fornecida no DTO.
        if ($dto->password !== null) {
            $data['password'] = $dto->password;
        }

        $user->update($data);

        return $user->fresh();
    }
}