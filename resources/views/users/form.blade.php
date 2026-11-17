@extends('layouts.app')

@section('content')
    @php $isEdit = $user->exists; @endphp

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        {{ $isEdit ? 'Editar Usuário' : 'Novo Usuário' }}
    </h1>

    <form action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}"
          method="POST"
          class="bg-white rounded shadow p-6 space-y-4">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border-gray-300 rounded px-3 py-2 border" required maxlength="150">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border-gray-300 rounded px-3 py-2 border" required maxlength="150">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Senha {{ $isEdit ? '(deixe em branco para manter)' : '*' }}
                </label>
                <input type="password" name="password"
                       class="w-full border-gray-300 rounded px-3 py-2 border"
                       @if(!$isEdit) required @endif minlength="6">
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perfil *</label>
                <select name="role" class="w-full border-gray-300 rounded px-3 py-2 border" required
                        onchange="document.getElementById('campo-cong').style.display = this.value === 'lider_congregacao' ? 'block' : 'none';">
                    @foreach(App\Domain\Users\Enums\UserRole::cases() as $r)
                        <option value="{{ $r->value }}"
                            @selected(old('role', $user->role?->value ?? 'visualizador') === $r->value)>
                            {{ $r->label() }}
                        </option>
                    @endforeach
                </select>
                @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div id="campo-cong"
             style="display: {{ old('role', $user->role?->value ?? '') === 'lider_congregacao' ? 'block' : 'none' }};">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Congregação (obrigatória para líder)
            </label>
            <select name="congregacao_id" class="w-full border-gray-300 rounded px-3 py-2 border">
                <option value="">Selecione</option>
                @foreach($congregacoes as $c)
                    <option value="{{ $c->id }}"
                        @selected(old('congregacao_id', $user->congregacao_id) == $c->id)>
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>
            @error('congregacao_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                {{ $isEdit ? 'Atualizar' : 'Criar' }}
            </button>
            <a href="{{ route('users.index') }}"
               class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
@endsection