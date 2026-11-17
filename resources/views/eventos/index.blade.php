@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Eventos</h1>
        @can('create', App\Models\Evento::class)
            <a href="{{ route('eventos.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Novo Evento
            </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('eventos.index') }}" class="bg-white rounded shadow p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="busca" value="{{ $filtros['busca'] ?? '' }}"
                   placeholder="Buscar evento ou anotação..."
                   class="w-full border-gray-300 rounded px-3 py-2 border text-sm">

            <select name="congregacao_id" class="w-full border-gray-300 rounded px-3 py-2 border text-sm">
                <option value="">Todas as congregações</option>
                @foreach($congregacoes as $c)
                    <option value="{{ $c->id }}" @selected(($filtros['congregacao_id'] ?? '') == $c->id)>
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>

            <select name="situacao" class="w-full border-gray-300 rounded px-3 py-2 border text-sm">
                <option value="">Todas as situações</option>
                @foreach(\App\Domain\Eventos\Enums\SituacaoEvento::cases() as $s)
                    <option value="{{ $s->value }}" @selected(($filtros['situacao'] ?? '') === $s->value)>
                        {{ $s->label() }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="de" value="{{ $filtros['de'] ?? '' }}"
                   placeholder="De"
                   class="w-full border-gray-300 rounded px-3 py-2 border text-sm">

            <input type="date" name="ate" value="{{ $filtros['ate'] ?? '' }}"
                   placeholder="Até"
                   class="w-full border-gray-300 rounded px-3 py-2 border text-sm">
        </div>
        <div class="flex gap-2 mt-3">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                Filtrar
            </button>
            <a href="{{ route('eventos.index') }}"
               class="px-4 py-2 rounded border border-gray-300 text-sm hover:bg-gray-50">
                Limpar
            </a>
            <span class="ml-auto text-xs text-gray-500 self-center">
                {{ $eventos->total() }} evento(s) encontrado(s)
            </span>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Congregação</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($eventos as $evento)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $evento->evento }}</div>
                            @if($evento->anotacoes)
                                <div class="text-xs text-gray-500 truncate max-w-md">{{ $evento->anotacoes }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $evento->congregacao->nome }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $evento->dt_evento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $evento->situacao->cor() }}">
                                {{ $evento->situacao->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            @can('update', $evento)
                                <a href="{{ route('eventos.edit', $evento) }}"
                                   class="text-indigo-600 hover:underline">Editar</a>
                            @endcan
                            @can('delete', $evento)
                                <form action="{{ route('eventos.destroy', $evento) }}" method="POST"
                                      class="inline ml-2"
                                      onsubmit="return confirm('Excluir este evento?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Excluir</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum evento encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $eventos->links() }}</div>
@endsection