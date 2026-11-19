<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Verificar se a aplicação está em modo de manutenção.
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Carregar o autoload do Composer.
require __DIR__.'/vendor/autoload.php';

// Inicializar a aplicação Laravel.
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

// Processar a requisição HTTP.
$app->handleRequest(Request::capture());