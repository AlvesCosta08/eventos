
@extends('layouts.app')

@php
    $isEdit = $user->exists;
    $perfilSelecionado = old('role', $user->role?->value ?? 'visualizador');
@endphp

@section('title', $isEdit ? 'Editar usuário' : 'Novo usuário')
@section('page_title', $isEdit ? 'Editar usuário' : 'Cadastrar usuário')

@section('content')

{{-- Cabeçalho --}}
<section class="mb-8">
    <a href="{{ route('users.index') }}"
       class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-blue-600">
        <svg class="h-4 w-4" viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Voltar para usuários
    </a>

    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
            <svg class="h-6 w-6" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M19 8v6M16 11h6"/>
            </svg>
        </div>

        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-blue-600">
                Administração
            </p>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                {{ $isEdit ? 'Editar usuário' : 'Novo usuário' }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ $isEdit
                    ? 'Atualize os dados e as permissões do usuário.'
                    : 'Preencha as informações para cadastrar um novo usuário.' }}
            </p>
        </div>
    </div>
</section>

<form
    action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}"
    method="POST"
    class="space-y-6"
>
    @csrf

    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Resumo dos erros --}}
    @if($errors->any())
        <div role="alert"
             class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <h2 class="text-sm font-bold text-red-800">
                Verifique os dados informados
            </h2>

            <ul class="mt-3 list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Dados pessoais --}}
    <section class="adtc-card overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                    </svg>
                </span>

                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Dados do usuário
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Informações de identificação e acesso.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

            {{-- Nome --}}
            <div>
                <label for="name"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Nome completo <span class="text-red-500">*</span>
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    placeholder="Nome do usuário"
                    maxlength="150"
                    autocomplete="name"
                    required
                    @error('name')
                        aria-invalid="true"
                        aria-describedby="name-error"
                    @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('name')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >

                @error('name')
                    <p id="name-error" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- E-mail --}}
            <div>
                <label for="email"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    E-mail <span class="text-red-500">*</span>
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    placeholder="usuario@exemplo.com"
                    maxlength="150"
                    autocomplete="email"
                    required
                    @error('email')
                        aria-invalid="true"
                        aria-describedby="email-error"
                    @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('email')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >

                @error('email')
                    <p id="email-error" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Senha --}}
            <div class="md:col-span-2">
                <label for="password"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Senha
                    @if(!$isEdit)
                        <span class="text-red-500">*</span>
                    @else
                        <span class="ml-1 text-xs font-normal text-slate-400">
                            (opcional na edição)
                        </span>
                    @endif
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="{{ $isEdit ? 'Deixe em branco para manter a senha' : 'Digite uma senha segura' }}"
                    minlength="8"
                    autocomplete="new-password"
                    aria-describedby="password-help @if($errors->has('password')) password-error @endif"
                    @if(!$isEdit) required @endif
                    @error('password') aria-invalid="true" @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('password')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >

                <p id="password-help" class="mt-2 text-xs text-slate-500">
                    @if($isEdit)
                        Deixe em branco para manter a senha atual.
                        Para alterar, informe pelo menos 8 caracteres.
                    @else
                        A senha deve conter pelo menos 8 caracteres.
                    @endif
                </p>

                @error('password')
                    <p id="password-error" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </section>

    {{-- Perfil e congregação --}}
    <section class="adtc-card overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </span>

                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Perfil e permissões
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Configure o nível de acesso e a congregação.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

            {{-- Perfil --}}
            <div>
                <label for="role"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Perfil de acesso <span class="text-red-500">*</span>
                </label>

                <select
                    id="role"
                    name="role"
                    required
                    @error('role')
                        aria-invalid="true"
                        aria-describedby="role-error"
                    @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('role')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
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
                    <p id="role-error" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs text-slate-500">
                    O perfil determina as funcionalidades disponíveis para o usuário.
                </p>
            </div>

            {{-- Congregação --}}
            <div
                id="campo-cong"
                @if($perfilSelecionado !== 'lider_congregacao') hidden @endif
            >
                <label for="congregacao_id"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Congregação
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="congregacao_id"
                    name="congregacao_id"
                    @if($perfilSelecionado === 'lider_congregacao') required @endif
                    @error('congregacao_id')
                        aria-invalid="true"
                        aria-describedby="congregacao-error"
                    @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('congregacao_id')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
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
                    <p id="congregacao-error" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs text-slate-500">
                    Obrigatória para usuários com perfil de líder de congregação.
                </p>
            </div>

        </div>
    </section>

    {{-- Ações --}}
    <div class="flex flex-col-reverse gap-3 pb-6 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ route('users.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Cancelar
        </a>

        <button type="submit"
                class="adtc-button-primary justify-center">
            <svg class="h-4 w-4" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M5 12l4 4L19 6"/>
            </svg>

            {{ $isEdit ? 'Salvar alterações' : 'Cadastrar usuário' }}
        </button>
    </div>

</form>

{{-- Controle da congregação conforme o perfil --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const perfil = document.getElementById('role');
        const campo = document.getElementById('campo-cong');
        const congregacao = document.getElementById('congregacao_id');

        if (!perfil || !campo || !congregacao) {
            return;
        }

        function atualizarCampoCongregacao() {
            const lider = perfil.value === 'lider_congregacao';

            campo.hidden = !lider;
            congregacao.required = lider;
        }

        perfil.addEventListener('change', atualizarCampoCongregacao);

        atualizarCampoCongregacao();
    });
</script>

@endsection
