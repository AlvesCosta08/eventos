<?php

namespace Database\Factories;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'evento' => fake()->sentence(3),
            'anotacoes' => fake()->optional()->paragraph(),
            'congregacao_id' => Congregacao::factory(),
            'dt_evento' => fake()->dateTimeBetween('-2 months', '+6 months'),
            'situacao' => SituacaoEvento::Agendado,
        ];
    }

    public function realizado(): static
    {
        return $this->state(fn () => ['situacao' => SituacaoEvento::Realizado]);
    }

    public function cancelado(): static
    {
        return $this->state(fn () => ['situacao' => SituacaoEvento::Cancelado]);
    }

    public function futuro(): static
    {
        return $this->state(fn () => [
            'dt_evento' => now()->addDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
