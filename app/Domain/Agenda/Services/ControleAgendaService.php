<?php

namespace App\Domain\Agenda\Services;

use App\Domain\Agenda\Enums\ModoAgenda;
use App\Models\Congregacao;
use App\Models\ControleAgenda;
use App\Models\HistoricoControleAgenda;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;

class ControleAgendaService
{
    public function modoDoAno(int $ano): ModoAgenda
    {
        $controle = ControleAgenda::query()
            ->where('ano', $ano)
            ->first();

        return $controle?->modo ?? ModoAgenda::Bloqueada;
    }

    public function podeGerenciar(
        User $usuario,
        int $ano,
        int $congregacaoId
    ): bool {
        if ($usuario->isAdminGeral()) {
            return true;
        }

        if (! $usuario->isLiderCongregacao()) {
            return false;
        }

        if (! $usuario->pertenceACongregacao($congregacaoId)) {
            return false;
        }

        return match ($this->modoDoAno($ano)) {
            ModoAgenda::Bloqueada => false,

            ModoAgenda::SomenteSede => Congregacao::query()
                ->whereKey($congregacaoId)
                ->where('is_sede', true)
                ->exists(),

            ModoAgenda::Liberada => true,
        };
    }

    public function autorizarGerenciamento(
        User $usuario,
        int $ano,
        int $congregacaoId
    ): void {
        if (! $this->podeGerenciar(
            $usuario,
            $ano,
            $congregacaoId
        )) {
            throw new AuthorizationException(
                'A agenda deste ano não está liberada para sua congregação.'
            );
        }
    }

    public function alterarModo(
        User $administrador,
        int $ano,
        ModoAgenda $novoModo
    ): ControleAgenda {
        if (! $administrador->isAdminGeral()) {
            throw new AuthorizationException(
                'Somente o Administrador Geral pode alterar o controle da agenda.'
            );
        }

        if ($ano < 1 || $ano > 9999) {
            throw new \InvalidArgumentException(
                'O ano informado é inválido.'
            );
        }

        return DB::transaction(function () use (
            $administrador,
            $ano,
            $novoModo
        ) {
            ControleAgenda::query()->insertOrIgnore([
                'ano' => $ano,
                'modo' => ModoAgenda::Bloqueada->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $controle = ControleAgenda::query()
                ->where('ano', $ano)
                ->lockForUpdate()
                ->firstOrFail();

            $modoAnterior = $controle->modo;

            if ($modoAnterior === $novoModo) {
                return $controle;
            }

            $controle->update([
                'modo' => $novoModo,
                'alterado_por' => $administrador->id,
            ]);

            HistoricoControleAgenda::query()->create([
                'controle_agenda_id' => $controle->id,
                'ano' => $ano,
                'modo_anterior' => $modoAnterior,
                'modo_novo' => $novoModo,
                'alterado_por' => $administrador->id,
            ]);

            return $controle->refresh();
        });
    }
}
