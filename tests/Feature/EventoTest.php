<?php

use App\Domain\Agenda\Services\ControleAgendaService; // ✅ ADICIONADO
use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;

use function Pest\Laravel\actingAs;

// ... (mantenha os testes anteriores inalterados até chegar nestes dois) ...

it('líder não pode transferir evento para outra congregação', function () {
    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();

    $lider = User::factory()->lider($congA)->create();

    $evento = Evento::factory()->create([
        'congregacao_id' => $congA->id,
    ]);

    // ✅ CORREÇÃO: Mock do serviço para evitar bloqueio por "Ano Bloqueado" durante o teste de update
    $this->mock(ControleAgendaService::class, function ($mock) {
        $mock->shouldReceive('podeGerenciar')->andReturnTrue();
    });

    actingAs($lider)
        ->put(route('eventos.update', $evento), [
            'evento' => 'Evento Atualizado',
            'congregacao_id' => $congB->id,
            'dt_evento' => '2026-12-25',
            'situacao' => SituacaoEvento::Agendado->value,
        ])
        ->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', [
        'id' => $evento->id,
        'evento' => 'Evento Atualizado',
        'congregacao_id' => $congA->id, // A Action força a manter a congregação do líder
    ]);
});

it('usuário sem congregação não visualiza eventos', function () {
    $viewer = User::factory()->visualizador()->create([
        'congregacao_id' => null,
    ]);

    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_RESTRITO_XYZ',
        'congregacao_id' => $cong->id,
    ]);

    actingAs($viewer)
        ->get(route('eventos.index'))
        ->assertOk()
        ->assertDontSee('EVENTO_RESTRITO_XYZ');
});

it('líder pode atualizar evento da própria congregação', function () {
    $cong = Congregacao::factory()->create();
    $lider = User::factory()->lider($cong)->create();

    $evento = Evento::factory()->create([
        'congregacao_id' => $cong->id,
    ]);

    // ✅ CORREÇÃO: Mock do serviço para evitar bloqueio por "Ano Bloqueado" durante o teste de update
    $this->mock(ControleAgendaService::class, function ($mock) {
        $mock->shouldReceive('podeGerenciar')->andReturnTrue();
    });

    actingAs($lider)
        ->put(route('eventos.update', $evento), [
            'evento' => 'Evento Editado Pelo Líder',
            'congregacao_id' => $cong->id,
            'dt_evento' => '2026-12-25',
            'situacao' => SituacaoEvento::Agendado->value,
        ])
        ->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', [
        'id' => $evento->id,
        'evento' => 'Evento Editado Pelo Líder',
        'congregacao_id' => $cong->id,
    ]);
});

it('admin pode transferir evento para outra congregação', function () {
    $admin = User::factory()->admin()->create();

    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();

    $evento = Evento::factory()->create([
        'congregacao_id' => $congA->id,
    ]);

    actingAs($admin)
        ->put(route('eventos.update', $evento), [
            'evento' => 'Evento Transferido',
            'congregacao_id' => $congB->id,
            'dt_evento' => '2026-12-25',
            'situacao' => SituacaoEvento::Agendado->value,
        ])
        ->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', [
        'id' => $evento->id,
        'evento' => 'Evento Transferido',
        'congregacao_id' => $congB->id,
    ]);
});