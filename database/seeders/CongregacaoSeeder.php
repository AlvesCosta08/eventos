<?php

namespace Database\Seeders;

use App\Models\Congregacao;
use Illuminate\Database\Seeder;

class CongregacaoSeeder extends Seeder
{
    public function run(): void
    {
        $nomes = [
            'SEDE', 'TABATINGA', 'NOVO MARANGUAPE 1', 'NOVO MARANGUAPE 2',
            'NOVO MARANGUAPE 3', 'NOVO PARQUE IRACEMA', 'PARQUE SÃO JOÃO',
            'IRACEMA', 'LAMEIRÃO', 'LAGES', 'SITIO SÃO LUIZ',
            'VITÓRIA', 'JUBAIA',
        ];

        foreach ($nomes as $nome) {
            Congregacao::firstOrCreate(['nome' => $nome]);
        }
    }
}