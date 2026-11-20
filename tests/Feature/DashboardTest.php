<?php

use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('admin vê próximos eventos no dashboard', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_FUTURO_ABC',
        'congregacao_id' => $cong->id,
        'dt_evento' => now()->addDays(3),
        'situacao' => 'agendado',
    ]);

    actingAs($admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('EVENTO_FUTURO_ABC');
});

it('líder vê apenas sua congregação no dashboard', function () {
    $congA = Congregacao::factory()->create(['nome' => 'A']);
    $congB = Congregacao::factory()->create(['nome' => 'B']);
    $lider = User::factory()->lider($congA)->create();

    Evento::factory()->create([
        'evento' => 'EVENTO_A_DASH',
        'congregacao_id' => $congA->id,
        'dt_evento' => now()->addDays(2),
    ]);
    Evento::factory()->create([
        'evento' => 'EVENTO_B_DASH',
        'congregacao_id' => $congB->id,
        'dt_evento' => now()->addDays(2),
    ]);

    actingAs($lider)->get(route('dashboard'))
        ->assertSee('EVENTO_A_DASH')
        ->assertDontSee('EVENTO_B_DASH');
});
