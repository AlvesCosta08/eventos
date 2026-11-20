<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CongregacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->city(),
        ];
    }
}
