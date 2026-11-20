<?php

namespace App\Domain\Users\Enums;

enum UserRole: string
{
    case AdminGeral = 'admin_geral';
    case LiderCongregacao = 'lider_congregacao';
    case Visualizador = 'visualizador';

    public function label(): string
    {
        return match ($this) {
            self::AdminGeral => 'Administrador Geral',
            self::LiderCongregacao => 'Líder de Congregação',
            self::Visualizador => 'Visualizador',
        };
    }

    public function podeGerenciarEventos(): bool
    {
        return in_array($this, [self::AdminGeral, self::LiderCongregacao], true);
    }

    public function podeGerenciarUsuarios(): bool
    {
        return $this === self::AdminGeral;
    }

    public function veTodosEventos(): bool
    {
        return $this === self::AdminGeral;
    }
}
