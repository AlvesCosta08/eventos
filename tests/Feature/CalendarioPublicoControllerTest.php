<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exibe o calendario publico com eventos do mes atual', function () {
    $congregacao = Congregacao::factory()->create();
    
    // Cria eventos para o mês atual
    Evento::factory()->create([
        'evento'         => 'Evento do Mês',
        'dt_evento'      => now()->startOfMonth()->addDays(5),
        'situacao'       => SituacaoEvento::Agendado,
        'congregacao_id' => $congregacao->id,
    ]);

    // ✅ Corrigido: usando o nome real da rota
    $response = $this->get(route('calendario.publico'));

    $response->assertOk();
    $response->assertViewIs('welcome');
    $response->assertViewHas('eventos');
    $response->assertViewHas('eventosPorDia');
    $response->assertSee('Evento do Mês');
});

it('permite filtrar eventos por mes especifico', function () {
    $congregacao = Congregacao::factory()->create();
    
    $mesEspecifico = now()->addMonths(2)->startOfMonth();
    
    Evento::factory()->create([
        'evento'         => 'Evento Futuro',
        'dt_evento'      => $mesEspecifico->copy()->addDays(10),
        'situacao'       => SituacaoEvento::Agendado,
        'congregacao_id' => $congregacao->id,
    ]);

    // ✅ Corrigido: usando o nome real da rota
    $response = $this->get(route('calendario.publico', [
        'mes' => $mesEspecifico->format('Y-m'),
    ]));

    $response->assertOk();
    $response->assertSee('Evento Futuro');
});

it('nao exibe eventos de outros meses', function () {
    $congregacao = Congregacao::factory()->create();
    
    // Evento do mês atual
    Evento::factory()->create([
        'evento'         => 'Evento Atual',
        'dt_evento'      => now()->startOfMonth()->addDays(5),
        'situacao'       => SituacaoEvento::Agendado,
        'congregacao_id' => $congregacao->id,
    ]);
    
    // Evento de outro mês
    Evento::factory()->create([
        'evento'         => 'Evento de Outro Mês',
        'dt_evento'      => now()->addMonths(3)->startOfMonth()->addDays(5),
        'situacao'       => SituacaoEvento::Agendado,
        'congregacao_id' => $congregacao->id,
    ]);

    // ✅ Corrigido: usando o nome real da rota
    $response = $this->get(route('calendario.publico'));

    $response->assertOk();
    $response->assertSee('Evento Atual');
    $response->assertDontSee('Evento de Outro Mês');
});

it('funciona sem autenticacao', function () {
    // ✅ Corrigido: usando o nome real da rota
    $response = $this->get(route('calendario.publico'));

    $response->assertOk();
    $response->assertViewIs('welcome');
});