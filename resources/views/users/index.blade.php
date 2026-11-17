@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Usuários</h1>
        <a href="{{ route('users.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Novo Usuário
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perfil</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Congregação</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="ml-2 text-xs text-indigo-600 font-semibold">(você)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                                {{ $u->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->congregacao?->nome ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('users.edit', $u) }}"
                               class="text-indigo-600 hover:underline">Editar</a>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u) }}" method="POST"
                                      class="inline ml-2"
                                      onsubmit="return confirm('Excluir este usuário?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Excluir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection