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
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filtros = $request->only(['busca', 'congregacao_id', 'situacao', 'de', 'ate']);

        $query = Evento::query()->with('congregacao');

        // Escopo forçado por congregação para líder
        if (!$user->role->veTodosEventos() && $user->congregacao_id) {
            $query->where('congregacao_id', $user->congregacao_id);
            $filtros['congregacao_id'] = $user->congregacao_id;
        }

        $query->filter($filtros);
        $eventos = $query->orderBy('dt_evento')->paginate(20)->withQueryString();

        $congregacoes = $user->role->veTodosEventos()
            ? Congregacao::orderBy('nome')->get()
            : Congregacao::where('id', $user->congregacao_id)->get();

        return view('eventos.index', compact('eventos', 'congregacoes', 'filtros'));
    }

    public function create(): View
    {
        $congregacoes = Congregacao::orderBy('nome')->get();
        $evento = new Evento();

        return view('eventos.form', compact('congregacoes', 'evento'));
    }

    public function store(StoreEventoRequest $request, CreateEventoAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('eventos.index')
            ->with('success', 'Evento criado com sucesso.');
    }

    public function edit(Evento $evento): View
    {
        $this->authorize('update', $evento);
        $congregacoes = Congregacao::orderBy('nome')->get();

        return view('eventos.form', compact('congregacoes', 'evento'));
    }

    public function update(UpdateEventoRequest $request, Evento $evento, UpdateEventoAction $action): RedirectResponse
    {
        $action->execute($evento, $request->validated());

        return redirect()->route('eventos.index')
            ->with('success', 'Evento atualizado com sucesso.');
    }

    public function destroy(Evento $evento, DeleteEventoAction $action): RedirectResponse
    {
        $this->authorize('delete', $evento);
        $action->execute($evento);

        return redirect()->route('eventos.index')
            ->with('success', 'Evento excluído com sucesso.');
    }
}