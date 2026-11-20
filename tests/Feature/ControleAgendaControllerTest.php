<?php

use App\Models\User;
use App\Models\ControleAgenda;
use App\Domain\Users\Enums\UserRole;
use App\Domain\Agenda\Enums\ModoAgenda;
use App\Domain\Agenda\Services\ControleAgendaService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite que o admin geral visualize o controle da agenda', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);

    $response = $this->actingAs($admin)->get(route('controle-agenda.index'));

    $response->assertOk();
    $response->assertViewIs('controle-agenda.index');
});

it('bloqueia usuarios nao admin de visualizar o controle da agenda', function () {
    $lider = User::factory()->create(['role' => UserRole::LiderCongregacao]);

    $response = $this->actingAs($lider)->get(route('controle-agenda.index'));

    $response->assertForbidden(); // 403
});

it('permite que o admin geral altere o modo da agenda', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);
    $ano = now()->year;

    // Criamos uma instância mockada do modelo para satisfazer o type hint de retorno do Service
    $controleAgendaMock = new ControleAgenda();

    // Mock do service retornando o objeto correto, não mais 'true'
    $this->mock(ControleAgendaService::class, function ($mock) use ($controleAgendaMock) {
        $mock->shouldReceive('alterarModo')
             ->once()
             ->andReturn($controleAgendaMock);
    });

    $response = $this->actingAs($admin)->put(route('controle-agenda.update', ['ano' => $ano]), [
        'modo' => ModoAgenda::Bloqueada->value, 
    ]);

    $response->assertRedirect(route('controle-agenda.index', ['ano' => $ano]));
    $response->assertSessionHas('success', 'Controle da agenda atualizado com sucesso.');
});

it('bloqueia usuarios nao admin de alterar o modo da agenda', function () {
    $visualizador = User::factory()->create(['role' => UserRole::Visualizador]);
    $ano = now()->year;

    $response = $this->actingAs($visualizador)->put(route('controle-agenda.update', ['ano' => $ano]), [
        'modo' => ModoAgenda::Bloqueada->value,
    ]);

    $response->assertForbidden(); // 403
});