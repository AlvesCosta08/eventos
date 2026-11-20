<?php

namespace App\Domain\Dashboard\Actions;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use App\Models\User;

class GetDashboardDataAction
{
    public function execute(User $user): array
    {
        // 🌟 Usamos o scope que já criamos para garantir a visibilidade correta
        $baseQuery = Evento::query()->visivelPara($user);

        $proximos7 = (clone $baseQuery)
            ->proximos(7) // 🌟 Scope existente
            ->get();

        $proximosEventos = (clone $baseQuery)
            ->futuros()   // 🌟 Scope existente
            ->limit(10)
            ->get();

        $totais = [
            'total'      => (clone $baseQuery)->count(),
            'proximos30' => (clone $baseQuery)->proximos(30)->count(),
            // 🌟 Usando o Enum para segurança de tipos, em vez de strings soltas
            'agendado'   => (clone $baseQuery)->where('situacao', SituacaoEvento::Agendado)->count(),
            'realizado'  => (clone $baseQuery)->where('situacao', SituacaoEvento::Realizado)->count(),
            'cancelado'  => (clone $baseQuery)->where('situacao', SituacaoEvento::Cancelado)->count(),
        ];

        $congregacoes = Congregacao::withCount('eventos')
            ->orderByDesc('eventos_count')
            ->limit(5)
            ->get();

        return [
            'proximos7'       => $proximos7,
            'proximosEventos' => $proximosEventos,
            'totais'          => $totais,
            'congregacoes'    => $congregacoes,
        ];
    }
}