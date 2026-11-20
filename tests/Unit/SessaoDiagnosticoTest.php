<?php

use App\Support\Diagnostico\SessaoDiagnostico;

function contextoSaudavel(array $override = []): array
{
    return array_merge([
        'app_url'               => 'https://adtc2.com.br',
        'app_key'               => 'base64:abc123',
        'request_scheme'        => 'https',
        'request_host'          => 'adtc2.com.br',
        'session_driver'        => 'file',
        'session_domain'        => null,
        'session_secure'        => true,
        'session_same_site'     => 'lax',
        'session_path'          => '/',
        'session_lifetime'      => 120,
        'sessions_dir_exists'   => true,
        'sessions_dir_writable' => true,
        'trust_proxies'         => true,
        'config_cached'         => false,
    ], $override);
}

function codigos(array $falhas): array
{
    return array_column($falhas, 'codigo');
}

it('não aponta falhas em um ambiente corretamente configurado', function () {
    expect((new SessaoDiagnostico)->analisar(contextoSaudavel()))->toBe([]);
});

it('detecta secure cookie acessado via http (causa clássica de 419)', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'app_url'        => 'http://adtc2.com.br',
        'request_scheme' => 'http',
        'session_secure' => true,
        'trust_proxies'  => false,
    ]));

    expect(codigos($falhas))->toContain('SECURE_COOKIE_EM_HTTP');
});

it('detecta diretório de sessões ausente', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'sessions_dir_exists' => false,
    ]));

    expect(codigos($falhas))->toContain('SESSIONS_DIR_AUSENTE');
});

it('detecta diretório de sessões sem escrita', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'sessions_dir_writable' => false,
    ]));

    expect(codigos($falhas))->toContain('SESSIONS_DIR_NAO_GRAVAVEL');
});

it('detecta session_domain divergente do host acessado', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'session_domain' => 'localhost',
    ]));

    expect(codigos($falhas))->toContain('SESSION_DOMAIN_DIVERGENTE');
});

it('ignora diferença apenas de www no session_domain', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'session_domain' => 'www.adtc2.com.br',
    ]));

    expect(codigos($falhas))->not->toContain('SESSION_DOMAIN_DIVERGENTE');
});

it('detecta sameSite none sem secure', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'session_same_site' => 'none',
        'session_secure'    => false,
        'app_url'           => 'http://adtc2.com.br',
        'request_scheme'    => 'http',
    ]));

    expect(codigos($falhas))->toContain('SAMESITE_NONE_SEM_SECURE');
});

it('detecta proxy reverso não confiado', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'request_scheme' => 'http',
        'trust_proxies'  => false,
        'session_secure' => false,
    ]));

    expect(codigos($falhas))->toContain('PROXY_NAO_CONFIADO');
});

it('detecta app_key ausente', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel(['app_key' => null]));

    expect(codigos($falhas))->toContain('APP_KEY_AUSENTE');
});

it('detecta config cache apontando para localhost', function () {
    $falhas = (new SessaoDiagnostico)->analisar(contextoSaudavel([
        'app_url'       => 'http://localhost:8000',
        'config_cached' => true,
    ]));

    expect(codigos($falhas))->toContain('CONFIG_CACHE_LOCAL');
});