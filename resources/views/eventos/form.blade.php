@extends('layouts.app')

@php
    $isEdit = $evento->exists;
@endphp

@section('title', $isEdit ? 'Editar evento' : 'Novo evento')
@section('page_title', $isEdit ? 'Editar evento' : 'Cadastrar evento')

@section('content')

{{-- Cabeçalho --}}
<section class="mb-8">
    <a href="{{ route('eventos.index') }}"
       class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-blue-600">
        <svg class="h-4 w-4" viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Voltar para eventos
    </a>

    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
            <svg class="h-6 w-6" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round"
                 stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M7 3v4M17 3v4M3 10h18"/>
                <path d="M8 15h3"/>
            </svg>
        </div>

        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-blue-600">
                Gestão de eventos
            </p>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                {{ $isEdit ? 'Editar evento' : 'Novo evento' }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                {{ $isEdit
                    ? 'Atualize as informações do evento selecionado.'
                    : 'Preencha os dados para cadastrar um novo evento.' }}
            </p>
        </div>
    </div>
</section>

{{-- Formulário --}}
<form
    action="{{ $isEdit ? route('eventos.update', $evento) : route('eventos.store') }}"
    method="POST"
    class="space-y-6"
>
    @csrf

    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Resumo de erros --}}
    @if($errors->any())
        <div role="alert"
             class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600"
                     viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 7v6M12 17h.01"/>
                </svg>

                <div>
                    <h2 class="text-sm font-bold text-red-800">
                        Verifique os dados informados
                    </h2>

                    <p class="mt-1 text-sm text-red-700">
                        Alguns campos precisam ser corrigidos antes de continuar.
                    </p>

                    <ul class="mt-3 list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Informações principais --}}
    <section class="adtc-card overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M8 6h13M8 12h13M8 18h13"/>
                        <path d="M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </span>

                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Informações do evento
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Identificação e descrição do evento.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-5 p-6">

            {{-- Nome --}}
            <div>
                <label for="evento"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Nome do evento
                    <span class="text-red-500">*</span>
                </label>

                <input
                    id="evento"
                    type="text"
                    name="evento"
                    value="{{ old('evento', $evento->evento) }}"
                    placeholder="Ex.: Encontro de jovens"
                    maxlength="150"
                    required
                    aria-invalid="{{ $errors->has('evento') ? 'true' : 'false' }}"
                    @error('evento') aria-describedby="erro-evento" @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('evento')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >

                @error('evento')
                    <p id="erro-evento" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs text-slate-400">
                    Máximo de 150 caracteres.
                </p>
            </div>

            {{-- Anotações --}}
            <div>
                <label for="anotacoes"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Anotações
                </label>

                <textarea
                    id="anotacoes"
                    name="anotacoes"
                    rows="5"
                    placeholder="Adicione observações ou informações complementares..."
                    aria-invalid="{{ $errors->has('anotacoes') ? 'true' : 'false' }}"
                    @error('anotacoes') aria-describedby="erro-anotacoes" @enderror
                    class="w-full resize-y rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('anotacoes')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >{{ old('anotacoes', $evento->anotacoes) }}</textarea>

                @error('anotacoes')
                    <p id="erro-anotacoes" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs text-slate-400">
                    Campo opcional.
                </p>
            </div>

        </div>
    </section>

    {{-- Organização e agendamento --}}
    <section class="adtc-card overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M7 3v4M17 3v4M3 10h18"/>
                    </svg>
                </span>

                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Organização e agendamento
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Defina o tipo, a congregação, a data e a situação.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-3">

            {{-- ✅ NOVO CAMPO: Tipo do Evento --}}
            <div>
                <label for="tipo"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Tipo do evento
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    required
                    aria-invalid="{{ $errors->has('tipo') ? 'true' : 'false' }}"
                    @error('tipo') aria-describedby="erro-tipo" @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('tipo')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >
                    <option value="">Selecione o tipo</option>
                    <option value="campo" @selected(old('tipo', $evento->tipo?->value ?? 'congregacao') === 'campo')>Campo</option>
                    <option value="congregacao" @selected(old('tipo', $evento->tipo?->value ?? 'congregacao') === 'congregacao')>Congregação</option>
                </select>

                @error('tipo')
                    <p id="erro-tipo" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs text-slate-400">
                    Apenas um evento de "Campo" é permitido por dia.
                </p>
            </div>

            {{-- Congregação --}}
            <div>
                <label for="congregacao_id"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Congregação
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="congregacao_id"
                    name="congregacao_id"
                    required
                    aria-invalid="{{ $errors->has('congregacao_id') ? 'true' : 'false' }}"
                    @error('congregacao_id') aria-describedby="erro-congregacao" @enderror
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
                            @selected(old('congregacao_id', $evento->congregacao_id) == $c->id)
                        >
                            {{ $c->nome }}
                        </option>
                    @endforeach
                </select>

                @error('congregacao_id')
                    <p id="erro-congregacao" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Data --}}
            <div>
                <label for="dt_evento"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Data do evento
                    <span class="text-red-500">*</span>
                </label>

                <input
                    id="dt_evento"
                    type="date"
                    name="dt_evento"
                    value="{{ old('dt_evento', $evento->dt_evento?->format('Y-m-d')) }}"
                    required
                    aria-invalid="{{ $errors->has('dt_evento') ? 'true' : 'false' }}"
                    @error('dt_evento') aria-describedby="erro-data" @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('dt_evento')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >

                @error('dt_evento')
                    <p id="erro-data" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Situação --}}
            <div>
                <label for="situacao"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Situação
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="situacao"
                    name="situacao"
                    required
                    aria-invalid="{{ $errors->has('situacao') ? 'true' : 'false' }}"
                    @error('situacao') aria-describedby="erro-situacao" @enderror
                    class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:ring-2
                        @error('situacao')
                            border-red-300 focus:border-red-500 focus:ring-red-100
                        @else
                            border-slate-200 focus:border-blue-500 focus:ring-blue-100
                        @enderror"
                >
                    @foreach(App\Domain\Eventos\Enums\SituacaoEvento::cases() as $s)
                        <option
                            value="{{ $s->value }}"
                            @selected(old('situacao', $evento->situacao?->value ?? 'agendado') === $s->value)
                        >
                            {{ $s->label() }}
                        </option>
                    @endforeach
                </select>

                @error('situacao')
                    <p id="erro-situacao" class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </section>

    {{-- Ações --}}
    <div class="flex flex-col-reverse gap-3 pb-6 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ route('eventos.index') }}"
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

            {{ $isEdit ? 'Salvar alterações' : 'Cadastrar evento' }}
        </button>
    </div>

</form>

@endsection