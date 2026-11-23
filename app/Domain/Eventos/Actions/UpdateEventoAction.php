<?php

namespace App\Domain\Eventos\Actions;

use App\Domain\Eventos\DTOs\UpdateEventoDTO;
use App\Models\Evento;
use App\Models\User;

class UpdateEventoAction
{
    public function execute(Evento $evento, UpdateEventoDTO $dto, User $user): Evento
    {
        if ($user->isLiderCongregacao()) {
            abort_if(
                $user->congregacao_id === null || (int) $evento->congregacao_id !== (int) $user->congregacao_id,
                403,
                'Você não pode alterar eventos de outra congregação.'
            );
            
            $dto = new UpdateEventoDTO(
                evento: $dto->evento,
                tipo: $dto->tipo, // ✅ Mantendo o tipo
                dtEvento: $dto->dtEvento,
                congregacaoId: $user->congregacao_id,
                situacao: $dto->situacao,
                anotacoes: $dto->anotacoes,
            );
        }

        $evento->update([
            'evento'         => $dto->evento,
            'tipo'           => $dto->tipo, // ✅ Salvando no banco
            'anotacoes'      => $dto->anotacoes,
            'congregacao_id' => $dto->congregacaoId,
            'dt_evento'      => $dto->dtEvento,
            'situacao'       => $dto->situacao,
        ]);

        return $evento->fresh();
    }
}