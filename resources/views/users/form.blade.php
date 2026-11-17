
@extends('layouts.app')

@section('content')
    @php
        $isEdit = $user->exists;
        $perfilSelecionado = old('role', $user->role?->value ?? 'visualizador');
    @endphp

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        {{ $isEdit ? 'Editar Usuário' : 'Novo Usuário' }}
    </h1>

    <form action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}"
          method="POST"
          class="bg-white rounded shadow p-6 space-y-4">
        @csrf

        @if($isEdit)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome *
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full rounded px-3 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror"
                    required
                    maxlength="150"
                    autocomplete="name"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >

                @error('name')
                    <p id="name-error" class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    E-mail *
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full rounded px-3 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror"
                    required
                    maxlength="150"
                    autocomplete="email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >

                @error('email')
                    <p id="email-error" class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Senha {{ $isEdit ? '(opcional na edição)' : '*' }}
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    class="w-full rounded px-3 py-2 border @error('password') border-red-500 @else border-gray-300 @enderror"
                    minlength="8"
                    autocomplete="new-password"
                    aria-describedby="password-help @error('password') password-error @enderror"
                    @if(!$isEdit) required @endif
                    @error('password') aria-invalid="true" @enderror
                >

                <p id="password-help" class="text-gray-500 text-sm mt-1">
                    @if($isEdit)
                        Deixe em branco para manter a senha atual.
                        Para alterar, informe pelo menos 8 caracteres.
                    @else
                        A senha deve conter pelo menos 8 caracteres.
                    @endif
                </p>

                @error('password')
                    <p id="password-error" class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                    Perfil *
                </label>

                <select
                    id="role"
                    name="role"
                    class="w-full rounded px-3 py-2 border @error('role') border-red-500 @else border-gray-300 @enderror"
                    required
                    @error('role') aria-invalid="true" aria-describedby="role-error" @enderror
                    onchange="atualizarCampoCongregacao()"
                >
                    @foreach(App\Domain\Users\Enums\UserRole::cases() as $r)
                        <option
                            value="{{ $r->value }}"
                            @selected($perfilSelecionado === $r->value)
                        >
                            {{ $r->label() }}
                        </option>
                    @endforeach
                </select>

                @error('role')
                    <p id="role-error" class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div
            id="campo-cong"
            style="display: {{ $perfilSelecionado === 'lider_congregacao' ? 'block' : 'none' }};"
        >
            <label for="congregacao_id" class="block text-sm font-medium text-gray-700 mb-1">
                Congregação (obrigatória para líder)
            </label>

            <select
                id="congregacao_id"
                name="congregacao_id"
                class="w-full rounded px-3 py-2 border @error('congregacao_id') border-red-500 @else border-gray-300 @enderror"
                @error('congregacao_id') aria-invalid="true" aria-describedby="congregacao-error" @enderror
            >
                <option value="">Selecione uma congregação</option>

                @foreach($congregacoes as $c)
                    <option
                        value="{{ $c->id }}"
                        @selected(old('congregacao_id', $user->congregacao_id) == $c->id)
                    >
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>

            @error('congregacao_id')
                <p id="congregacao-error" class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
            >
                {{ $isEdit ? 'Atualizar' : 'Criar' }}
            </button>

            <a
                href="{{ route('users.index') }}"
                class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50"
            >
                Cancelar
            </a>
        </div>
    </form>

    <script>
        function atualizarCampoCongregacao() {
            const perfil = document.getElementById('role').value;
            const campo = document.getElementById('campo-cong');
            const congregacao = document.getElementById('congregacao_id');

            const lider = perfil === 'lider_congregacao';

            campo.style.display = lider ? 'block' : 'none';
            congregacao.required = lider;
        }

        document.addEventListener('DOMContentLoaded', atualizarCampoCongregacao);
    </script>
@endsection
