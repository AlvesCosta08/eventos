<?php

namespace App\Http\Controllers;

use App\Domain\Congregacoes\Actions\GetCongregacoesDisponiveisParaUsuarioAction;
use App\Domain\Eventos\Actions\CreateEventoAction;
use App\Domain\Eventos\Actions\DeleteEventoAction;
use App\Domain\Eventos\Actions\UpdateEventoAction;
use App\Domain\Eventos\DTOs\CreateEventoDTO;
use App\Domain\Eventos\DTOs\UpdateEventoDTO;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventoController extends Controller
{
    public function index(
        Request $request,
        GetCongregacoesDisponiveisParaUsuarioAction $getCongregacoesAction
    ): View {
        Gate::authorize('viewAny', Evento::class);

        $user = $request->user();
        
        // ✅ CORREÇÃO: Permitimos o filtro por congregacao_id. 
        // O scopeVisivelPara já garante que líderes só veem os seus, tornando o filtro seguro.
        $filtros = $request->only(['busca', 'situacao', 'de', 'ate', 'congregacao_id']);

        $eventos = Evento::query()
            ->with('congregacao')
            ->visivelPara($user) 
            ->filter($filtros)
            ->orderBy('dt_evento')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        $congregacoes = $getCongregacoesAction->execute($user);

        return view('eventos.index', compact('eventos', 'congregacoes', 'filtros'));
    }

    public function create(GetCongregacoesDisponiveisParaUsuarioAction $getCongregacoesAction): View
    {
        Gate::authorize('create', Evento::class);

        $user = auth()->user();
        $congregacoes = $getCongregacoesAction->execute($user);
        
        $evento = new Evento;
        if ($user->isLiderCongregacao()) {
            $evento->congregacao_id = $user->congregacao_id;
        }

        return view('eventos.form', compact('congregacoes', 'evento'));
    }

    public function store(
        StoreEventoRequest $request,
        CreateEventoAction $action
    ): RedirectResponse {
        Gate::authorize('create', Evento::class);

        $dto = CreateEventoDTO::fromArray($request->validated());
        $action->execute($dto, $request->user());

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento criado com sucesso.');
    }

    public function edit(
        Evento $evento,
        GetCongregacoesDisponiveisParaUsuarioAction $getCongregacoesAction
    ): View {
        Gate::authorize('update', $evento);

        $congregacoes = $getCongregacoesAction->execute(auth()->user());

        return view('eventos.form', compact('congregacoes', 'evento'));
    }

    public function update(
        UpdateEventoRequest $request,
        Evento $evento,
        UpdateEventoAction $action
    ): RedirectResponse {
        Gate::authorize('update', $evento);

        $dto = UpdateEventoDTO::fromArray($request->validated());
        $action->execute($evento, $dto, $request->user());

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento atualizado com sucesso.');
    }

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