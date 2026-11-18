
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Entrar — ADTC2 Eventos</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 antialiased">

    <main class="flex min-h-screen">

        {{-- Painel de identidade visual --}}
        <section class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-slate-900 p-10 text-white lg:flex xl:p-16">

            {{-- Elementos decorativos --}}
            <div aria-hidden="true"
                 class="pointer-events-none absolute -right-28 -top-28 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl">
            </div>

            <div aria-hidden="true"
                 class="pointer-events-none absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl">
            </div>

            {{-- Marca --}}
            <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 shadow-lg shadow-blue-950/30">
                    <svg class="h-7 w-7"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 10h18"/>
                        <path d="m9 16 2 2 4-4"/>
                    </svg>
                </div>

                <div>
                    <p class="text-2xl font-extrabold tracking-tight">
                        ADTC2
                    </p>

                    <p class="text-sm font-medium text-slate-400">
                        Gestão de Eventos
                    </p>
                </div>
            </div>

            {{-- Apresentação --}}
            <div class="relative z-10 max-w-xl">
                <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-500/10 px-4 py-2 text-xs font-semibold text-blue-200">
                    <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                    Sistema de gestão
                </span>

                <h1 class="text-4xl font-extrabold leading-tight tracking-tight xl:text-5xl">
                    Organização e controle
                    <span class="text-blue-400">
                        em um só lugar.
                    </span>
                </h1>

                <p class="mt-6 max-w-lg text-base leading-8 text-slate-300">
                    Acompanhe eventos, consulte informações e organize
                    as atividades das congregações com praticidade.
                </p>

                <div class="mt-10 grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300">
                            <svg class="h-5 w-5"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                <path d="M16 3v4M8 3v4M3 10h18"/>
                            </svg>
                        </div>

                        <p class="text-sm font-bold text-white">
                            Eventos
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-400">
                            Planejamento e acompanhamento.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300">
                            <svg class="h-5 w-5"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                                <path d="M19 8v6M16 11h6"/>
                            </svg>
                        </div>

                        <p class="text-sm font-bold text-white">
                            Usuários
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-400">
                            Acesso conforme o perfil.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Rodapé --}}
            <div class="relative z-10 border-t border-white/10 pt-6">
                <p class="text-xs text-slate-400">
                    ADTC2 Eventos — Sistema de Gestão
                </p>
            </div>
        </section>

        {{-- Painel de autenticação --}}
        <section class="flex min-h-screen w-full items-center justify-center px-5 py-10 sm:px-8 lg:w-1/2 lg:px-12">

            <div class="w-full max-w-md">

                {{-- Marca para dispositivos móveis --}}
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200">
                        <svg class="h-6 w-6"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M16 3v4M8 3v4M3 10h18"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-xl font-extrabold text-slate-900">
                            ADTC2
                        </p>

                        <p class="text-xs font-medium text-slate-500">
                            Gestão de Eventos
                        </p>
                    </div>
                </div>

                {{-- Título --}}
                <div class="mb-8">
                    <span class="mb-3 inline-block text-xs font-bold uppercase tracking-widest text-blue-600">
                        Acesso ao sistema
                    </span>

                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
                        Bem-vindo!
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Informe suas credenciais para acessar o painel
                        de gerenciamento de eventos.
                    </p>
                </div>

                {{-- Erros de autenticação --}}
                @if($errors->any())
                    <div role="alert"
                         class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 8v4M12 16h.01"/>
                            </svg>

                            <div>
                                <p class="text-sm font-bold text-red-800">
                                    Não foi possível entrar
                                </p>

                                <ul class="mt-1 list-inside list-disc space-y-1 text-xs text-red-700">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Formulário --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="space-y-5"
                >
                    @csrf

                    {{-- E-mail --}}
                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            E-mail
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Digite seu e-mail"
                            autocomplete="username"
                            maxlength="150"
                            required
                            autofocus
                            @error('email')
                                aria-invalid="true"
                                aria-describedby="email-error"
                            @enderror
                            class="w-full rounded-xl border bg-white px-4 py-3.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2
                                @error('email')
                                    border-red-300 focus:border-red-500 focus:ring-red-100
                                @else
                                    border-slate-200 focus:border-blue-500 focus:ring-blue-100
                                @enderror"
                        >

                        @error('email')
                            <p id="email-error" class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Senha --}}
                    <div>
                        <label for="password"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Senha
                        </label>

                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Digite sua senha"
                                autocomplete="current-password"
                                required
                                @error('password')
                                    aria-invalid="true"
                                    aria-describedby="password-error"
                                @enderror
                                class="w-full rounded-xl border bg-white px-4 py-3.5 pr-14 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2
                                    @error('password')
                                        border-red-300 focus:border-red-500 focus:ring-red-100
                                    @else
                                        border-slate-200 focus:border-blue-500 focus:ring-blue-100
                                    @enderror"
                            >

                            <button
                                type="button"
                                id="toggle-password"
                                aria-label="Mostrar senha"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 flex items-center justify-center rounded-r-xl px-4 text-slate-400 transition hover:text-blue-600"
                            >
                                <svg class="h-5 w-5"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <p id="password-error" class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Lembrar-me --}}
                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        >

                        <span class="text-sm font-medium text-slate-600">
                            Lembrar-me neste dispositivo
                        </span>
                    </label>

                    {{-- Entrar --}}
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Entrar no sistema

                        <svg class="h-4 w-4"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </button>

                </form>

                {{-- Rodapé --}}
                <div class="mt-10 border-t border-slate-200 pt-6 text-center">
                    <p class="text-xs text-slate-400">
                        ADTC2 Eventos — Acesso restrito a usuários autorizados.
                    </p>
                </div>

            </div>
        </section>

    </main>

    {{-- Mostrar ou ocultar senha --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const senha = document.getElementById('password');
            const botao = document.getElementById('toggle-password');

            if (!senha || !botao) {
                return;
            }

            botao.addEventListener('click', () => {
                const mostrar = senha.type === 'password';

                senha.type = mostrar ? 'text' : 'password';
                botao.setAttribute('aria-pressed', String(mostrar));
                botao.setAttribute(
                    'aria-label',
                    mostrar ? 'Ocultar senha' : 'Mostrar senha'
                );
            });
        });
    </script>

</body>
</html>
