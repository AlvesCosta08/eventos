<?php

namespace App\Http\Controllers;

use App\Models\Congregacao;
use App\Models\Evento;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $base = Evento::query()->with('congregacao');
        if (!$user->role->veTodosEventos() && $user->congregacao_id) {
            $base->where('congregacao_id', $user->congregacao_id);
        }

        $proximos7 = (clone $base)
            ->whereDate('dt_evento', '>=', now()->toDateString())
            ->whereDate('dt_evento', '<=', now()->addDays(7)->toDateString())
            ->orderBy('dt_evento')
            ->get();

        $proximosEventos = (clone $base)
            ->whereDate('dt_evento', '>=', now()->toDateString())
            ->orderBy('dt_evento')
            ->limit(10)
            ->get();

        $totais = [
            'total' => (clone $base)->count(),
            'proximos30' => (clone $base)
                ->whereDate('dt_evento', '>=', now()->toDateString())
                ->whereDate('dt_evento', '<=', now()->addDays(30)->toDateString())
                ->count(),
            'agendado' => (clone $base)->where('situacao', 'agendado')->count(),
            'realizado' => (clone $base)->where('situacao', 'realizado')->count(),
            'cancelado' => (clone $base)->where('situacao', 'cancelado')->count(),
        ];

        $congregacoes = Congregacao::withCount('eventos')
            ->orderByDesc('eventos_count')
            ->limit(5)
            ->get();

        return view('dashboard', compact('proximos7', 'proximosEventos', 'totais', 'congregacoes'));
    }
}