<?php

namespace App\Domain\Eventos\Actions;

use App\Domain\Eventos\DTOs\CreateEventoDTO;
use App\Models\Evento;
use App\Models\User;

class CreateEventoAction
{
    public function execute(CreateEventoDTO $dto, User $user): Evento
    {
        if ($user->isLiderCongregacao()) {
            abort_if($user->congregacao_id === null, 403, 'Usuário sem congregação vinculada.');
            
            $dto = new CreateEventoDTO(
                evento: $dto->evento,
                dtEvento: $dto->dtEvento,
                congregacaoId: $user->congregacao_id,
                situacao: $dto->situacao,
                anotacoes: $dto->anotacoes,
            );
        }

        return Evento::create([
            'evento'         => $dto->evento,
            'anotacoes'      => $dto->anotacoes,
            'congregacao_id' => $dto->congregacaoId,
            'dt_evento'      => $dto->dtEvento,
            'situacao'       => $dto->situacao,
        ]);
    }
}