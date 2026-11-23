@extends('layouts.app')

@section('title', 'Eventos')
@section('page_title', 'Gerenciamento de eventos')

@section('content')

{{-- Cabeçalho --}}
<section class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
            <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                Gestão de eventos
            </span>
        </div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
            Eventos
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Consulte, filtre e gerencie os eventos cadastrados.
        </p>
    </div>

    @can('create', App\Models\Evento::class)
        <a href="{{ route('eventos.create') }}"
           class="adtc-button-primary self-start">
            <svg class="h-4 w-4" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Novo evento
        </a>
    @endcan
</section>

{{-- Filtros --}}
<section class="adtc-card mb-6 overflow-hidden">
    <div class="border-b border-slate-100 px-6 py-5">
        <div class="flex items-center gap-3">
            <span class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M4 7h16M7 12h10M10 17h4"/>
                </svg>
            </span>

            <div>
                <h2 class="text-sm font-bold text-slate-800">
                    Filtros de pesquisa
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Refine a listagem de eventos.
                </p>
            </div>
        </div>
    </div>

    <form method="GET"
          action="{{ route('eventos.index') }}"
          class="p-6">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">

            {{-- Busca --}}
            <div class="xl:col-span-2">
                <label for="busca"
                       class="mb-2 block text-xs font-semibold text-slate-600">
                    Buscar evento
                </label>

                <input
                    id="busca"
                    type="text"
                    name="busca"
                    value="{{ $filtros['busca'] ?? '' }}"
                    placeholder="Nome ou anotação..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            {{-- ✅ NOVO FILTRO: Tipo --}}
            <div>
                <label for="tipo"
                       class="mb-2 block text-xs font-semibold text-slate-600">
                    Tipo
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">Todos os tipos</option>
                    <option value="campo" @selected(($filtros['tipo'] ?? '') === 'campo')>Campo</option>
                    <option value="congregacao" @selected(($filtros['tipo'] ?? '') === 'congregacao')>Congregação</option>
                </select>
            </div>

            {{-- Congregação --}}
            <div>
                <label for="congregacao_id"
                       class="mb-2 block text-xs font-semibold text-slate-600">
                    Congregação
                </label>

                <select
                    id="congregacao_id"
                    name="congregacao_id"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">Todas as congregações</option>

                    @foreach($congregacoes as $c)
                        <option
                            value="{{ $c->id }}"
                            @selected(($filtros['congregacao_id'] ?? '') == $c->id)
                        >
                            {{ $c->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Situação --}}
            <div>
                <label for="situacao"
                       class="mb-2 block text-xs font-semibold text-slate-600">
                    Situação
                </label>

                <select
                    id="situacao"
                    name="situacao"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">Todas as situações</option>

                    @foreach(\App\Domain\Eventos\Enums\SituacaoEvento::cases() as $s)
                        <option
                            value="{{ $s->value }}"
                            @selected(($filtros['situacao'] ?? '') === $s->value)
                        >
                            {{ $s->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Data inicial --}}
            <div>
                <label for="de"
                       class="mb-2 block text-xs font-semibold text-slate-600">
                    Data inicial
                </label>

                <input
                    id="de"
                    type="date"
                    name="de"
                    value="{{ $filtros['de'] ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            {{-- Data final --}}
            <div>
                <label for="ate"
                       class="mb-2 block text-xs font-semibold text-slate-600">
                    Data final
                </label>

                <input
                    id="ate"
                    type="date"
                    name="ate"
                    value="{{ $filtros['ate'] ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
            </div>

        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-5">

            <button type="submit"
                    class="adtc-button-primary">
                <svg class="h-4 w-4" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round">
                    <circle cx="10.5" cy="10.5" r="6.5"/>
                    <path d="m16 16 5 5"/>
                </svg>
                Aplicar filtros
            </button>

            <a href="{{ route('eventos.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Limpar
            </a>

            <span class="w-full text-xs font-medium text-slate-500 sm:ml-auto sm:w-auto">
                {{ $eventos->total() }} evento(s) encontrado(s)
            </span>
        </div>

    </form>
</section>

{{-- Listagem --}}
<section class="adtc-card overflow-hidden">

    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
        <div>
            <h2 class="text-base font-bold text-slate-800">
                Lista de eventos
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Eventos cadastrados conforme os filtros selecionados.
            </p>
        </div>

        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
            {{ $eventos->total() }} registro(s)
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Evento
                    </th>

                    {{-- ✅ NOVA COLUNA: Tipo --}}
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Tipo
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Congregação
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Data
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        Situação
                    </th>

                    <th scope="col"
                        class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                        Ações
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($eventos as $evento)
                    <tr class="transition-colors hover:bg-slate-50">

                        {{-- Evento --}}
                        <td class="max-w-sm px-6 py-4">
                            <div class="font-semibold text-slate-800">
                                {{ $evento->evento }}
                            </div>

                            @if($evento->anotacoes)
                                <p class="mt-1 max-w-xs truncate text-xs text-slate-500"
                                   title="{{ $evento->anotacoes }}">
                                    {{ $evento->anotacoes }}
                                </p>
                            @endif
                        </td>

                        {{-- ✅ NOVA COLUNA: Dados do Tipo --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($evento->tipo?->value === 'campo')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                    Campo
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                    Congregação
                                </span>
                            @endif
                        </td>

                        {{-- Congregação --}}
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-600">
                                {{ $evento->congregacao->nome }}
                            </span>
                        </td>

                        {{-- Data --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                <svg class="h-4 w-4 text-slate-400"
                                     viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path d="M7 3v4M17 3v4M3 10h18"/>
                                </svg>

                                {{ $evento->dt_evento->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Situação --}}
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold {{ $evento->situacao->cor() }}">
                                {{ $evento->situacao->label() }}
                            </span>
                        </td>

                        {{-- Ações --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">

                                @can('update', $evento)
                                    <a
                                        href="{{ route('eventos.edit', $evento) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
                                        title="Editar evento"
                                    >
                                        <svg class="h-3.5 w-3.5"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="m16 4 4 4-11 11-4 1 1-4L17 5a2.12 2.12 0 0 1 3 3"/>
                                        </svg>
                                        Editar
                                    </a>
                                @endcan

                                @can('delete', $evento)
                                    <form
                                        action="{{ route('eventos.destroy', $evento) }}"
                                        method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este evento?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                                            title="Excluir evento"
                                        >
                                            <svg class="h-3.5 w-3.5"
                                                 viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor"
                                                 stroke-width="2" stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 10v6M14 10v6"/>
                                            </svg>
                                            Excluir
                                        </button>
                                    </form>
                                @endcan

                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- ✅ Atualizado de colspan="5" para colspan="6" por causa da nova coluna --}}
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <svg class="h-7 w-7"
                                     viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor"
                                     stroke-width="1.5" stroke-linecap="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path d="M7 3v4M17 3v4M3 10h18"/>
                                </svg>
                            </div>

                            <p class="text-sm font-bold text-slate-700">
                                Nenhum evento encontrado
                            </p>

                            <p class="mt-2 text-xs text-slate-400">
                                Ajuste os filtros para encontrar outros eventos.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($eventos->hasPages())
        <div class="border-t border-slate-100 px-6 py-5">
            {{ $eventos->links() }}
        </div>
    @endif

</section>

@endsection