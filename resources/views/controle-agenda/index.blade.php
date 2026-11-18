
@extends('layouts.app')

@section('title', 'Controle da Agenda')
@section('page_title', 'Controle da Agenda Anual')

@section('content')
<div class="space-y-6">

    {{-- Cabeçalho --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                Controle da Agenda Anual
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Configure quais líderes podem gerenciar eventos em cada ano.
            </p>
        </div>
    </div>

    {{-- Seleção do ano --}}
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold">Selecionar ano</h2>

        <form
            action="{{ route('controle-agenda.index') }}"
            method="GET"
            class="flex flex-wrap items-end gap-3"
        >
            <div>
                <label for="ano" class="mb-1 block text-sm font-medium">
                    Ano da agenda
                </label>
                <input
                    type="number"
                    id="ano"
                    name="ano"
                    min="1"
                    max="9999"
                    value="{{ $ano }}"
                    required
                    class="w-36 rounded-lg border border-gray-300 px-3 py-2"
                >
            </div>

            <button
                type="submit"
                class="rounded-lg bg-slate-800 px-5 py-2 text-white hover:bg-slate-700"
            >
                Consultar
            </button>
        </form>

        @error('ano')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @if($controles->isNotEmpty())
            <div class="mt-5">
                <p class="mb-2 text-sm font-medium">
                    Anos já configurados
                </p>

                <div class="flex flex-wrap gap-2">
                    @foreach($controles as $controle)
                        <a
                            href="{{ route('controle-agenda.index', ['ano' => $controle->ano]) }}"
                            class="rounded-lg border px-3 py-1.5 text-sm
                                {{ $controle->ano === $ano
                                    ? 'border-slate-800 bg-slate-800 text-white'
                                    : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}"
                        >
                            {{ $controle->ano }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- Modo atual e alteração --}}
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold">
                    Configuração de {{ $ano }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    O modo é independente para cada ano.
                </p>
            </div>

            <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-800">
                {{ $modoAtual->label() }}
            </span>
        </div>

        <form
            action="{{ route('controle-agenda.update', ['ano' => $ano]) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <fieldset>
                <legend class="mb-3 text-sm font-semibold">
                    Selecione o modo de funcionamento
                </legend>

                <div class="grid gap-3 md:grid-cols-3">
                    @foreach($modos as $modo)
                        <label class="flex cursor-pointer gap-3 rounded-xl border border-gray-200 p-4 hover:bg-gray-50">
                            <input
                                type="radio"
                                name="modo"
                                value="{{ $modo->value }}"
                                class="mt-1"
                                {{ old('modo', $modoAtual->value) === $modo->value ? 'checked' : '' }}
                                required
                            >

                            <span>
                                <strong class="block text-sm">
                                    {{ $modo->label() }}
                                </strong>

                                <span class="mt-2 block text-sm text-gray-500">
                                    @switch($modo->value)
                                        @case('bloqueada')
                                            Apenas o Administrador Geral pode gerenciar eventos.
                                            @break

                                        @case('somente_sede')
                                            O Administrador Geral e os líderes da Sede podem gerenciar eventos de suas respectivas competências.
                                            @break

                                        @case('liberada')
                                            O Administrador Geral e os líderes de todas as congregações podem gerenciar eventos, respeitando suas congregações.
                                            @break
                                    @endswitch
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            @error('modo')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-slate-800 px-5 py-2.5 font-medium text-white hover:bg-slate-700"
                >
                    Salvar configuração
                </button>

                <span class="text-sm text-gray-500">
                    Alterações de modo são registradas no histórico.
                </span>
            </div>
        </form>
    </section>

    {{-- Histórico --}}
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-semibold">
                Histórico de alterações — {{ $ano }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Registro de mudanças de configuração realizadas pelos administradores.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Data e hora</th>
                        <th class="px-4 py-3 font-semibold">Modo anterior</th>
                        <th class="px-4 py-3 font-semibold">Novo modo</th>
                        <th class="px-4 py-3 font-semibold">Responsável</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($historico as $registro)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3">
                                {{ $registro->created_at?->format('d/m/Y H:i:s') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $registro->modo_anterior->label() }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $registro->modo_novo->label() }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $registro->responsavel?->name ?? 'Usuário removido' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma alteração registrada para este ano.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($historico->hasPages())
            <div class="mt-5">
                {{ $historico->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
