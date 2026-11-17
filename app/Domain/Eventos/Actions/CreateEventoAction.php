<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class CreateEventoAction
{
    public function execute(array $data): Evento
    {
        return Evento::create($data);
    }
}