<?php

namespace Database\Factories;

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Visualizador,
            'congregacao_id' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::AdminGeral,
            'congregacao_id' => null,
        ]);
    }

    public function lider(?Congregacao $congregacao = null): static
    {
        return $this->state(fn () => [
            'role' => UserRole::LiderCongregacao,
            'congregacao_id' => $congregacao?->id ?? Congregacao::factory(),
        ]);
    }

    public function visualizador(): static
    {
        return $this->state(fn () => ['role' => UserRole::Visualizador]);
    }
}