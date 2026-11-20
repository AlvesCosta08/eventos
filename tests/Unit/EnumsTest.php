<?php

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Domain\Users\Enums\UserRole;

it('admin e líder podem gerenciar eventos', function () {
    expect(UserRole::AdminGeral->podeGerenciarEventos())->toBeTrue()
        ->and(UserRole::LiderCongregacao->podeGerenciarEventos())->toBeTrue()
        ->and(UserRole::Visualizador->podeGerenciarEventos())->toBeFalse();
});

it('apenas admin vê todos os eventos', function () {
    expect(UserRole::AdminGeral->veTodosEventos())->toBeTrue()
        ->and(UserRole::LiderCongregacao->veTodosEventos())->toBeFalse();
});

it('apenas admin gerencia usuários', function () {
    expect(UserRole::AdminGeral->podeGerenciarUsuarios())->toBeTrue()
        ->and(UserRole::LiderCongregacao->podeGerenciarUsuarios())->toBeFalse()
        ->and(UserRole::Visualizador->podeGerenciarUsuarios())->toBeFalse();
});

it('enum de situação retorna labels corretos', function () {
    expect(SituacaoEvento::Agendado->label())->toBe('Agendado')
        ->and(SituacaoEvento::Realizado->label())->toBe('Realizado')
        ->and(SituacaoEvento::Cancelado->label())->toBe('Cancelado');
});
