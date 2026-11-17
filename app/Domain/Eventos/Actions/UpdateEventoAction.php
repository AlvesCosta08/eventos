<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class UpdateEventoAction
{
    public function execute(Evento $evento, array $data): Evento
    {
        $evento->update($data);
        return $evento->fresh();
    }
}