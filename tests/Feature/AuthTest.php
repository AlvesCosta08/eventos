<?php

use App\Models\User;

use function Pest\Laravel\{get, post};

it('redireciona guest para login ao acessar dashboard', function () {
    get(route('dashboard'))->assertRedirect(route('login'));
});

it('redireciona guest para login ao acessar eventos', function () {
    get(route('eventos.index'))->assertRedirect(route('login'));
});

it('usuário pode fazer login com credenciais válidas', function () {
    $user = User::factory()->admin()->create(['password' => 'secret']);

    post(route('login'), ['email' => $user->email, 'password' => 'secret'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('não autentica com senha errada', function () {
    $user = User::factory()->create();

    post(route('login'), ['email' => $user->email, 'password' => 'wrong'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('usuário autenticado pode fazer logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();
});