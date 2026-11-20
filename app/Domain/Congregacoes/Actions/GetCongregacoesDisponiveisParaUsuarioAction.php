<?php

namespace App\Domain\Congregacoes\Actions;

use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Support\Collection;

class GetCongregacoesDisponiveisParaUsuarioAction
{
    public function execute(User $user): Collection
    {
        if ($user->isLiderCongregacao()) {
            return Congregacao::query()
                ->where('id', $user->congregacao_id)
                ->orderBy('nome')
                ->get();
        }

        if ($user->isAdminGeral() || $user->isVisualizador()) {
            return Congregacao::query()
                ->orderBy('nome')
                ->get();
        }

        // Fallback seguro para perfis desconhecidos
        return Congregacao::query()->whereRaw('1 = 0')->get();
    }
}