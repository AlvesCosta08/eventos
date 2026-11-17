<?php

namespace App\Domain\Eventos\Enums;

enum SituacaoEvento: string
{
    case Agendado = 'agendado';
    case Realizado = 'realizado';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Agendado => 'Agendado',
            self::Realizado => 'Realizado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Agendado => 'bg-blue-100 text-blue-800',
            self::Realizado => 'bg-green-100 text-green-800',
            self::Cancelado => 'bg-red-100 text-red-800',
        };
    }
}