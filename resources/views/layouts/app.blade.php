
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
        <div class="max-w-7xl mx-auto px-4 py-3">

            <div class="flex flex-wrap items-center justify-between gap-3">

                <a href="{{ route('dashboard') }}"
                   class="font-bold text-lg text-indigo-700">
                    📅 ADTC2
                </a>

                @auth
                    <div class="flex flex-wrap items-center gap-3 text-sm">

                        <span class="text-gray-600">
                            {{ auth()->user()->name }}
                        </span>

                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">
                            {{ auth()->user()->role->label() }}
                        </span>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="text-red-600 hover:underline">
                                Sair
                            </button>
                        </form>

                    </div>
                @endauth

            </div>

            @auth
                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm border-t border-gray-100 pt-3">

                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-md transition-colors
                       {{ request()->routeIs('dashboard')
                           ? 'bg-indigo-100 text-indigo-700 font-semibold'
                           : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('eventos.index') }}"
                       class="px-3 py-2 rounded-md transition-colors
                       {{ request()->routeIs('eventos.*')
                           ? 'bg-indigo-100 text-indigo-700 font-semibold'
                           : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                        Eventos
                    </a>

                    @if(auth()->user()->isAdminGeral())
                        <a href="{{ route('users.index') }}"
                           class="px-3 py-2 rounded-md transition-colors
                           {{ request()->routeIs('users.*')
                               ? 'bg-indigo-100 text-indigo-700 font-semibold'
                               : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                            Usuários
                        </a>

                        <a href="{{ route('users.create') }}"
                           class="px-3 py-2 rounded-md transition-colors
                           {{ request()->routeIs('users.create')
                               ? 'bg-indigo-100 text-indigo-700 font-semibold'
                               : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                            Novo Usuário
                        </a>
                    @endif

                </div>
            @endauth

        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
