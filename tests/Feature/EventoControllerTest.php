<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;
use App\Domain\Users\Enums\UserRole;
use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Domain\Agenda\Services\ControleAgendaService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
 * =========================================================================
 * CENÁRIO 1: Administrador Geral
 * =========================================================================
 */
it('permite que um admin geral crie um evento em qualquer congregacao', function () {
    $this->withoutExceptionHandling();

    $congregacao = Congregacao::factory()->create(['nome' => 'Sede Central']);
    
    $admin = User::factory()->create();
    $admin->forceFill(['role' => UserRole::AdminGeral])->save();

    $this->actingAs($admin);

    $dadosDoEvento = [
        'evento'         => 'Culto de Celebração', 
        'anotacoes'      => 'Evento especial de fim de ano',
        'dt_evento'      => now()->addDays(10)->format('Y-m-d'),
        'congregacao_id' => $congregacao->id,
        'situacao'       => SituacaoEvento::Agendado->value,
    ];

    $response = $this->post(route('eventos.store'), $dadosDoEvento);

    $response->assertRedirect(route('eventos.index'));
    $response->assertSessionHas('success', 'Evento criado com sucesso.');
    
    $this->assertDatabaseHas('eventos', [
        'evento'         => 'Culto de Celebração',
        'congregacao_id' => $congregacao->id,
    ]);
});

/*
 * =========================================================================
 * CENÁRIO 2: Líder de Congregação (CRIAÇÃO)
 * =========================================================================
 */
it('forca a congregacao do lider na criacao, ignorando dados maliciosos do formulario', function () {
    $this->withoutExceptionHandling();

    $this->mock(ControleAgendaService::class, function ($mock) {
        $mock->shouldReceive('podeGerenciar')->andReturnTrue();
    });

    $congregacaoDoLider = Congregacao::factory()->create(['nome' => 'Congregação Zona Sul']);
    $outraCongregacao   = Congregacao::factory()->create(['nome' => 'Congregação Zona Norte']);
    
    $lider = User::factory()->create();
    $lider->forceFill([
        'role'           => UserRole::LiderCongregacao,
        'congregacao_id' => $congregacaoDoLider->id,
    ])->save();

    $this->actingAs($lider);

    $dadosMaliciosos = [
        'evento'         => 'Evento do Líder',
        'dt_evento'      => now()->addDays(5)->format('Y-m-d'),
        'congregacao_id' => $outraCongregacao->id,
        'situacao'       => SituacaoEvento::Agendado->value,
    ];

    $response = $this->post(route('eventos.store'), $dadosMaliciosos);

    $response->assertRedirect(route('eventos.index'));
    
    $this->assertDatabaseHas('eventos', [
        'evento'         => 'Evento do Líder',
        'congregacao_id' => $congregacaoDoLider->id,
    ]);
    
    $this->assertDatabaseMissing('eventos', [
        'evento'         => 'Evento do Líder',
        'congregacao_id' => $outraCongregacao->id,
    ]);
});

it('bloqueia a criacao se o lider nao tiver congregacao vinculada', function () {
    // ✅ withoutExceptionHandling REMOVIDO aqui para permitir o 403 do FormRequest

    $liderSemCongregacao = User::factory()->create();
    $liderSemCongregacao->forceFill([
        'role'           => UserRole::LiderCongregacao,
        'congregacao_id' => null,
    ])->save();

    $this->actingAs($liderSemCongregacao);

    $response = $this->post(route('eventos.store'), [
        'evento'    => 'Evento Impossível',
        'dt_evento' => now()->addDays(5)->format('Y-m-d'),
        'situacao'  => SituacaoEvento::Agendado->value,
    ]);

    // O FormRequest bloqueia com 403 antes de chegar no Controller
    $response->assertForbidden(); 
});

/*
 * =========================================================================
 * CENÁRIO 3: Líder de Congregação (EDIÇÃO)
 * =========================================================================
 */
it('bloqueia a edicao se o lider tentar alterar evento de outra congregacao', function () {
    // ✅ withoutExceptionHandling REMOVIDO aqui para permitir o 403 do FormRequest

    $congregacaoA = Congregacao::factory()->create();
    $congregacaoB = Congregacao::factory()->create();
    
    $eventoDaCongregacaoB = Evento::factory()->create([
        'congregacao_id' => $congregacaoB->id,
        'dt_evento'      => now()->format('Y-m-d'),
    ]);
    
    $liderDaA = User::factory()->create();
    $liderDaA->forceFill([
        'role'           => UserRole::LiderCongregacao,
        'congregacao_id' => $congregacaoA->id,
    ])->save();

    $this->actingAs($liderDaA);

    $response = $this->put(route('eventos.update', $eventoDaCongregacaoB), [
        'evento'    => 'Tentativa de Hack',
        'dt_evento' => now()->addDays(5)->format('Y-m-d'),
        'situacao'  => SituacaoEvento::Agendado->value,
    ]);

    // O FormRequest bloqueia com 403
    $response->assertForbidden();
});

it('permite que o lider atualize um evento da sua propria congregacao', function () {
    $this->withoutExceptionHandling();

    $this->mock(ControleAgendaService::class, function ($mock) {
        $mock->shouldReceive('podeGerenciar')->andReturnTrue();
    });

    $congregacao = Congregacao::factory()->create();
    $evento = Evento::factory()->create([
        'congregacao_id' => $congregacao->id,
        'dt_evento'      => now()->format('Y-m-d'),
    ]);
    
    $lider = User::factory()->create();
    $lider->forceFill([
        'role'           => UserRole::LiderCongregacao,
        'congregacao_id' => $congregacao->id,
    ])->save();

    $this->actingAs($lider);

    $response = $this->put(route('eventos.update', $evento), [
        'evento'         => 'Evento Atualizado',
        'dt_evento'      => now()->addDays(5)->format('Y-m-d'),
        'congregacao_id' => $congregacao->id,
        'situacao'       => SituacaoEvento::Agendado->value,
    ]);

    $response->assertRedirect(route('eventos.index'));
    $response->assertSessionHas('success', 'Evento atualizado com sucesso.');
    
    $this->assertDatabaseHas('eventos', [
        'id'             => $evento->id,
        'evento'         => 'Evento Atualizado',
        'congregacao_id' => $congregacao->id,
    ]);
});