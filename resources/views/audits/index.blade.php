@extends('layouts.app')

@section('title', 'Auditoria do Sistema')
@section('page_title', 'Histórico de Auditoria')

@section('content')

<section class="mb-8">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>

        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-indigo-600">
                Segurança e Rastreabilidade
            </p>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                Histórico de Auditoria do Sistema
            </h1>
            <p class="mt-2 text-sm text-slate-500">
                Registro detalhado de todas as ações de criação, atualização e exclusão realizadas no sistema.
            </p>
        </div>
    </div>
</section>

<section class="adtc-card overflow-hidden">
    <div class="p-6 text-gray-900">
        @if($audits->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700">Nenhuma ação registrada ainda.</p>
                <p class="mt-2 text-xs text-slate-400">As ações de criação, edição e exclusão aparecerão aqui automaticamente.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Data/Hora</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Usuário</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ação</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Modelo</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Mudanças</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($audits as $audit)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    {{ $audit->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ $audit->user ? $audit->user->name : 'Sistema/Desconhecido' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $colors = [
                                            'created' => 'bg-green-100 text-green-800',
                                            'updated' => 'bg-amber-100 text-amber-800',
                                            'deleted' => 'bg-rose-100 text-rose-800',
                                            'restored' => 'bg-blue-100 text-blue-800',
                                        ];
                                        $color = $colors[$audit->event] ?? 'bg-slate-100 text-slate-800';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $color }}">
                                        {{ strtoupper($audit->event) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    @if($audit->event === 'created')
                                        <span class="text-slate-500">Registro criado</span>
                                    @elseif($audit->event === 'deleted')
                                        <span class="text-rose-600 font-medium">Registro excluído</span>
                                    @else
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($audit->getModified() as $attribute => $modified)
                                                <li>
                                                    <span class="font-semibold text-slate-800">{{ ucfirst($attribute) }}:</span>
                                                    <span class="text-rose-500 line-through">{{ $modified['old'] ?? 'null' }}</span>
                                                    <span class="text-slate-400 mx-1">→</span>
                                                    <span class="text-emerald-600 font-medium">{{ $modified['new'] ?? 'null' }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-5">
                {{ $audits->links() }}
            </div>
        @endif
    </div>
</section>

@endsection