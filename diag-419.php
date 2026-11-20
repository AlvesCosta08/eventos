<?php
/**
 * ⚠️ DIAGNÓSTICO PURO (sem Laravel) — APAGUE APÓS O USO
 */
$SENHA = 'Alves2026@Diag';
if (($_GET['key'] ?? '') !== $SENHA) { http_response_code(403); exit('Acesso negado'); }

// Caminho absoluto REAL do projeto (descoberto pelo JSON anterior)
$projeto = $_GET['projeto'] ?? '/home4/adtc2m99/public_html/sistemas/adtc2-eventos';

header('Content-Type: application/json; charset=utf-8');

function lerEnv($path) {
    if (!file_exists($path)) return null;
    $env = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        $l = trim($l);
        if ($l === '' || $l[0] === '#') continue;
        if (strpos($l, '=') !== false) {
            [$k, $v] = explode('=', $l, 2);
            $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
        }
    }
    return $env;
}
function infoDir($path) {
    return [
        'existe' => is_dir($path),
        'gravavel' => is_dir($path) && is_writable($path),
        'perms' => is_dir($path) ? substr(sprintf('%o', fileperms($path)), -4) : null,
    ];
}

$env = lerEnv($projeto . '/.env');
$sessions = $projeto . '/storage/framework/sessions';

$escritaOk = null;
if (is_dir($sessions) && is_writable($sessions)) {
    $tmp = $sessions . '/t_' . uniqid();
    $escritaOk = @file_put_contents($tmp, 'ok') !== false;
    if ($escritaOk) @unlink($tmp);
}

echo json_encode([
    'projeto_path'   => $projeto,
    'projeto_encontrado' => is_dir($projeto),
    'acesso' => [
        'scheme' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http',
        'host'   => $_SERVER['HTTP_HOST'] ?? '?',
        'x_forwarded_proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'ausente',
        'x_forwarded_host'  => $_SERVER['HTTP_X_FORWARDED_HOST']  ?? 'ausente',
    ],
    'env' => $env ? [
        'APP_ENV' => $env['APP_ENV'] ?? '❌',
        'APP_DEBUG' => $env['APP_DEBUG'] ?? '❌',
        'APP_URL' => $env['APP_URL'] ?? '❌',
        'SESSION_DRIVER' => $env['SESSION_DRIVER'] ?? 'file',
        'SESSION_DOMAIN' => $env['SESSION_DOMAIN'] ?? '(vazio)',
        'SESSION_SECURE_COOKIE' => $env['SESSION_SECURE_COOKIE'] ?? '(vazio)',
        'SESSION_SAME_SITE' => $env['SESSION_SAME_SITE'] ?? 'lax',
        'SESSION_PATH' => $env['SESSION_PATH'] ?? '/',
        'APP_KEY_presente' => !empty($env['APP_KEY']),
    ] : '❌ .env não lido em ' . $projeto . '/.env',
    'storage' => [
        'framework/sessions' => infoDir($sessions),
        'teste_escrita' => $escritaOk,
    ],
    'bootstrap_cache' => [
        'config.php' => file_exists($projeto . '/bootstrap/cache/config.php'),
        'services.php' => file_exists($projeto . '/bootstrap/cache/services.php'),
    ],
    'htaccess' => [
        'public/.htaccess' => file_exists($projeto . '/public/.htaccess')
            ? file_get_contents($projeto . '/public/.htaccess') : 'ausente',
        'raiz/.htaccess' => file_exists($projeto . '/.htaccess')
            ? file_get_contents($projeto . '/.htaccess') : 'ausente',
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);