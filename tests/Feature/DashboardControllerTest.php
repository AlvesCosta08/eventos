<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;
use App\Domain\Users\Enums\UserRole;
use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exibe dados de todas as congregacoes para o admin geral', function () {
    $congA = Congregacao::factory()->create(['nome' => 'Congregação A']);
    $congB = Congregacao::factory()->create(['nome' => 'Congregação B']);
    
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);

    // Cria eventos em ambas as congregações
    Evento::factory()->create(['congregacao_id' => $congA->id, 'situacao' => SituacaoEvento::Agendado]);
    Evento::factory()->create(['congregacao_id' => $congB->id, 'situacao' => SituacaoEvento::Agendado]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewIs('dashboard');
    
    // Admin deve ver o total de 2 eventos
    $response->assertViewHas('totais', function ($totais) {
        return $totais['total'] === 2;
    });
});

it('exibe apenas dados da congregacao do lider no dashboard', function () {
    $congLider = Congregacao::factory()->create(['nome' => 'Minha Congregação']);
    $congOutra = Congregacao::factory()->create(['nome' => 'Outra Congregação']);
    
    $lider = User::factory()->create([
        'role' => UserRole::LiderCongregacao,
        'congregacao_id' => $congLider->id
    ]);

    // Cria eventos em ambas as congregações
    Evento::factory()->create(['congregacao_id' => $congLider->id, 'situacao' => SituacaoEvento::Agendado]);
    Evento::factory()->create(['congregacao_id' => $congOutra->id, 'situacao' => SituacaoEvento::Agendado]);

    $response = $this->actingAs($lider)->get(route('dashboard'));

    $response->assertOk();
    
    // Líder deve ver apenas o total de 1 evento (o da sua congregação)
    $response->assertViewHas('totais', function ($totais) {
        return $totais['total'] === 1;
    });
});

it('exibe as top 5 congregacoes no dashboard', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);
    
    // Cria congregações com quantidades diferentes de eventos
    $c1 = Congregacao::factory()->create();
    $c2 = Congregacao::factory()->create();
    Evento::factory()->count(5)->create(['congregacao_id' => $c1->id]);
    Evento::factory()->count(2)->create(['congregacao_id' => $c2->id]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('congregacoes', function ($congregacoes) use ($c1, $c2) {
        return $congregacoes->first()->id === $c1->id && $congregacoes->count() >= 2;
    });
});