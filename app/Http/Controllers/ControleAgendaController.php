<?php

namespace App\Http\Controllers;

use App\Domain\Agenda\Enums\ModoAgenda;
use App\Domain\Agenda\Services\ControleAgendaService;
use App\Models\ControleAgenda;
use App\Models\HistoricoControleAgenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ControleAgendaController extends Controller
{
    /**
     * Exibe o controle anual da agenda.
     */
    public function index(
        Request $request,
        ControleAgendaService $servico
    ): View {
        // Somente o Administrador Geral.
        abort_unless(
            $request->user()?->isAdminGeral(),
            403
        );

        $dados = $request->validate([
            'ano' => [
                'sometimes',
                'required',
                'integer',
                'between:1,9999',
            ],
        ]);

        $ano = (int) ($dados['ano'] ?? now()->year);

        $modoAtual = $servico->modoDoAno($ano);

        $modos = ModoAgenda::cases();

        $controles = ControleAgenda::query()
            ->with('responsavel')
            ->orderByDesc('ano')
            ->get();

        $historico = HistoricoControleAgenda::query()
            ->with('responsavel')
            ->where('ano', $ano)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('controle-agenda.index', compact(
            'ano',
            'modoAtual',
            'modos',
            'controles',
            'historico'
        ));
    }

    /**
     * Altera o modo de um ano.
     */
    public function update(
        Request $request,
        int $ano,
        ControleAgendaService $servico
    ): RedirectResponse {
        // Somente o Administrador Geral.
        abort_unless(
            $request->user()?->isAdminGeral(),
            403
        );

        abort_unless(
            $ano >= 1 && $ano <= 9999,
            404
        );

        $dados = $request->validate([
            'modo' => [
                'required',
                Rule::enum(ModoAgenda::class),
            ],
        ]);

        $novoModo = ModoAgenda::from($dados['modo']);

        $servico->alterarModo(
            $request->user(),
            $ano,
            $novoModo
        );

        return redirect()
            ->route('controle-agenda.index', ['ano' => $ano])
            ->with(
                'success',
                'Controle da agenda atualizado com sucesso.'
            );
    }
}
