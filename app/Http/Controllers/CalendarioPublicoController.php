<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarioPublicoController extends Controller
{
    public function index(Request $request): View
    {
        $dados = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes = isset($dados['mes'])
            ? CarbonImmutable::createFromFormat('!Y-m', $dados['mes'])
            : CarbonImmutable::now()->startOfMonth();

        // ✅ CORREÇÃO: Garantir ao PHPStan que $mes é sempre uma instância válida de CarbonImmutable
        if (! $mes instanceof CarbonImmutable) {
            $mes = CarbonImmutable::now()->startOfMonth();
        }

        $inicio = $mes->startOfMonth();
        $fim = $mes->endOfMonth();

        $eventos = Evento::query()
            ->select([
                'id',
                'evento',
                'dt_evento',
                'situacao',
                'congregacao_id',
            ])
            ->with('congregacao:id,nome')
            ->whereDate('dt_evento', '>=', $inicio->toDateString())
            ->whereDate('dt_evento', '<=', $fim->toDateString())
            ->orderBy('dt_evento')
            ->orderBy('evento')
            ->get();

        $eventosPorDia = $eventos->groupBy(
            fn (Evento $evento) => $evento->dt_evento->format('Y-m-d')
        );

        return view('welcome', [
            'mes'           => $mes,
            'eventos'       => $eventos,
            'eventosPorDia' => $eventosPorDia,
            'mesAnterior'   => $mes->subMonth()->format('Y-m'),
            'mesSeguinte'   => $mes->addMonth()->format('Y-m'),
        ]);
    }
}