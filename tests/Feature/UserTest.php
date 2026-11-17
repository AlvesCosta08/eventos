
<?php

use App\Domain\Users\Enums\UserRole;
use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{actingAs, delete, get, post, put};

// ==========================================================
// TESTES ORIGINAIS
// ==========================================================

it('admin pode listar usuários', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Fulano Teste']);

    actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Fulano Teste');
});

it('líder não pode listar usuários', function () {
    $lider = User::factory()->lider()->create();

    actingAs($lider)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('visualizador não pode listar usuários', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('admin pode criar usuário líder', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Novo Líder',
            'email' => 'novo.lider@adtc2.local',
            'password' => 'senha123',
            'role' => UserRole::LiderCongregacao->value,
            'congregacao_id' => $cong->id,
        ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'novo.lider@adtc2.local',
        'role' => UserRole::LiderCongregacao->value,
        'congregacao_id' => $cong->id,
    ]);
});

it('admin não pode excluir a si mesmo', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)
        ->delete(route('users.destroy', $admin))
        ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);
});

it('admin pode atualizar usuário sem trocar senha', function () {
    $admin = User::factory()->admin()->create();

    $alvo = User::factory()->visualizador()->create([
        'name' => 'Nome Antigo',
        'password' => bcrypt('senha_original'),
    ]);

    actingAs($admin)
        ->put(route('users.update', $alvo), [
            'name' => 'Nome Novo',
            'email' => $alvo->email,
            'role' => UserRole::Visualizador->value,
            'password' => '',
        ])
        ->assertRedirect(route('users.index'));

    $alvo->refresh();

    expect($alvo->name)->toBe('Nome Novo')
        ->and(Hash::check('senha_original', $alvo->password))->toBeTrue();
});

// ==========================================================
// TESTES ADICIONAIS DE SEGURANÇA
// ==========================================================

it('líder não pode criar usuário', function () {
    $lider = User::factory()->lider()->create();

    actingAs($lider)
        ->post(route('users.store'), [
            'name' => 'Usuário Indevido',
            'email' => 'indevido.lider@adtc2.local',
            'password' => 'senha123',
            'role' => UserRole::Visualizador->value,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'indevido.lider@adtc2.local',
    ]);
});

it('visualizador não pode criar usuário', function () {
    $viewer = User::factory()->visualizador()->create();

    actingAs($viewer)
        ->post(route('users.store'), [
            'name' => 'Usuário Indevido',
            'email' => 'indevido.viewer@adtc2.local',
            'password' => 'senha123',
            'role' => UserRole::Visualizador->value,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'indevido.viewer@adtc2.local',
    ]);
});

it('líder não pode atualizar usuário', function () {
    $lider = User::factory()->lider()->create();
    $alvo = User::factory()->visualizador()->create();

    actingAs($lider)
        ->put(route('users.update', $alvo), [
            'name' => 'Alteração Indevida',
            'email' => $alvo->email,
            'role' => UserRole::AdminGeral->value,
        ])
        ->assertForbidden();

    $alvo->refresh();

    expect($alvo->role)->toBe(UserRole::Visualizador)
        ->and($alvo->name)->not->toBe('Alteração Indevida');
});

it('visualizador não pode atualizar usuário', function () {
    $viewer = User::factory()->visualizador()->create();
    $alvo = User::factory()->lider()->create();

    actingAs($viewer)
        ->put(route('users.update', $alvo), [
            'name' => 'Alteração Indevida',
            'email' => $alvo->email,
            'role' => UserRole::AdminGeral->value,
        ])
        ->assertForbidden();

    $alvo->refresh();

    expect($alvo->role)->toBe(UserRole::LiderCongregacao)
        ->and($alvo->name)->not->toBe('Alteração Indevida');
});

it('líder não pode excluir usuário', function () {
    $lider = User::factory()->lider()->create();
    $alvo = User::factory()->visualizador()->create();

    actingAs($lider)
        ->delete(route('users.destroy', $alvo))
        ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $alvo->id,
    ]);
});

it('admin pode excluir outro usuário', function () {
    $admin = User::factory()->admin()->create();
    $alvo = User::factory()->visualizador()->create();

    actingAs($admin)
        ->delete(route('users.destroy', $alvo))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', [
        'id' => $alvo->id,
    ]);
});

it('não permite criar líder sem congregação', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Líder Sem Congregação',
            'email' => 'lider.sem.congregacao@adtc2.local',
            'password' => 'senha123',
            'role' => UserRole::LiderCongregacao->value,
        ])
        ->assertSessionHasErrors('congregacao_id');

    $this->assertDatabaseMissing('users', [
        'email' => 'lider.sem.congregacao@adtc2.local',
    ]);
});

it('admin geral é criado sem congregação mesmo que uma seja enviada', function () {
    $admin = User::factory()->admin()->create();
    $cong = Congregacao::factory()->create();

    actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Outro Administrador',
            'email' => 'outro.admin@adtc2.local',
            'password' => 'senha123',
            'role' => UserRole::AdminGeral->value,
            'congregacao_id' => $cong->id,
        ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'outro.admin@adtc2.local',
        'role' => UserRole::AdminGeral->value,
        'congregacao_id' => null,
    ]);
});
