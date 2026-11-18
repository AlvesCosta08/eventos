
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel') | ADTC2 Eventos</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="adtc-body min-h-screen">

@auth
<div class="adtc-shell" data-app-shell>

    {{-- Sobreposição do menu no celular --}}
    <button
        type="button"
        class="adtc-sidebar-overlay"
        data-sidebar-close
        aria-label="Fechar menu"
        tabindex="-1"
    ></button>

    {{-- Menu lateral --}}
    <aside class="adtc-sidebar" id="adtc-sidebar" data-sidebar>

        <div class="adtc-brand">
            <a href="{{ route('dashboard') }}" class="adtc-brand-link">
                <span class="adtc-brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="3"/>
                        <path d="M7 3v4M17 3v4M3 10h18"/>
                        <path d="m9 16 2 2 4-4"/>
                    </svg>
                </span>

                <span class="adtc-brand-text">
                    <strong>ADTC2</strong>
                    <small>Gestão de Eventos</small>
                </span>
            </a>

            <button
                type="button"
                class="adtc-sidebar-close"
                data-sidebar-close
                aria-label="Fechar menu"
            >
                &times;
            </button>
        </div>

        <div class="adtc-sidebar-content">

            <p class="adtc-nav-heading">MENU PRINCIPAL</p>

            <nav class="adtc-nav" aria-label="Navegação principal">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="adtc-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                   @if(request()->routeIs('dashboard')) aria-current="page" @endif>

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span>Dashboard</span>
                </a>

                {{-- Eventos --}}
                <a href="{{ route('eventos.index') }}"
                   class="adtc-nav-link {{ request()->routeIs('eventos.*') ? 'is-active' : '' }}"
                   @if(request()->routeIs('eventos.*')) aria-current="page" @endif>

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M7 3v4M17 3v4M3 10h18"/>
                    </svg>

                    <span>Eventos</span>
                </a>

                {{-- RN-001: Controle da Agenda Anual --}}
                @if(auth()->user()->isAdminGeral())

                    <a href="{{ route('controle-agenda.index') }}"
                       class="adtc-nav-link {{ request()->routeIs('controle-agenda.*') ? 'is-active' : '' }}"
                       @if(request()->routeIs('controle-agenda.*')) aria-current="page" @endif>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M7 3v4M17 3v4M3 10h18"/>
                            <path d="M8 15h8M12 11v8"/>
                        </svg>

                        <span>Controle da Agenda</span>
                    </a>

                    {{-- Usuários --}}
                    <a href="{{ route('users.index') }}"
                       class="adtc-nav-link {{ request()->routeIs('users.*') ? 'is-active' : '' }}"
                       @if(request()->routeIs('users.*')) aria-current="page" @endif>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>

                        <span>Usuários</span>
                    </a>

                @endif

            </nav>

            {{-- Acesso rápido --}}
            <p class="adtc-nav-heading adtc-nav-heading-secondary">
                ACESSO RÁPIDO
            </p>

            <nav class="adtc-nav" aria-label="Acesso rápido">

                @if(auth()->user()->podeGerenciarEventos())

                    <a href="{{ route('eventos.create') }}" class="adtc-nav-link">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>

                        <span>Novo evento</span>
                    </a>

                @endif

                @if(auth()->user()->isAdminGeral())

                    <a href="{{ route('users.create') }}" class="adtc-nav-link">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M19 8v6M16 11h6"/>
                        </svg>

                        <span>Novo usuário</span>
                    </a>

                @endif

            </nav>

        </div>

        {{-- Identificação do usuário --}}
        <div class="adtc-sidebar-footer">

            <div class="adtc-sidebar-user">

                <span class="adtc-avatar">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </span>

                <div class="adtc-user-details">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->role->label() }}</small>
                </div>

            </div>

        </div>

    </aside>

    {{-- Conteúdo principal --}}
    <div class="adtc-main-area">

        <header class="adtc-topbar">

            <div class="adtc-topbar-left">

                <button
                    type="button"
                    class="adtc-menu-toggle"
                    data-sidebar-toggle
                    aria-label="Abrir menu"
                    aria-controls="adtc-sidebar"
                    aria-expanded="false"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div class="adtc-page-identity">
                    <span class="adtc-topbar-eyebrow">ADTC2 EVENTOS</span>
                    <strong>@yield('page_title', 'Painel de controle')</strong>
                </div>

            </div>

            <div class="adtc-topbar-right">

                <span class="adtc-topbar-role">
                    {{ auth()->user()->role->label() }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="adtc-logout-button">
                        Sair
                    </button>
                </form>

            </div>

        </header>

        <main class="adtc-content" id="conteudo-principal">

            @if(session('success'))
                <div class="adtc-alert adtc-alert-success" role="status">
                    <span aria-hidden="true">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="adtc-alert" role="alert">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @yield('content')

        </main>

        <footer class="adtc-footer">
            <span>ADTC2 Eventos</span>
            <span>Gestão organizada, eventos conectados.</span>
        </footer>

    </div>

</div>

@else

    <main class="min-h-screen">
        @yield('content')
    </main>

@endauth

</body>
</html>
