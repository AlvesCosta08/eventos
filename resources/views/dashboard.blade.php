
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Visão geral')

@section('content')

{{-- Cabeçalho de boas-vindas --}}
<section class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                Painel de controle
            </span>
        </div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
            Olá, {{ auth()->user()->name }}!
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Acompanhe os eventos e as atividades da sua congregação.
        </p>
    </div>

    @if(auth()->user()->podeGerenciarEventos())
        <a href="{{ route('eventos.create') }}"
           class="adtc-button-primary self-start">
            <svg class="h-4 w-4" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Novo evento
        </a>
    @endif
</section>

{{-- Indicadores --}}
<section class="mb-8">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-800">
            Resumo dos eventos
        </h2>

        <span class="text-xs text-slate-400">
            Visão atual
        </span>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">

        {{-- Total --}}
        <div class="adtc-card p-5">
            <div class="mb-5 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">
                    Total de eventos
                </span>

                <span class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M7 3v4M17 3v4M3 10h18"/>
                    </svg>
                </span>
            </div>

            <div class="adtc-stat-value">
                {{ $totais['total'] }}
            </div>

            <p class="mt-2 text-xs text-slate-400">
                Eventos cadastrados
            </p>
        </div>

        {{-- Próximos 30 dias --}}
        <div class="adtc-card p-5">
            <div class="mb-5 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">
                    Próximos 30 dias
                </span>

                <span class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </span>
            </div>

            <div class="adtc-stat-value">
                {{ $totais['proximos30'] }}
            </div>

            <p class="mt-2 text-xs text-slate-400">
                Eventos no período
            </p>
        </div>

        {{-- Agendados --}}
        <div class="adtc-card p-5">
            <div class="mb-5 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">
                    Agendados
                </span>

                <span class="rounded-xl bg-sky-50 p-2.5 text-sky-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M7 3v4M17 3v4M3 10h18M8 15h8"/>
                    </svg>
                </span>
            </div>

            <div class="adtc-stat-value">
                {{ $totais['agendado'] }}
            </div>

            <p class="mt-2 text-xs text-slate-400">
                Aguardando realização
            </p>
        </div>

        {{-- Realizados --}}
        <div class="adtc-card p-5">
            <div class="mb-5 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">
                    Realizados
                </span>

                <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m8 12 3 3 5-6"/>
                    </svg>
                </span>
            </div>

            <div class="adtc-stat-value">
                {{ $totais['realizado'] }}
            </div>

            <p class="mt-2 text-xs text-slate-400">
                Eventos concluídos
            </p>
        </div>

        {{-- Cancelados --}}
        <div class="adtc-card p-5">
            <div class="mb-5 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">
                    Cancelados
                </span>

                <span class="rounded-xl bg-rose-50 p-2.5 text-rose-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m9 9 6 6M15 9l-6 6"/>
                    </svg>
                </span>
            </div>

            <div class="adtc-stat-value">
                {{ $totais['cancelado'] }}
            </div>

            <p class="mt-2 text-xs text-slate-400">
                Eventos cancelados
            </p>
        </div>

    </div>
</section>

{{-- Eventos dos próximos sete dias --}}
<section class="adtc-card mb-8 overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-6">
        <div>
            <h2 class="text-base font-bold text-slate-800">
                Próximos 7 dias
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Acompanhe os eventos mais próximos.
            </p>
        </div>

        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
            {{ $proximos7->count() }} evento(s)
        </span>
    </div>

    @if($proximos7->isEmpty())
        <div class="px-6 py-12 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <svg class="h-7 w-7" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="1.5" stroke-linecap="round"
                     stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M7 3v4M17 3v4M3 10h18"/>
                </svg>
            </div>

            <p class="text-sm font-semibold text-slate-700">
                Nenhum evento nos próximos 7 dias
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Os eventos programados aparecerão aqui.
            </p>
        </div>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($proximos7 as $ev)
                <div class="flex flex-col gap-4 px-6 py-4 transition-colors hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <strong class="text-lg leading-none">
                                {{ $ev->dt_evento->format('d') }}
                            </strong>

                            <span class="mt-1 text-[10px] font-semibold uppercase">
                                {{ $ev->dt_evento->format('M') }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-bold text-slate-800">
                                {{ $ev->evento }}
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $ev->congregacao->nome }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $ev->situacao->cor() }}">
                            {{ $ev->situacao->label() }}
                        </span>

                        <span class="text-xs font-medium text-slate-500">
                            {{ $ev->dt_evento->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- Próximos eventos e congregações --}}
<section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- Próximos eventos --}}
    <div class="adtc-card overflow-hidden xl:col-span-2">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-6">
            <div>
                <h2 class="text-base font-bold text-slate-800">
                    Próximos eventos
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Programação futura
                </p>
            </div>

            <a href="{{ route('eventos.index') }}"
               class="text-xs font-bold text-blue-600 transition-colors hover:text-blue-800">
                Ver todos
            </a>
        </div>

        @if($proximosEventos->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-slate-500">
                    Nenhum evento futuro encontrado.
                </p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($proximosEventos as $ev)
                    <div class="flex flex-col gap-3 px-6 py-4 transition-colors hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-800">
                                {{ $ev->evento }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $ev->congregacao->nome }}
                            </p>
                        </div>

                        <span class="shrink-0 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">
                            {{ $ev->dt_evento->format('d/m/Y') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Ranking de congregações --}}
    <div class="adtc-card overflow-hidden">
        <div class="border-b border-slate-100 p-6">
            <h2 class="text-base font-bold text-slate-800">
                Top congregações
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Quantidade de eventos cadastrados
            </p>
        </div>

        @if($congregacoes->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-slate-500">
                    Nenhuma congregação encontrada.
                </p>
            </div>
        @else
            <div class="space-y-5 p-6">
                @foreach($congregacoes as $cong)
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <span class="truncate text-xs font-semibold text-slate-700">
                                {{ $cong->nome }}
                            </span>

                            <span class="shrink-0 text-xs font-bold text-blue-700">
                                {{ $cong->eventos_count }}
                            </span>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full bg-blue-500 transition-all duration-700"
                                style="width: {{ $congregacoes->max('eventos_count') > 0
                                    ? round(($cong->eventos_count / $congregacoes->max('eventos_count')) * 100)
                                    : 0 }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</section>

@endsection
