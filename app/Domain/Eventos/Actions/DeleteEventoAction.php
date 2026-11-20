<?php

namespace App\Domain\Eventos\Actions;

use App\Models\Evento;

class DeleteEventoAction
{
    /**
     * Executa a exclusão do evento.
     * 
     * @param Evento $evento
     * @return void
     */
    public function execute(Evento $evento): void
    {
        $evento->delete();
    }
}