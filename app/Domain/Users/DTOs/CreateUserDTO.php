<?php

namespace App\Domain\Users\DTOs;

use App\Domain\Users\Enums\UserRole;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly UserRole $role,
        public readonly ?int $congregacaoId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            email: (string) $data['email'],
            password: (string) $data['password'],
            role: $data['role'] instanceof UserRole ? $data['role'] : UserRole::from($data['role']),
            congregacaoId: isset($data['congregacao_id']) ? (int) $data['congregacao_id'] : null,
        );
    }
}