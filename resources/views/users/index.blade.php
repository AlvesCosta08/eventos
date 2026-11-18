
@extends('layouts.app')

@section('title', 'Usuários')
@section('page_title', 'Gerenciamento de usuários')

@section('content')

{{-- Cabeçalho --}}
<section class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
            <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                Administração
            </span>
        </div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
            Usuários
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Gerencie os usuários, perfis e congregações do sistema.
        </p>
    </div>

    @can('create', App\Models\User::class)
        <a href="{{ route('users.create') }}"
           class="adtc-button-primary self-start">
            <svg class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Novo usuário
        </a>
    @endcan
</section>

{{-- Listagem --}}
<section class="adtc-card overflow-hidden">

    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
        <div class="flex items-center gap-3">
            <span class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                <svg class="h-5 w-5"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>

            <div>
                <h2 class="text-base font-bold text-slate-800">
                    Usuários cadastrados
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Relação de contas e permissões de acesso.
                </p>
            </div>
        </div>

        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
            {{ $users->total() }} usuário(s)
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Usuário
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        E-mail
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Perfil
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Congregação
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                        Ações
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($users as $u)
                    <tr class="transition-colors hover:bg-slate-50">

                        {{-- Nome --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-700">
                                    {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                                </div>

                                <div>
                                    <div class="font-semibold text-slate-800">
                                        {{ $u->name }}
                                    </div>

                                    @if($u->id === auth()->id())
                                        <span class="mt-1 inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                            Você
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- E-mail --}}
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">
                                {{ $u->email }}
                            </span>
                        </td>

                        {{-- Perfil --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">
                                {{ $u->role->label() }}
                            </span>
                        </td>

                        {{-- Congregação --}}
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">
                                {{ $u->congregacao?->nome ?? '—' }}
                            </span>
                        </td>

                        {{-- Ações --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">

                                @can('update', $u)
                                    <a
                                        href="{{ route('users.edit', $u) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
                                        title="Editar usuário"
                                    >
                                        <svg class="h-3.5 w-3.5"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="m16 4 4 4-11 11-4 1 1-4L17 5a2.12 2.12 0 0 1 3 3"/>
                                        </svg>
                                        Editar
                                    </a>
                                @endcan

                                @if($u->id !== auth()->id())
                                    @can('delete', $u)
                                        <form
                                            action="{{ route('users.destroy', $u) }}"
                                            method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                                                title="Excluir usuário"
                                            >
                                                <svg class="h-3.5 w-3.5"
                                                     viewBox="0 0 24 24"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="2"
                                                     stroke-linecap="round"
                                                     stroke-linejoin="round">
                                                    <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 10v6M14 10v6"/>
                                                </svg>
                                                Excluir
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <svg class="h-7 w-7"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.5"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M2 21v-2a7 7 0 0 1 14 0v2M19 8v6M16 11h6"/>
                                </svg>
                            </div>

                            <p class="text-sm font-bold text-slate-700">
                                Nenhum usuário encontrado
                            </p>

                            <p class="mt-2 text-xs text-slate-400">
                                Os usuários cadastrados aparecerão aqui.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($users->hasPages())
        <div class="border-t border-slate-100 px-6 py-5">
            {{ $users->links() }}
        </div>
    @endif

</section>

@endsection
