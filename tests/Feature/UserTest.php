<?php

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use App\Models\User;

use function Pest\Laravel\{actingAs, delete, get, post, put};

it('admin pode listar usuários', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Fulano Teste']);

    actingAs($admin)->get(route('users.index'))
        ->assertOk()
        ->assertSee('Fulano Teste');
});

it('líder não pode listar usuários', function () {
    $lider = User::factory()->lider()->create();

    actingAs($lider)->get(route('users.index'))->assertForbidden();
});

it('visualizador não pode listar usuários', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)->get(route('users.index'))->assertForbidden();
});

it('admin pode criar usuário líder', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    actingAs($admin)->post(route('users.store'), [
        'name' => 'Novo Líder',
        'email' => 'novo.lider@adtc2.local',
        'password' => 'senha123',
        'role' => UserRole::LiderCongregacao->value,
        'congregacao_id' => $cong->id,
    ])->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'novo.lider@adtc2.local',
        'role' => UserRole::LiderCongregacao->value,
        'congregacao_id' => $cong->id,
    ]);
});

it('admin não pode excluir a si mesmo', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)->delete(route('users.destroy', $admin))->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('admin pode atualizar usuário sem trocar senha', function () {
    $admin = User::factory()->admin()->create();
    $alvo = User::factory()->visualizador()->create([
        'name' => 'Nome Antigo',
        'password' => bcrypt('senha_original'),
    ]);

    actingAs($admin)->put(route('users.update', $alvo), [
        'name' => 'Nome Novo',
        'email' => $alvo->email,
        'role' => UserRole::Visualizador->value,
        // password em branco = manter
    ])->assertRedirect(route('users.index'));

    $alvo->refresh();
    expect($alvo->name)->toBe('Nome Novo')
        ->and(Hash::check('senha_original', $alvo->password))->toBeTrue();
});