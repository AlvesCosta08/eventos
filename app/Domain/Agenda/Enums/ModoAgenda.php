<?php

namespace App\Domain\Agenda\Enums;

enum ModoAgenda: string
{
    case Bloqueada = 'bloqueada';
    case SomenteSede = 'somente_sede';
    case Liberada = 'liberada';

    public function label(): string
    {
        return match ($this) {
            self::Bloqueada => 'Bloqueada',
            self::SomenteSede => 'Somente Sede',
            self::Liberada => 'Liberada',
        };
    }
}
