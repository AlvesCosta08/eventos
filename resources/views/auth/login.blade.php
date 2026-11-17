<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login — ADTC2 Eventos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded shadow p-8 w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center text-indigo-700">📅 ADTC2</h1>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border-gray-300 rounded px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" name="password" required
                       class="w-full border-gray-300 rounded px-3 py-2 border">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember"> Lembrar-me
            </label>
            <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                Entrar
            </button>
        </form>

        <div class="mt-4 text-xs text-gray-500 text-center space-y-1">
            <p>Demo:</p>
            <p>admin@adtc2.local / password</p>
            <p>lider.sede@adtc2.local / password</p>
            <p>viewer@adtc2.local / password</p>
        </div>
    </div>
</body>
</html>