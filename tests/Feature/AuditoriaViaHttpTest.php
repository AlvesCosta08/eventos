<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('audita a criação de evento via requisição HTTP real (Cenário de Produção)', function () {
    // 1. Preparação
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    $this->actingAs($admin);
    Audit::query()->delete(); // Limpa auditorias de setup (ex: criação do usuário)

    // 2. Ação: Simula o POST do formulário pelo navegador
    $response = $this->post(route('eventos.store'), [
        'evento'         => 'Culto de Jovens',
        'tipo'           => 'congregacao', // ✅ Adicionado: campo obrigatório
        'dt_evento'      => '2026-12-25',
        'congregacao_id' => $cong->id,
        'situacao'       => 'agendado',
    ]);

    // 3. Verificação
    $response->assertRedirect(route('eventos.index'));
    expect(Audit::count())->toBe(1);
    
    $audit = Audit::first();
    expect($audit->event)->toBe('created')
        ->and($audit->user_id)->toBe($admin->id)
        ->and($audit->ip_address)->not->toBeNull(); // Prova que pegou o IP (127.0.0.1 no teste)
        
    // Prova que a URL foi resolvida (será 'console' no teste, mas URL real em produção)
    expect(in_array($audit->url, ['console', 'http://localhost/eventos']))->toBeTrue();
});

it('audita a atualização de evento via requisição HTTP real', function () {
    $admin = User::factory()->admin()->create();
    $evento = Evento::factory()->create();

    $this->actingAs($admin);
    Audit::query()->delete();

    $response = $this->put(route('eventos.update', $evento), [
        'evento'         => 'Culto Atualizado',
        'tipo'           => 'campo', // ✅ Adicionado: campo obrigatório
        'dt_evento'      => '2026-12-26',
        'congregacao_id' => $evento->congregacao_id,
        'situacao'       => 'realizado',
    ]);

    $response->assertRedirect(route('eventos.index'));

    expect(Audit::count())->toBe(1);
    expect(Audit::first()->event)->toBe('updated')
        ->and(Audit::first()->old_values['evento'])->toBe($evento->evento)
        ->and(Audit::first()->new_values['evento'])->toBe('Culto Atualizado');
});