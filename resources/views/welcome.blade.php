<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1730">
    <title>Calendário de Eventos | ADTC2</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{color-scheme:light;--ink:#13233c;--muted:#64748b;--line:#e2e8f0;--blue:#2563eb;--navy:#0b1730;--surface:#fff}
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body.adtc-public{margin:0;min-height:100vh;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;background:#f5f8fc;color:var(--ink)}
        button,a{-webkit-tap-highlight-color:transparent}
        button{font:inherit}
        a{text-decoration:none}
        :focus-visible{outline:3px solid #60a5fa;outline-offset:3px}
        .container{width:min(1180px,calc(100% - 36px));margin-inline:auto}
        .site-header{background:#fff;border-bottom:1px solid var(--line);position:relative;z-index:5}
        .header-inner{display:flex;justify-content:space-between;align-items:center;gap:16px;min-height:80px}
        .brand{display:flex;align-items:center;gap:12px;color:var(--ink)}
        .brand-mark{display:grid;place-items:center;width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#2563eb,#0f3c9d);color:white;font-size:20px;font-weight:850;box-shadow:0 9px 22px #2563eb38}
        .brand-name{display:block;font-weight:850;font-size:19px;letter-spacing:-.035em}
        .brand-caption{display:block;color:var(--muted);font-size:12px;margin-top:2px}
        .login-link,.nav-button,.close-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;cursor:pointer;transition:transform .2s ease,background .2s ease,box-shadow .2s ease}
        .login-link{background:#1d4ed8;color:#fff;border-radius:12px;padding:12px 18px;font-size:13px;font-weight:750;box-shadow:0 8px 18px #1d4ed82c}
        .login-link:hover{background:#1e40af;transform:translateY(-2px);box-shadow:0 12px 25px #1d4ed83c}
        .hero{background:radial-gradient(circle at 85% 10%,#1d4d9a 0,transparent 40%),linear-gradient(120deg,#0b1730,#112b54);color:#fff;position:relative;overflow:hidden}
        .hero:after{content:"";position:absolute;width:360px;height:360px;right:-140px;bottom:-230px;border:60px solid #ffffff09;border-radius:50%;pointer-events:none}
        .hero-inner{padding:56px 0 62px;position:relative;z-index:1}
        .eyebrow{display:inline-flex;align-items:center;gap:8px;border:1px solid #ffffff35;border-radius:999px;padding:8px 13px;color:#c9dcff;background:#ffffff0d;font-size:12px;font-weight:750;letter-spacing:.04em;text-transform:uppercase}
        .eyebrow-dot{height:7px;width:7px;background:#60a5fa;border-radius:50%;box-shadow:0 0 0 4px #60a5fa24}
        .hero h1{margin:20px 0 12px;font-size:clamp(31px,5vw,49px);line-height:1.12;letter-spacing:-.055em;font-weight:850;max-width:750px}
        .hero p{margin:0;color:#c7d5e9;line-height:1.7;max-width:620px;font-size:15px}
        .main{margin-top:-24px;padding-bottom:65px;position:relative;z-index:2}
        .calendar-panel{background:var(--surface);border:1px solid var(--line);border-radius:22px;box-shadow:0 18px 60px #13233c12;overflow:hidden}
        .calendar-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:24px 26px;border-bottom:1px solid var(--line)}
        .toolbar-caption{display:block;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.12em;margin-bottom:5px}
        .calendar-toolbar h2{margin:0;font-size:clamp(20px,3vw,26px);font-weight:850;letter-spacing:-.035em;text-transform:capitalize}
        .calendar-nav{display:flex;align-items:center;gap:8px}
        .nav-button{border:1px solid #dbe4f0;border-radius:11px;background:#fff;color:#334155;padding:10px 13px;font-size:13px;font-weight:750;white-space:nowrap}
        .nav-button:hover{background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8;transform:translateY(-2px)}
        .nav-button.today{background:#eff6ff;color:#1d4ed8;border-color:#dbeafe}
        .calendar-weekdays,.calendar-grid{display:grid;grid-template-columns:repeat(7,minmax(0,1fr))}
        .weekday{text-align:center;padding:14px 5px;background:#f8fafc;border-bottom:1px solid var(--line);font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:#64748b;font-weight:800}
        .calendar-day{min-height:132px;padding:10px;border-right:1px solid var(--line);border-bottom:1px solid var(--line);min-width:0;transition:background .2s ease}
        .calendar-day:nth-child(7n){border-right:0}
        .calendar-day:not(.outside):hover{background:#f8fbff}
        .calendar-day.outside{background:#f9fbfd}
        .day-number{display:inline-grid;place-items:center;width:31px;height:31px;border-radius:10px;font-size:12px;font-weight:800;color:#334155}
        .calendar-day.is-today .day-number{background:#2563eb;color:#fff;box-shadow:0 5px 14px #2563eb45}
        .day-events{display:grid;gap:6px;margin-top:7px}
        .event-trigger{width:100%;min-width:0;display:block;text-align:left;border:1px solid transparent;border-radius:9px;padding:8px;background:#eaf1ff;color:#1d4ed8;cursor:pointer;transition:transform .18s ease,box-shadow .18s ease,filter .18s ease;overflow-wrap:anywhere}
        .event-trigger:hover{transform:translateY(-2px);box-shadow:0 6px 14px #1d4ed821;filter:saturate(1.15)}
        .event-trigger strong{display:block;font-size:11px;line-height:1.35;font-weight:800}
        .event-trigger small{display:block;margin-top:4px;font-size:10px;line-height:1.35;opacity:.83}
        .event-trigger.realizado{background:#e5f9ee;color:#166534}
        .event-trigger.cancelado{background:#fff0f0;color:#b91c1c}
        .legend{display:flex;flex-wrap:wrap;gap:15px;padding:17px 24px;color:#64748b;font-size:12px;font-weight:650}
        .legend-item{display:inline-flex;align-items:center;gap:7px}
        .legend-dot{height:9px;width:9px;border-radius:50%;background:#2563eb}
        .legend-dot.done{background:#16a34a}.legend-dot.cancel{background:#dc2626}
        .section-header{display:flex;justify-content:space-between;align-items:end;gap:12px;margin:34px 0 16px}
        .section-header h2{font-size:24px;letter-spacing:-.04em;font-weight:850;margin:0}
        .section-header p{margin:6px 0 0;font-size:13px;color:var(--muted)}
        .event-count{background:#eaf1ff;color:#1d4ed8;border-radius:999px;padding:7px 12px;font-size:12px;font-weight:800;white-space:nowrap}
        .event-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
        .event-card{width:100%;display:flex;align-items:center;gap:16px;text-align:left;background:#fff;border:1px solid var(--line);border-radius:16px;padding:17px;cursor:pointer;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
        .event-card:hover{transform:translateY(-3px);box-shadow:0 12px 30px #13233c12;border-color:#bfdbfe}
        .date-chip{display:grid;place-items:center;flex:0 0 63px;min-height:65px;background:#eff6ff;border-radius:12px;color:#1d4ed8}
        .date-chip strong{font-size:24px;line-height:1;font-weight:850}
        .date-chip span{font-size:11px;text-transform:uppercase;font-weight:800;margin-top:3px}
        .card-info{min-width:0;flex:1}
        .card-title{display:block;font-size:15px;font-weight:800;color:#1e293b;line-height:1.4;overflow-wrap:anywhere}
        .card-place{display:block;color:#64748b;font-size:12px;margin-top:5px;overflow-wrap:anywhere}
        .status{display:inline-flex;align-items:center;border-radius:999px;padding:5px 9px;margin-top:9px;background:#dbeafe;color:#1d4ed8;font-size:11px;font-weight:800}
        .status.realizado{background:#dcfce7;color:#166534}.status.cancelado{background:#fee2e2;color:#b91c1c}
        .empty{border:1px dashed #cbd5e1;background:#fff;border-radius:16px;padding:42px 20px;text-align:center;color:#64748b}
        .empty strong{display:block;color:#334155;font-size:16px;margin-bottom:7px}
        .footer{border-top:1px solid var(--line);background:#fff;color:#64748b;text-align:center;padding:25px 16px;font-size:12px}
        .event-modal{width:min(490px,calc(100% - 30px));max-width:none;max-height:min(90vh,750px);overflow-y:auto;margin:auto;border:0;border-radius:22px;padding:0;background:#fff;color:#17243a;box-shadow:0 30px 90px #0004}
        .event-modal::backdrop{background:#071426ae;backdrop-filter:blur(5px)}
        .event-modal[open]{animation:modal-in .25s ease-out}
        .modal-head{padding:22px 24px;background:linear-gradient(120deg,#0b1730,#1b3b73);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:15px}
        .modal-head strong{font-size:15px}
        .close-button{width:34px;height:34px;border:1px solid #ffffff4d;border-radius:10px;background:#ffffff15;color:#fff;font-size:24px;line-height:1}
        .close-button:hover{background:#ffffff30}
        .modal-body{padding:26px}
        .modal-body h2{font-size:clamp(22px,4vw,29px);line-height:1.25;letter-spacing:-.045em;margin:0 0 18px;overflow-wrap:anywhere}
        .detail-row{padding:14px 0;border-bottom:1px solid #e2e8f0;display:grid;grid-template-columns:110px minmax(0,1fr);gap:10px;font-size:14px;line-height:1.5}
        .detail-row dt{font-weight:800;color:#64748b}.detail-row dd{margin:0;overflow-wrap:anywhere}
        .modal-action{display:block;width:100%;margin-top:24px;background:#2563eb;color:white;border:0;border-radius:12px;padding:13px;font-weight:800;cursor:pointer}
        .modal-action:hover{background:#1d4ed8}
        @keyframes modal-in{from{opacity:0;transform:translateY(12px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
        @keyframes fade-up{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .animate-in{animation:fade-up .55s ease both}
        @media(max-width:800px){.event-list{grid-template-columns:1fr}.calendar-day{min-height:112px;padding:7px}.event-trigger{padding:6px}.event-trigger strong{font-size:10px}.event-trigger small{font-size:9px}}
        @media(max-width:600px){.container{width:min(100% - 26px,1180px)}.header-inner{min-height:72px}.brand-mark{width:40px;height:40px}.brand-name{font-size:16px}.brand-caption{font-size:10px}.login-link{padding:10px 12px;font-size:11px}.hero-inner{padding:43px 0 54px}.hero p{font-size:13px}.main{margin-top:-18px}.calendar-panel{border-radius:17px}.calendar-toolbar{flex-direction:column;align-items:stretch;padding:18px 14px}.calendar-nav{justify-content:space-between}.nav-button{padding:10px 11px;font-size:12px}.weekday{font-size:10px;padding:11px 2px}.calendar-grid{display:block}.calendar-day{display:none;border-right:0;min-height:0;padding:13px 14px}.calendar-day.outside{display:none}.calendar-day.has-events{display:flex;gap:12px;border-bottom:1px solid var(--line)}.calendar-day.has-events .day-number{flex:0 0 34px;background:#f1f5f9}.calendar-day.has-events.is-today .day-number{background:#2563eb}.calendar-day.has-events .day-events{flex:1;min-width:0;margin:0}.event-trigger{padding:11px}.event-trigger strong{font-size:13px}.event-trigger small{font-size:11px}.calendar-weekdays{display:none}.calendar-grid:empty{min-height:40px}.legend{padding:14px;gap:12px}.section-header{align-items:start}.section-header h2{font-size:21px}.event-card{padding:14px}.detail-row{grid-template-columns:1fr;gap:3px}.modal-body{padding:21px}}
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;animation:none!important;transition:none!important}}
    </style>
</head>
<body class="adtc-public">
@php
    $inicio = $mes->startOfMonth();
    $diasNoMes = $mes->daysInMonth;
    $espacosIniciais = $inicio->dayOfWeek;
    $totalCelulas = (int) (ceil(($espacosIniciais + $diasNoMes) / 7) * 7);
    $hoje = now()->toDateString();
    $nomeMes = \Illuminate\Support\Str::ucfirst($mes->locale('pt_BR')->translatedFormat('F Y'));
@endphp
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('calendario.publico') }}" aria-label="Página inicial AGENDA ANUAL ADTC2 | CAMPO">
            <span class="brand-mark" aria-hidden="true">A</span>
            <span><span class="brand-name">AGENDA ANUAL ADTC2 | CAMPO</span><span class="brand-caption">Agenda das congregações</span></span>
        </a>
        @auth
            <a class="login-link" href="{{ route('dashboard') }}">Acessar painel <span aria-hidden="true">↗</span></a>
        @else
            <a class="login-link" href="{{ route('login') }}">Entrar no sistema <span aria-hidden="true">↗</span></a>
        @endauth
    </div>
</header>
<section class="hero">
    <div class="container hero-inner animate-in">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Agenda pública</span>
        <h1>Momentos que nos<br>aproximam.</h1>
        <p>Confira a programação das congregações, encontre os próximos eventos e acompanhe cada data em um só lugar. Acesso livre, sem cadastro.</p>
    </div>
</section>
<main class="container main">
    <section class="calendar-panel animate-in" aria-label="Calendário mensal de eventos">
        <div class="calendar-toolbar">
            <div><span class="toolbar-caption">Calendário de eventos</span><h2>{{ $nomeMes }}</h2></div>
            <nav class="calendar-nav" aria-label="Navegação entre meses">
                <a class="nav-button" href="{{ route('calendario.publico', ['mes' => $mesAnterior]) }}" aria-label="Mês anterior">← Anterior</a>
                <a class="nav-button today" href="{{ route('calendario.publico') }}">Hoje</a>
                <a class="nav-button" href="{{ route('calendario.publico', ['mes' => $mesSeguinte]) }}" aria-label="Próximo mês">Próximo →</a>
            </nav>
        </div>
        <div class="calendar-weekdays" aria-hidden="true">
            @foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $diaSemana)
                <div class="weekday">{{ $diaSemana }}</div>
            @endforeach
        </div>
        <div class="calendar-grid">
            @for ($celula = 0; $celula < $totalCelulas; $celula++)
                @php
                    $numeroDia = $celula - $espacosIniciais + 1;
                    $dentroDoMes = $numeroDia >= 1 && $numeroDia <= $diasNoMes;
                    $data = $dentroDoMes ? $inicio->addDays($numeroDia - 1)->format('Y-m-d') : null;
                    $eventosDoDia = $data ? $eventosPorDia->get($data, collect()) : collect();
                @endphp
                <div class="calendar-day {{ !$dentroDoMes ? 'outside' : '' }} {{ $data === $hoje ? 'is-today' : '' }} {{ $eventosDoDia->isNotEmpty() ? 'has-events' : '' }}">
                    @if ($dentroDoMes)
                        <span class="day-number">{{ $numeroDia }}</span>
                        <div class="day-events">
                            @foreach ($eventosDoDia as $evento)
                                <button type="button" class="event-trigger {{ $evento->situacao->value }}" data-evento-detalhes
                                    data-titulo="{{ $evento->evento }}"
                                    data-data="{{ $evento->dt_evento->locale('pt_BR')->translatedFormat('d/m/Y') }}"
                                    data-congregacao="{{ $evento->congregacao?->nome ?? 'Não informada' }}"
                                    data-situacao="{{ $evento->situacao->label() }}"
                                    data-status="{{ $evento->situacao->value }}"
                                    aria-label="Ver detalhes do evento {{ $evento->evento }}">
                                    <strong>{{ $evento->evento }}</strong>
                                    <small>{{ $evento->congregacao?->nome ?? 'Congregação não informada' }}</small>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endfor
        </div>
        <div class="legend" aria-label="Legenda de situações">
            <span class="legend-item"><span class="legend-dot"></span> Agendado</span>
            <span class="legend-item"><span class="legend-dot done"></span> Realizado</span>
            <span class="legend-item"><span class="legend-dot cancel"></span> Cancelado</span>
        </div>
    </section>
    <section aria-labelledby="titulo-eventos">
        <div class="section-header"><div><h2 id="titulo-eventos">Eventos do mês</h2><p>Selecione um evento para consultar os detalhes.</p></div><span class="event-count">{{ $eventos->count() }} {{ $eventos->count() === 1 ? 'evento' : 'eventos' }}</span></div>
        @if ($eventos->isEmpty())
            <div class="empty"><strong>Nenhum evento neste mês</strong>Use as setas do calendário para consultar outras datas.</div>
        @else
            <div class="event-list">
                @foreach ($eventos as $evento)
                    <button type="button" class="event-card" data-evento-detalhes
                        data-titulo="{{ $evento->evento }}"
                        data-data="{{ $evento->dt_evento->locale('pt_BR')->translatedFormat('d/m/Y') }}"
                        data-congregacao="{{ $evento->congregacao?->nome ?? 'Não informada' }}"
                        data-situacao="{{ $evento->situacao->label() }}"
                        data-status="{{ $evento->situacao->value }}"
                        aria-label="Ver detalhes do evento {{ $evento->evento }}">
                        <span class="date-chip"><strong>{{ $evento->dt_evento->format('d') }}</strong><span>{{ $evento->dt_evento->locale('pt_BR')->translatedFormat('M') }}</span></span>
                        <span class="card-info"><span class="card-title">{{ $evento->evento }}</span><span class="card-place">{{ $evento->congregacao?->nome ?? 'Congregação não informada' }}</span><span class="status {{ $evento->situacao->value }}">{{ $evento->situacao->label() }}</span></span>
                        <span aria-hidden="true" style="color:#94a3b8;font-size:21px">›</span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>
</main>
<footer class="footer">AGENDA ANUAL ADTC2 | CAMPO © {{ date('Y') }} · Calendário público das congregações</footer>
<dialog id="modal-evento" class="event-modal" aria-labelledby="modal-evento-titulo">
    <div class="modal-head"><strong>Detalhes do evento</strong><button class="close-button" type="button" data-fechar-modal aria-label="Fechar janela">×</button></div>
    <div class="modal-body">
        <h2 id="modal-evento-titulo"></h2>
        <dl>
            <div class="detail-row"><dt>Data</dt><dd id="modal-evento-data"></dd></div>
            <div class="detail-row"><dt>Congregação</dt><dd id="modal-evento-congregacao"></dd></div>
            <div class="detail-row"><dt>Situação</dt><dd><span class="status" id="modal-evento-situacao"></span></dd></div>
        </dl>
        <button type="button" class="modal-action" data-fechar-modal>Fechar detalhes</button>
    </div>
</dialog>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modal-evento');
        if (!modal) return;
        document.querySelectorAll('[data-evento-detalhes]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('modal-evento-titulo').textContent = button.dataset.titulo || '';
                document.getElementById('modal-evento-data').textContent = button.dataset.data || '';
                document.getElementById('modal-evento-congregacao').textContent = button.dataset.congregacao || '';
                const status = document.getElementById('modal-evento-situacao');
                status.textContent = button.dataset.situacao || '';
                status.className = 'status ' + (['agendado','realizado','cancelado'].includes(button.dataset.status) ? button.dataset.status : '');
                modal.showModal();
            });
        });
        document.querySelectorAll('[data-fechar-modal]').forEach(button => button.addEventListener('click', () => modal.close()));
        modal.addEventListener('click', (event) => { if (event.target === modal) modal.close(); });
    });
</script>
</body>
</html>
