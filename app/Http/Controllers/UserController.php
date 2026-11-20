<?php

namespace App\Http\Controllers;

use App\Domain\Congregacoes\Actions\GetCongregacoesDisponiveisParaUsuarioAction;
use App\Domain\Users\Actions\CreateUserAction;
use App\Domain\Users\Actions\DeleteUserAction;
use App\Domain\Users\Actions\UpdateUserAction;
use App\Domain\Users\DTOs\CreateUserDTO;
use App\Domain\Users\DTOs\UpdateUserDTO;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with('congregacao')
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(GetCongregacoesDisponiveisParaUsuarioAction $getCongregacoesAction): View
    {
        $this->authorize('create', User::class);
        
        $user = new User;
        $congregacoes = $getCongregacoesAction->execute(auth()->user());

        return view('users.form', compact('congregacoes', 'user'));
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $this->authorize('create', User::class);

        // ✅ CORREÇÃO: Converter o array validado em DTO antes de passar para a Action
        $dto = CreateUserDTO::fromArray($request->validated());
        $action->execute($dto);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user, GetCongregacoesDisponiveisParaUsuarioAction $getCongregacoesAction): View
    {
        $this->authorize('update', $user);
        
        $congregacoes = $getCongregacoesAction->execute(auth()->user());

        return view('users.form', compact('congregacoes', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $this->authorize('update', $user);

        // ✅ CORREÇÃO: Converter o array validado em DTO antes de passar para a Action
        $dto = UpdateUserDTO::fromArray($request->validated());
        $action->execute($user, $dto);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        $this->authorize('delete', $user);
        
        $action->execute($user);

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}