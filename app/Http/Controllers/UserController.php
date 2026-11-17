<?php

namespace App\Http\Controllers;

use App\Domain\Users\Actions\CreateUserAction;
use App\Domain\Users\Actions\DeleteUserAction;
use App\Domain\Users\Actions\UpdateUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('congregacao')->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);
        $congregacoes = Congregacao::orderBy('nome')->get();
        $user = new User();

        return view('users.form', compact('congregacoes', 'user'));
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        $congregacoes = Congregacao::orderBy('nome')->get();

        return view('users.form', compact('congregacoes', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $action->execute($user, $request->validated());

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