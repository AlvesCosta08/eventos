<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registra auditoria ao criar um evento', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    // ✅ CORREÇÃO: Limpa as auditorias geradas pela criação do User e Congregacao
    // para que a contagem reflita APENAS a criação do Evento.
    Audit::query()->delete();

    $this->actingAs($admin);

    $evento = Evento::create([
        'evento' => 'Culto de Celebração',
        'dt_evento' => now()->addDays(5),
        'congregacao_id' => $cong->id,
        'situacao' => \App\Domain\Eventos\Enums\SituacaoEvento::Agendado,
    ]);

    expect(Audit::count())->toBe(1);

    $audit = Audit::first();
    expect($audit->event)->toBe('created')
        ->and($audit->user_id)->toBe($admin->id)
        ->and($audit->auditable_type)->toBe(Evento::class)
        ->and($audit->new_values['evento'])->toBe('Culto de Celebração')
        ->and($audit->old_values)->toBe([]);
});

it('registra auditoria ao atualizar um evento com diff de valores', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    $this->actingAs($admin);

    $evento = Evento::create([
        'evento' => 'Evento Antigo',
        'dt_evento' => now()->addDays(5),
        'congregacao_id' => $cong->id,
        'situacao' => \App\Domain\Eventos\Enums\SituacaoEvento::Agendado,
    ]);

    // Limpa a auditoria de criação para focar apenas na atualização
    Audit::query()->delete();

    $evento->update([
        'evento' => 'Evento Atualizado',
    ]);

    expect(Audit::count())->toBe(1);

    $audit = Audit::first();
    expect($audit->event)->toBe('updated')
        ->and($audit->user_id)->toBe($admin->id)
        ->and($audit->old_values['evento'])->toBe('Evento Antigo')
        ->and($audit->new_values['evento'])->toBe('Evento Atualizado');
});

it('registra auditoria ao deletar um evento', function () {
    $admin = User::factory()->admin()->create();
    $evento = Evento::factory()->create();

    // ✅ CORREÇÃO: Limpa a auditoria de criação para focar apenas na deleção
    Audit::query()->delete();

    $this->actingAs($admin);

    $evento->delete();

    expect(Audit::count())->toBe(1)
        ->and(Audit::first()->event)->toBe('deleted')
        ->and(Audit::first()->user_id)->toBe($admin->id);
});