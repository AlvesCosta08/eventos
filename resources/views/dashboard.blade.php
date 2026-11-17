@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

    {{-- Cards de totais --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Total</div>
            <div class="text-2xl font-bold text-gray-800">{{ $totais['total'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Próximos 30d</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $totais['proximos30'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Agendados</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totais['agendado'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Realizados</div>
            <div class="text-2xl font-bold text-green-600">{{ $totais['realizado'] }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-gray-500 uppercase">Cancelados</div>
            <div class="text-2xl font-bold text-red-600">{{ $totais['cancelado'] }}</div>
        </div>
    </div>

    {{-- Próximos 7 dias --}}
    <div class="bg-white rounded shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">🗓️ Próximos 7 dias</h2>
        @if($proximos7->isEmpty())
            <p class="text-gray-500 text-sm">Nenhum evento nos próximos 7 dias.</p>
        @else
            <div class="space-y-2">
                @foreach($proximos7 as $ev)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <span class="font-medium text-gray-800">{{ $ev->evento }}</span>
                            <span class="text-xs text-gray-500 ml-2">· {{ $ev->congregacao->nome }}</span>
                        </div>
                        <div class="flex gap-2 items-center">
                            <span class="px-2 py-1 rounded text-xs {{ $ev->situacao->cor() }}">{{ $ev->situacao->label() }}</span>
                            <span class="text-sm text-gray-700">{{ $ev->dt_evento->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        {{-- Próximos eventos --}}
        <div class="md:col-span-2 bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">⏭️ Próximos eventos</h2>
            @if($proximosEventos->isEmpty())
                <p class="text-gray-500 text-sm">Nenhum evento futuro.</p>
            @else
                <div class="space-y-2">
                    @foreach($proximosEventos as $ev)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <span class="font-medium text-gray-800">{{ $ev->evento }}</span>
                                <span class="text-xs text-gray-500 ml-2">· {{ $ev->congregacao->nome }}</span>
                            </div>
                            <span class="text-sm text-gray-700">{{ $ev->dt_evento->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Top congregações --}}
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🏛️ Top congregações</h2>
            <div class="space-y-2">
                @foreach($congregacoes as $cong)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $cong->nome }}</span>
                        <span class="text-gray-500">{{ $cong->eventos_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection