<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Faz o middleware ValidateCsrfToken validar de verdade
    app()->instance('env', 'production');
});

afterEach(function () {
    app()->instance('env', 'testing');
});

it('bloqueia login sem token csrf com 419', function () {
    $user = User::factory()->create(['password' => 'secret']);

    $this->post(route('login'), [
        'email'    => $user->email,
        'password' => 'secret',
    ])->assertStatus(419);
});

it('aceita login com token csrf válido emitido pelo formulário', function () {
    $user = User::factory()->create(['password' => 'secret']);

    $pagina = $this->get(route('login'))->assertOk();

    // O formulário precisa expor o hidden _token
    $pagina->assertSee('_token', false);

    $token = csrf_token();

    $this->post(route('login'), [
        '_token'   => $token,
        'email'    => $user->email,
        'password' => 'secret',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('emite cookie de sessão e xsrf no GET de login', function () {
    $response = $this->get(route('login'))->assertOk();

    $cookies = collect($response->headers->getCookies())->map->getName()->all();

    expect($cookies)->toContain(config('session.cookie'))
        ->and($cookies)->toContain('XSRF-TOKEN');
});