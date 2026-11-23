<?php

use App\Domain\Agenda\Services\ControleAgendaService;
use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('líder não pode transferir evento para outra congregação', function () {
    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();

    $lider = User::factory()->create([
        'role' => \App\Domain\Users\Enums\UserRole::LiderCongregacao,
        'congregacao_id' => $congA->id,
    ]);

    $evento = Evento::factory()->create([
        'congregacao_id' => $congA->id,
    ]);

    $this->mock(ControleAgendaService::class, function ($mock) {
        $mock->shouldReceive('podeGerenciar')->andReturnTrue();
    });

    $this->actingAs($lider)
        ->put(route('eventos.update', $evento), [
            'evento'         => 'Evento Atualizado',
            'tipo'           => 'congregacao',
            'congregacao_id' => $congB->id, // Dado malicioso que será ignorado pelo prepareForValidation
            'dt_evento'      => '2026-12-25',
            'situacao'       => SituacaoEvento::Agendado->value,
        ])
        // ✅ CORRIGIDO: O sistema ignora o dado malicioso e processa com sucesso (302)
        ->assertRedirect(route('eventos.index'));

    // A prova real: O evento foi atualizado, mas CONTINUA na congregação original
    $this->assertDatabaseHas('eventos', [
        'id' => $evento->id,
        'evento' => 'Evento Atualizado',
        'congregacao_id' => $congA->id, 
    ]);
});

it('usuário sem congregação não visualiza eventos', function () {
    $viewer = User::factory()->create([
        'role' => \App\Domain\Users\Enums\UserRole::Visualizador,
        'congregacao_id' => null,
    ]);

    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_RESTRITO_XYZ',
        'congregacao_id' => $cong->id,
    ]);

    $this->actingAs($viewer)
        ->get(route('eventos.index'))
        ->assertOk()
        ->assertDontSee('EVENTO_RESTRITO_XYZ');
});

it('líder pode atualizar evento da própria congregação', function () {
    $cong = Congregacao::factory()->create();
    $lider = User::factory()->create([
        'role' => \App\Domain\Users\Enums\UserRole::LiderCongregacao,
        'congregacao_id' => $cong->id,
    ]);

    $evento = Evento::factory()->create([
        'congregacao_id' => $cong->id,
    ]);

    $this->mock(ControleAgendaService::class, function ($mock) {
        $mock->shouldReceive('podeGerenciar')->andReturnTrue();
    });

    $this->actingAs($lider)
        ->put(route('eventos.update', $evento), [
            'evento'         => 'Evento Editado Pelo Líder',
            'tipo'           => 'campo',
            'congregacao_id' => $cong->id,
            'dt_evento'      => '2026-12-25',
            'situacao'       => SituacaoEvento::Agendado->value,
        ])
        ->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', [
        'id' => $evento->id,
        'evento' => 'Evento Editado Pelo Líder',
        'congregacao_id' => $cong->id,
    ]);
});

it('admin pode transferir evento para outra congregação', function () {
    $admin = User::factory()->create([
        'role' => \App\Domain\Users\Enums\UserRole::AdminGeral,
    ]);

    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();

    $evento = Evento::factory()->create([
        'congregacao_id' => $congA->id,
    ]);

    $this->actingAs($admin)
        ->put(route('eventos.update', $evento), [
            'evento'         => 'Evento Transferido',
            'tipo'           => 'congregacao',
            'congregacao_id' => $congB->id,
            'dt_evento'      => '2026-12-25',
            'situacao'       => SituacaoEvento::Agendado->value,
        ])
        ->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', [
        'id' => $evento->id,
        'evento' => 'Evento Transferido',
        'congregacao_id' => $congB->id,
    ]);
});