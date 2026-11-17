
<?php

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;

use function Pest\Laravel\{actingAs, delete, get, post, put};

it('admin pode listar todos os eventos', function () {
    $admin = User::factory()->admin()->create();
    Evento::factory()->count(3)->create();

    actingAs($admin)->get(route('eventos.index'))
        ->assertOk()
        ->assertSee('Eventos');
});

it('líder vê apenas eventos da própria congregação', function () {
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);
    $lider = User::factory()->lider($congA)->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_A',
        'congregacao_id' => $congA->id,
    ]);

    Evento::factory()->create([
        'evento' => 'EVENTO_B',
        'congregacao_id' => $congB->id,
    ]);

    actingAs($lider)->get(route('eventos.index'))
        ->assertOk()
        ->assertSee('EVENTO_A')
        ->assertDontSee('EVENTO_B');
});

it('visualizador não pode criar evento', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)->post(route('eventos.store'), [])
        ->assertForbidden();
});

it('admin pode criar evento', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    actingAs($admin)->post(route('eventos.store'), [
        'evento' => 'Novo Evento',
        'congregacao_id' => $cong->id,
        'dt_evento' => '2026-12-25',
        'situacao' => SituacaoEvento::Agendado->value,
    ])->assertRedirect(route('eventos.index'));

    $this->assertDatabaseHas('eventos', [
        'evento' => 'Novo Evento',
        'congregacao_id' => $cong->id,
    ]);
});

it('líder não pode editar evento de outra congregação', function () {
    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();
    $lider = User::factory()->lider($congA)->create();

    $evento = Evento::factory()->create([
        'congregacao_id' => $congB->id,
    ]);

    actingAs($lider)->put(route('eventos.update', $evento), [
        'evento' => 'Hack',
        'congregacao_id' => $congB->id,
        'dt_evento' => '2026-01-01',
        'situacao' => SituacaoEvento::Agendado->value,
    ])->assertForbidden();

    $this->assertDatabaseMissing('eventos', [
        'id' => $evento->id,
        'evento' => 'Hack',
    ]);
});

it('apenas admin pode excluir evento', function () {
    $lider = User::factory()->lider()->create();
    $admin = User::factory()->admin()->create();
    $evento = Evento::factory()->create();

    actingAs($lider)
        ->delete(route('eventos.destroy', $evento))
        ->assertForbidden();

    actingAs($admin)
        ->delete(route('eventos.destroy', $evento))
        ->assertRedirect();

    $this->assertDatabaseMissing('eventos', [
        'id' => $evento->id,
    ]);
});

it('admin pode filtrar por congregação', function () {
    $admin = User::factory()->admin()->create();
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);

    Evento::factory()->create([
        'evento' => 'EVENTO_A',
        'congregacao_id' => $congA->id,
    ]);

    Evento::factory()->create([
        'evento' => 'EVENTO_B',
        'congregacao_id' => $congB->id,
    ]);

    actingAs($admin)
        ->get(route('eventos.index', [
            'congregacao_id' => $congA->id,
        ]))
        ->assertOk()
        ->assertSee('EVENTO_A')
        ->assertDontSee('EVENTO_B');
});

it('admin pode filtrar por situação', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'AGENDADO_XYZ',
        'congregacao_id' => $cong->id,
        'situacao' => SituacaoEvento::Agendado,
    ]);

    Evento::factory()->create([
        'evento' => 'REALIZADO_XYZ',
        'congregacao_id' => $cong->id,
        'situacao' => SituacaoEvento::Realizado,
    ]);

    actingAs($admin)
        ->get(route('eventos.index', [
            'situacao' => 'agendado',
        ]))
        ->assertOk()
        ->assertSee('AGENDADO_XYZ')
        ->assertDontSee('REALIZADO_XYZ');
});

it('admin pode buscar por texto no nome do evento', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'Casamento Coletivo',
        'congregacao_id' => $cong->id,
    ]);

    Evento::factory()->create([
        'evento' => 'Batismo nas águas',
        'congregacao_id' => $cong->id,
    ]);

    actingAs($admin)
        ->get(route('eventos.index', [
            'busca' => 'Casamento',
        ]))
        ->assertOk()
        ->assertSee('Casamento Coletivo')
        ->assertDontSee('Batismo nas águas');
});

it('líder não vê eventos de outra congregação mesmo filtrando por ela', function () {
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);
    $lider = User::factory()->lider($congA)->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_B_SECRETO',
        'congregacao_id' => $congB->id,
    ]);

    actingAs($lider)
        ->get(route('eventos.index', [
            'congregacao_id' => $congB->id,
        ]))
        ->assertOk()
        ->assertDontSee('EVENTO_B_SECRETO');
});

// ==========================================================
// TESTES ADICIONAIS DE SEGURANÇA
// ==========================================================

it('visualizador não pode acessar formulário de cadastro', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)
        ->get(route('eventos.create'))
        ->assertForbidden();
});

it('líder não pode transferir evento para outra congregação', function () {
    $congA = Congregacao::factory()->create();
    $congB = Congregacao::factory()->create();

    $lider = User::factory()->lider($congA)->create();

    $evento = Evento::factory()->create([
        'congregacao_id' => $congA->id,
    ]);

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
        'congregacao_id' => $congA->id,
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
