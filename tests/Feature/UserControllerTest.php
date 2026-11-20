<?php

use App\Models\Congregacao;
use App\Models\User;
use App\Domain\Users\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite que o admin geral visualize a lista de usuarios', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);
    
    User::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('users.index'));

    $response->assertOk();
    $response->assertViewIs('users.index');
    $response->assertViewHas('users');
});

it('bloqueia usuarios nao admin de visualizar a lista de usuarios', function () {
    $lider = User::factory()->create(['role' => UserRole::LiderCongregacao]);

    $response = $this->actingAs($lider)->get(route('users.index'));

    $response->assertForbidden();
});

it('permite que o admin geral crie um novo usuario', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);
    $congregacao = Congregacao::factory()->create();

    $dados = [
        'name'           => 'Novo Usuário',
        'email'          => 'novo@example.com',
        'password'       => 'password123',
        'role'           => UserRole::LiderCongregacao->value,
        'congregacao_id' => $congregacao->id,
    ];

    $response = $this->actingAs($admin)->post(route('users.store'), $dados);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Usuário criado com sucesso.');
    
    $this->assertDatabaseHas('users', [
        'name'  => 'Novo Usuário',
        'email' => 'novo@example.com',
    ]);
});

it('permite que o admin geral edite um usuario existente', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);
    $congregacao = Congregacao::factory()->create();
    $user = User::factory()->create(['role' => UserRole::Visualizador]);

    $dados = [
        'name'           => 'Nome Atualizado',
        'email'          => 'atualizado@example.com',
        'password'       => 'newpassword123',
        'role'           => UserRole::LiderCongregacao->value,
        'congregacao_id' => $congregacao->id,
    ];

    $response = $this->actingAs($admin)->put(route('users.update', $user), $dados);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Usuário atualizado com sucesso.');
    
    $this->assertDatabaseHas('users', [
        'id'    => $user->id,
        'name'  => 'Nome Atualizado',
        'email' => 'atualizado@example.com',
    ]);
});

it('permite que o admin geral exclua um usuario', function () {
    $admin = User::factory()->create(['role' => UserRole::AdminGeral]);
    $user = User::factory()->create(['role' => UserRole::Visualizador]);

    $response = $this->actingAs($admin)->delete(route('users.destroy', $user));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'Usuário excluído com sucesso.');
    
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});