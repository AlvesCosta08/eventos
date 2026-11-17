<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class DeleteEventoAction
{
    public function execute(Evento $evento): void
    {
        $evento->delete();
    }
}