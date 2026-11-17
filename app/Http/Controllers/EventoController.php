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
use Illuminate\View\View;

class EventoController extends Controller
{
    /**
     * Lista os eventos conforme as permissões do usuário.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Evento::class);

        $user = $request->user();

        $filtros = $request->only([
            'busca',
            'congregacao_id',
            'situacao',
            'de',
            'ate',
        ]);

        $query = Evento::query()->with('congregacao');

        if (!$user->isAdminGeral()) {
            if ($user->congregacao_id === null) {
                // Sem congregação, não pode visualizar eventos.
                $query->whereRaw('1 = 0');
            } else {
                // Restringe os eventos à própria congregação.
                $query->where(
                    'congregacao_id',
                    $user->congregacao_id
                );
            }

            // Impede que filtros ampliem o escopo permitido.
            unset($filtros['congregacao_id']);
        }

        $query->filter($filtros);

        $eventos = $query
            ->orderBy('dt_evento')
            ->paginate(20)
            ->withQueryString();

        $congregacoes = $user->isAdminGeral()
            ? Congregacao::orderBy('nome')->get()
            : Congregacao::where(
                'id',
                $user->congregacao_id
            )->get();

        // Mantém o filtro visual da congregação do usuário.
        if (!$user->isAdminGeral()) {
            $filtros['congregacao_id'] = $user->congregacao_id;
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
        $this->authorize('create', Evento::class);

        $user = auth()->user();

        $congregacoes = $user->isAdminGeral()
            ? Congregacao::orderBy('nome')->get()
            : Congregacao::where(
                'id',
                $user->congregacao_id
            )->get();

        $evento = new Evento();

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
        $action->execute($request->validated());

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento criado com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Evento $evento): View
    {
        $this->authorize('update', $evento);

        $user = auth()->user();

        $congregacoes = $user->isAdminGeral()
            ? Congregacao::orderBy('nome')->get()
            : Congregacao::where(
                'id',
                $user->congregacao_id
            )->get();

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
        $action->execute(
            $evento,
            $request->validated()
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
        $this->authorize('delete', $evento);

        $action->execute($evento);

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento excluído com sucesso.');
    }
}
