<?php

namespace App\Policies;

use App\Domain\Agenda\Services\ControleAgendaService;
use App\Models\Evento;
use App\Models\User;

class EventoPolicy
{
    /**
     * Permite acessar a listagem de eventos.
     *
     * O controlador continua responsável
     * por restringir os registros à congregação.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Permite visualizar eventos da própria
     * congregação ou todos, no caso do administrador.
     */
    public function view(User $user, Evento $evento): bool
    {
        return $user->isAdminGeral()
            || $user->pertenceACongregacao(
                (int) $evento->congregacao_id
            );
    }

    /**
     * Autoriza a abertura do formulário de cadastro.
     *
     * A permissão para cadastrar em determinado
     * ano será verificada no StoreEventoRequest.
     */
    public function create(User $user): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        return $user->isLiderCongregacao()
            && $user->congregacao_id !== null;
    }

    /**
     * RN-001: autoriza a edição conforme
     * o ano do evento e a congregação.
     */
    public function update(User $user, Evento $evento): bool
    {
        // Administrador Geral não depende
        // do modo de controle da agenda.
        if ($user->isAdminGeral()) {
            return true;
        }

        // Apenas líderes da própria congregação
        // podem editar seus eventos.
        if (
            ! $user->isLiderCongregacao()
            || ! $user->pertenceACongregacao(
                (int) $evento->congregacao_id
            )
        ) {
            return false;
        }

        // Obtém o ano original do evento.
        $ano = (int) $evento->dt_evento->year;

        // Verifica o modo da agenda para esse ano.
        return app(ControleAgendaService::class)
            ->podeGerenciar(
                $user,
                $ano,
                (int) $evento->congregacao_id
            );
    }

    /**
     * Exclusão exclusiva do Administrador Geral.
     */
    public function delete(User $user, Evento $evento): bool
    {
        return $user->isAdminGeral();
    }
}
