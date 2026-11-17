<?php

namespace Database\Seeders;

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@adtc2.local'],
            [
                'name' => 'Administrador Geral',
                'password' => 'password',
                'role' => UserRole::AdminGeral,
                'email_verified_at' => now(),
            ]
        );

        $sede = Congregacao::where('nome', 'SEDE')->first();
        if ($sede) {
            User::firstOrCreate(
                ['email' => 'lider.sede@adtc2.local'],
                [
                    'name' => 'Líder Sede',
                    'password' => 'password',
                    'role' => UserRole::LiderCongregacao,
                    'congregacao_id' => $sede->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        User::firstOrCreate(
            ['email' => 'viewer@adtc2.local'],
            [
                'name' => 'Visualizador Demo',
                'password' => 'password',
                'role' => UserRole::Visualizador,
                'email_verified_at' => now(),
            ]
        );
    }
}