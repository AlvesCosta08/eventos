<?php

namespace App\Domain\Users\DTOs;

use App\Domain\Users\Enums\UserRole;

class UpdateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password, // Nullable: se for null, a Action não altera a senha
        public readonly UserRole $role,
        public readonly ?int $congregacaoId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            email: (string) $data['email'],
            password: isset($data['password']) && $data['password'] !== '' ? (string) $data['password'] : null,
            role: $data['role'] instanceof UserRole ? $data['role'] : UserRole::from($data['role']),
            congregacaoId: isset($data['congregacao_id']) && $data['congregacao_id'] !== '' ? (int) $data['congregacao_id'] : null,
        );
    }
}