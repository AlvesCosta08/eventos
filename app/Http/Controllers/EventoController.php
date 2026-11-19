<?php

namespace App\Http\Controllers;

use App\Domain\Eventos\Actions\CreateEventoAction;
use App\Domain\Eventos\Actions\DeleteEventoAction;
use App\Domain\Eventos\Actions\UpdateEventoAction;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Congregacao;
use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventoController extends Controller
{
    /**
     * Lista os eventos conforme as permissões do usuário.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Evento::class);

        $user = $request->user();

        $filtros = $request->only([
            'busca',
            'congregacao_id',
            'situacao',
            'de',
            'ate',
        ]);

        $query = Evento::query()
            ->with('congregacao');

        /*
         * Administrador geral:
         * visualiza eventos de todas as congregações.
         *
         * Líder:
         * visualiza apenas eventos da sua congregação.
         *
         * Visualizador:
         * pode consultar eventos de todas as congregações,
         * desde que a Policy permita viewAny.
         */
        if ($user->isLiderCongregacao()) {
            if ($user->congregacao_id === null) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(
                    'congregacao_id',
                    $user->congregacao_id
                );
            }

            // Impede ampliar o escopo pelo filtro.
            unset($filtros['congregacao_id']);
        } elseif (!$user->isAdminGeral() && !$user->isVisualizador()) {
            // Bloqueia perfis desconhecidos.
            $query->whereRaw('1 = 0');

            unset($filtros['congregacao_id']);
        }

        $query->filter($filtros);

        $eventos = $query
            ->orderBy('dt_evento')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        /*
         * Congregações disponíveis nos filtros.
         */
        if ($user->isLiderCongregacao()) {
            $congregacoes = Congregacao::query()
                ->where('id', $user->congregacao_id)
                ->orderBy('nome')
                ->get();

            $filtros['congregacao_id'] = $user->congregacao_id;
        } elseif ($user->isAdminGeral() || $user->isVisualizador()) {
            $congregacoes = Congregacao::query()
                ->orderBy('nome')
                ->get();
        } else {
            $congregacoes = Congregacao::query()
                ->whereRaw('1 = 0')
                ->get();
        }

        return view(
            'eventos.index',
            compact('eventos', 'congregacoes', 'filtros')
        );
    }

    /**
     * Exibe o formulário de cadastro.
     */
    public function create(): View
    {
        Gate::authorize('create', Evento::class);

        $user = auth()->user();

        $congregacoes = $user->isAdminGeral()
            ? Congregacao::query()
                ->orderBy('nome')
                ->get()
            : Congregacao::query()
                ->where('id', $user->congregacao_id)
                ->get();

        $evento = new Evento();

        if ($user->isLiderCongregacao()) {
            $evento->congregacao_id = $user->congregacao_id;
        }

        return view(
            'eventos.form',
            compact('congregacoes', 'evento')
        );
    }

    /**
     * Salva um novo evento.
     */
    public function store(
        StoreEventoRequest $request,
        CreateEventoAction $action
    ): RedirectResponse {
        Gate::authorize('create', Evento::class);

        $user = $request->user();

        $dados = $request->validated();

        /*
         * A congregação do líder é determinada
         * pelo usuário autenticado, nunca pelo formulário.
         */
        if ($user->isLiderCongregacao()) {
            abort_if(
                $user->congregacao_id === null,
                403,
                'Usuário sem congregação vinculada.'
            );

            $dados['congregacao_id'] = $user->congregacao_id;
        }

        $action->execute($dados);

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento criado com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Evento $evento): View
    {
        Gate::authorize('update', $evento);

        $user = auth()->user();

        $congregacoes = $user->isAdminGeral()
            ? Congregacao::query()
                ->orderBy('nome')
                ->get()
            : Congregacao::query()
                ->where('id', $user->congregacao_id)
                ->get();

        return view(
            'eventos.form',
            compact('congregacoes', 'evento')
        );
    }

    /**
     * Atualiza um evento existente.
     */
    public function update(
        UpdateEventoRequest $request,
        Evento $evento,
        UpdateEventoAction $action
    ): RedirectResponse {
        Gate::authorize('update', $evento);

        $user = $request->user();

        $dados = $request->validated();

        /*
         * Impede que o líder transfira um evento
         * para outra congregação.
         */
        if ($user->isLiderCongregacao()) {
            abort_if(
                $user->congregacao_id === null ||
                (int) $evento->congregacao_id !==
                    (int) $user->congregacao_id,
                403,
                'Você não pode alterar eventos de outra congregação.'
            );

            $dados['congregacao_id'] = $user->congregacao_id;
        }

        $action->execute(
            $evento,
            $dados
        );

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento atualizado com sucesso.');
    }

    /**
     * Exclui um evento.
     */
    public function destroy(
        Evento $evento,
        DeleteEventoAction $action
    ): RedirectResponse {
        Gate::authorize('delete', $evento);

        $action->execute($evento);

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento excluído com sucesso.');
    }
}
