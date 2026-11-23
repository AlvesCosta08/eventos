<?php

namespace App\Domain\Eventos\Enums;

enum TipoEvento: string
{
    case Campo = 'campo';
    case Congregacao = 'congregacao';

    public function label(): string
    {
        return match ($this) {
            self::Campo => 'Campo',
            self::Congregacao => 'Congregação',
        };
    }
}