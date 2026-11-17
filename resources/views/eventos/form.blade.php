@extends('layouts.app')

@section('content')
    @php $isEdit = $evento->exists; @endphp

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        {{ $isEdit ? 'Editar Evento' : 'Novo Evento' }}
    </h1>

    <form action="{{ $isEdit ? route('eventos.update', $evento) : route('eventos.store') }}"
          method="POST"
          class="bg-white rounded shadow p-6 space-y-4">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Evento *</label>
            <input type="text" name="evento" value="{{ old('evento', $evento->evento) }}"
                   class="w-full border-gray-300 rounded px-3 py-2 border" required maxlength="150">
            @error('evento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Anotações</label>
            <textarea name="anotacoes" rows="4"
                      class="w-full border-gray-300 rounded px-3 py-2 border">{{ old('anotacoes', $evento->anotacoes) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Congregação *</label>
                <select name="congregacao_id" class="w-full border-gray-300 rounded px-3 py-2 border" required>
                    <option value="">Selecione</option>
                    @foreach($congregacoes as $c)
                        <option value="{{ $c->id }}"
                            @selected(old('congregacao_id', $evento->congregacao_id) == $c->id)>
                            {{ $c->nome }}
                        </option>
                    @endforeach
                </select>
                @error('congregacao_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                <input type="date" name="dt_evento"
                       value="{{ old('dt_evento', $evento->dt_evento?->format('Y-m-d')) }}"
                       class="w-full border-gray-300 rounded px-3 py-2 border" required>
                @error('dt_evento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Situação *</label>
                <select name="situacao" class="w-full border-gray-300 rounded px-3 py-2 border" required>
                    @foreach(App\Domain\Eventos\Enums\SituacaoEvento::cases() as $s)
                        <option value="{{ $s->value }}"
                            @selected(old('situacao', $evento->situacao?->value ?? 'agendado') === $s->value)>
                            {{ $s->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                {{ $isEdit ? 'Atualizar' : 'Criar' }}
            </button>
            <a href="{{ route('eventos.index') }}"
               class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
@endsection