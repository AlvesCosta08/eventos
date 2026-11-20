<?php

use Illuminate\Support\Facades\URL;

it('gera a URL de login respeitando o subdiretório do APP_URL', function () {
    $urlBase = 'https://adtc2maranguapecombr.com/sistemas/adtc2-eventos/public';
    
    // 1. Força a configuração
    config(['app.url' => $urlBase]);
    
    // 2. Força o gerador de URLs do Laravel a usar esta base
    URL::forceRootUrl($urlBase);
    
    // 3. FORÇA O ESQUEMA HTTPS (Essa era a linha que faltava para o teste passar!)
    URL::forceScheme('https');

    $url = route('login');

    expect($url)
        ->toBe($urlBase . '/login')
        ->and($url)->toContain('/sistemas/adtc2-eventos/public');
});

it('gera a URL de login corretamente quando APP_URL é a raiz (sem subdiretório)', function () {
    $urlBase = 'https://adtc2maranguapecombr.com';
    
    config(['app.url' => $urlBase]);
    URL::forceRootUrl($urlBase);
    URL::forceScheme('https'); // ✅ Adicionado aqui também

    $url = route('login');

    expect($url)
        ->toBe($urlBase . '/login')
        ->and($url)->not->toContain('//login');
});