<?php

namespace App\Support\Diagnostico;

class SessaoDiagnostico
{
    /**
     * Analisa o contexto de sessão/CSRF e retorna as falhas que causam HTTP 419.
     *
     * @param array{
     *   app_url:string, app_key:?string, request_scheme:string, request_host:string,
     *   session_driver:string, session_domain:?string, session_secure:bool,
     *   session_same_site:string, session_path:string, session_lifetime:int,
     *   sessions_dir_exists:bool, sessions_dir_writable:bool,
     *   trust_proxies:bool, config_cached:bool
     * } $ctx
     * @return array<int, array{nivel:string, codigo:string, mensagem:string, correcao:string}>
     */
    public function analisar(array $ctx): array
    {
        $falhas = [];

        $add = function (string $nivel, string $codigo, string $mensagem, string $correcao) use (&$falhas) {
            $falhas[] = compact('nivel', 'codigo', 'mensagem', 'correcao');
        };

        if (empty($ctx['app_key'])) {
            $add('erro', 'APP_KEY_AUSENTE',
                'APP_KEY vazia: o cookie de sessão criptografado não pode ser decifrado.',
                'php artisan key:generate --force');
        }

        if ($ctx['session_driver'] === 'file') {
            if (! $ctx['sessions_dir_exists']) {
                $add('erro', 'SESSIONS_DIR_AUSENTE',
                    'O diretório storage/framework/sessions não existe (comum em deploy por FTP/Git).',
                    'mkdir -p storage/framework/{sessions,views,cache}');
            } elseif (! $ctx['sessions_dir_writable']) {
                $add('erro', 'SESSIONS_DIR_NAO_GRAVAVEL',
                    'O diretório de sessões não tem permissão de escrita: a sessão não persiste.',
                    'chmod 755 storage/framework/sessions (ou 775 conforme o PHP da hospedagem)');
            }
        }

        if ($ctx['session_secure'] && $ctx['request_scheme'] === 'http') {
            $add('erro', 'SECURE_COOKIE_EM_HTTP',
                'SESSION_SECURE_COOKIE=true, mas o acesso é HTTP: o navegador não envia o cookie.',
                'SESSION_SECURE_COOKIE=false (ou passe a acessar por HTTPS)');
        }

        if ($ctx['session_same_site'] === 'none' && ! $ctx['session_secure']) {
            $add('erro', 'SAMESITE_NONE_SEM_SECURE',
                'SameSite=None exige Secure; o navegador rejeita o cookie de sessão.',
                'SESSION_SAME_SITE=lax');
        }

        $domain = $this->normalizarHost((string) ($ctx['session_domain'] ?? ''));
        $host   = $this->normalizarHost($ctx['request_host']);

        if ($domain !== '' && $domain !== $host) {
            $add('erro', 'SESSION_DOMAIN_DIVERGENTE',
                "SESSION_DOMAIN ({$ctx['session_domain']}) não casa com o host acessado ({$ctx['request_host']}).",
                'SESSION_DOMAIN=null (deixe vazio para usar o host atual)');
        }

        if ($ctx['session_path'] !== '/') {
            $add('aviso', 'SESSION_PATH_RESTRITO',
                "SESSION_PATH={$ctx['session_path']} pode impedir o envio do cookie em outras rotas.",
                'SESSION_PATH=/');
        }

        if ($ctx['session_lifetime'] <= 0) {
            $add('erro', 'SESSION_LIFETIME_INVALIDO',
                'SESSION_LIFETIME <= 0 expira a sessão imediatamente.',
                'SESSION_LIFETIME=120');
        }

        $urlHost   = $this->normalizarHost((string) parse_url($ctx['app_url'], PHP_URL_HOST));
        $urlScheme = (string) parse_url($ctx['app_url'], PHP_URL_SCHEME);

        if ($urlHost !== '' && $urlHost !== $host) {
            $add('aviso', 'APP_URL_HOST_DIVERGENTE',
                "APP_URL ({$ctx['app_url']}) aponta para outro host que o acessado ({$ctx['request_host']}).",
                'Ajuste APP_URL para o domínio real, incluindo www se usado');
        }

        if ($urlScheme === 'https' && $ctx['request_scheme'] === 'http' && ! $ctx['trust_proxies']) {
            $add('erro', 'PROXY_NAO_CONFIADO',
                'HTTPS terminado no proxy, mas o Laravel enxerga HTTP: cookies/URLs saem errados.',
                'Configure trustProxies(at: \'*\') em bootstrap/app.php');
        }

        if ($ctx['config_cached'] && str_contains($ctx['app_url'], 'localhost')) {
            $add('erro', 'CONFIG_CACHE_LOCAL',
                'Config em cache apontando para localhost (cache gerado na máquina local).',
                'php artisan config:clear && php artisan config:cache (na hospedagem)');
        }

        return $falhas;
    }

    private function normalizarHost(string $host): string
    {
        return strtolower(preg_replace('/^www\./i', '', trim($host)) ?? '');
    }
}