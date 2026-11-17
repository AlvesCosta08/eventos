<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADTC2 Eventos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg text-indigo-700">📅 ADTC2</a>
                @auth
                    <div class="hidden md:flex gap-4 text-sm">
                        <a href="{{ route('dashboard') }}"
                           class="{{ request()->routeIs('dashboard') ? 'text-indigo-700 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('eventos.index') }}"
                           class="{{ request()->routeIs('eventos.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
                            Eventos
                        </a>
                        @if(auth()->user()->isAdminGeral())
                            <a href="{{ route('users.index') }}"
                               class="{{ request()->routeIs('users.*') ? 'text-indigo-700 font-semibold' : 'text-gray-600 hover:text-indigo-600' }}">
                                Usuários
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
            @auth
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                        {{ auth()->user()->role->label() }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="text-red-600 hover:underline">Sair</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>