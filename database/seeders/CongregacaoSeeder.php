<?php

namespace Database\Seeders;

use App\Models\Congregacao;
use Illuminate\Database\Seeder;

class CongregacaoSeeder extends Seeder
{
    public function run(): void
    {
        $nomes = [
            'SEDE',
            'ALEGRIA',
            'JUBAIA',
            'LAGES',
            'NOVO MARANGUAPE 1',
            'NOVO MARANGUAPE 2',
            'NOVO MARANGUAPE 3',
            'NOVO MARANGUAPE 4',
            'OUTRA BANDA',
            'PARQUE SÃO JOÃO',
            'NOVO PARQUE IRACEMA',
            'NOVO PARQUE IRACEMA 2',
            'SITIO SÃO LUIZ',
            'TABATINGA',
            'UMARIZEIRAS',
            'VITÓRIA',
            'VIÇOSA',
            'PAPARA',
            'PLANALTO',
            'SERRA JUBAIA',
            'IRACEMA',
            'PARAISO',
            'CASTELO',
            'LAMEIRÃO',
        ];

        foreach ($nomes as $nome) {
            Congregacao::firstOrCreate([
                'nome' => $nome,
            ]);
        }
    }
}
