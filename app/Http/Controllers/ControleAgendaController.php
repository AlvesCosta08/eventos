<?php

namespace App\Http\Controllers;

use App\Domain\Agenda\Enums\ModoAgenda;
use App\Domain\Agenda\Services\ControleAgendaService;
use App\Http\Requests\IndexControleAgendaRequest;
use App\Http\Requests\UpdateControleAgendaRequest;
use App\Models\ControleAgenda;
use App\Models\HistoricoControleAgenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ControleAgendaController extends Controller
{
    /**
     * Exibe o controle anual da agenda.
     */
    public function index(
        IndexControleAgendaRequest $request,
        ControleAgendaService $servico
    ): View {
        $ano = (int) ($request->input('ano') ?? now()->year);

        return view('controle-agenda.index', [
            'ano'         => $ano,
            'modoAtual'   => $servico->modoDoAno($ano),
            'modos'       => ModoAgenda::cases(),
            'controles'   => ControleAgenda::query()
                ->with('responsavel')
                ->orderByDesc('ano')
                ->get(),
            'historico'   => HistoricoControleAgenda::query()
                ->with('responsavel')
                ->where('ano', $ano)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    /**
     * Altera o modo de um ano.
     */
    public function update(
        UpdateControleAgendaRequest $request,
        int $ano,
        ControleAgendaService $servico
    ): RedirectResponse {
        $novoModo = ModoAgenda::from($request->validated('modo'));

        $servico->alterarModo(
            $request->user(),
            $ano,
            $novoModo
        );

        return redirect()
            ->route('controle-agenda.index', ['ano' => $ano])
            ->with('success', 'Controle da agenda atualizado com sucesso.');
    }
}