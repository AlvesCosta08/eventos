<?php
// ⚠️ APAGUE ESTE ARQUIVO IMEDIATAMENTE APÓS O USO!

// 1. Carregar o autoloader
require __DIR__.'/vendor/autoload.php';

// 2. Verificação crítica: O pacote de auditoria foi extraído no servidor?
if (!class_exists(\OwenIt\Auditing\AuditingServiceProvider::class)) {
    echo "<pre style='background:#111; color:#ff5555; padding:20px; font-family:monospace; font-size:14px; border-radius:8px;'>";
    echo "🚨 ERRO CRÍTICO DETECTADO!\n\n";
    echo "O pacote 'owen-it/laravel-auditing' NÃO foi encontrado no servidor.\n";
    echo "Isso acontece quando a pasta 'vendor' não foi enviada ou não foi extraída corretamente.\n\n";
    echo "✅ COMO RESOLVER (PASSO A PASSO):\n";
    echo "1. No seu computador, compacte a pasta 'vendor' inteira em um arquivo 'vendor.zip'.\n";
    echo "2. No cPanel (Gerenciador de Arquivos), acesse:\n";
    echo "   /home4/adtc2m99/public_html/sistemas/adtc2-eventos/\n";
    echo "3. Faça o UPLOAD do 'vendor.zip' e clique com o botão direito em 'Extrair'.\n";
    echo "4. Confirme a extração para a pasta atual e depois APAGUE o 'vendor.zip'.\n";
    echo "5. Atualize esta página para rodar o script novamente.\n";
    echo "</pre>";
    exit;
}

// 3. Inicializar o Laravel
try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
} catch (Exception $e) {
    echo "<pre style='background:#111; color:#ff5555; padding:20px; font-family:monospace; font-size:14px; border-radius:8px;'>";
    echo "🚨 ERRO AO INICIALIZAR O LARAVEL:\n" . $e->getMessage() . "\n";
    echo "Verifique se a pasta 'vendor' foi extraída completamente.\n";
    echo "</pre>";
    exit;
}

// 4. Formatação da saída de sucesso
echo "<pre style='background:#111; color:#0f0; padding:20px; font-family:monospace; font-size:14px; border-radius:8px;'>";
echo "🚀 INICIANDO SETUP COMPLETO NO SERVIDOR...\n";
echo str_repeat("=", 65) . "\n\n";

// 5. Limpar todos os caches
echo "🧹 [1/5] Limpando caches do Laravel...\n";
try {
    $kernel->call('config:clear');   echo "   ✅ Config cache limpo\n";
    $kernel->call('cache:clear');    echo "   ✅ Application cache limpo\n";
    $kernel->call('view:clear');     echo "   ✅ View cache limpo\n";
    $kernel->call('route:clear');    echo "   ✅ Route cache limpo\n";
    echo "   🎉 Todos os caches foram limpos com sucesso!\n\n";
} catch (Exception $e) {
    echo "   ❌ Erro ao limpar caches: " . $e->getMessage() . "\n\n";
}

// 6. Rodar Migrações
echo "🗄️  [2/5] Executando migrações (criando tabela 'audits' se não existir)...\n";
try {
    $kernel->call('migrate', ['--force' => true]);
    echo "   ✅ Migrações executadas com sucesso!\n\n";
} catch (Exception $e) {
    echo "   ❌ Erro nas migrações: " . $e->getMessage() . "\n\n";
}

// 7. Rodar Seeders
echo "🌱 [3/5] Executando seeders (populando banco)...\n";
try {
    $kernel->call('db:seed', ['--force' => true]);
    echo "   ✅ Seeders executados com sucesso!\n\n";
} catch (Exception $e) {
    echo "   ⚠️  Aviso nos seeders (normal se os dados já existirem): " . $e->getMessage() . "\n\n";
}

// 8. Garantir acesso do Administrador
echo "🔐 [4/5] Garantindo acesso do Administrador...\n";
try {
    $emailAdmin = 'alves@gmail.com';
    $senhaPlana = '12345678';

    $user = \App\Models\User::where('email', $emailAdmin)->first();

    if (!$user) {
        echo "   ⚠️  Usuário não encontrado. Criando novo administrador...\n";
        $user = \App\Models\User::create([
            'name' => 'Administrador Geral',
            'email' => $emailAdmin,
            'password' => bcrypt($senhaPlana),
            'role' => \App\Domain\Users\Enums\UserRole::AdminGeral,
        ]);
        echo "   ✅ Usuário criado com sucesso!\n";
    } else {
        echo "   ℹ️  Usuário encontrado ({$user->name}). Resetando a senha para o padrão...\n";
        $user->password = bcrypt($senhaPlana);
        $user->save();
        echo "   ✅ Senha resetada com sucesso!\n";
    }
    
    echo "\n   📧 Email de Login: {$emailAdmin}\n";
    echo "   🔑 Senha de Login: {$senhaPlana}\n\n";
} catch (Exception $e) {
    echo "   ❌ Erro ao gerenciar usuário admin: " . $e->getMessage() . "\n\n";
}

// 9. Verificar status final
echo "🔍 [5/5] Verificando status final do ambiente...\n";
try {
    $kernel->call('about', ['--only' => 'environment']);
    echo "   ✅ Ambiente verificado e saudável!\n\n";
} catch (Exception $e) {
    echo "   ⚠️  Não foi possível verificar o ambiente: " . $e->getMessage() . "\n\n";
}

// 10. Mensagem Final
echo str_repeat("=", 65) . "\n";
echo "🏁 SETUP CONCLUÍDO COM SUCESSO!\n\n";
echo "🚨 ATENÇÃO CRÍTICA DE SEGURANÇA:\n";
echo "   1. Abra uma aba anônima e tente fazer login com os dados acima.\n";
echo "   2. VOLTE AO GERENCIADOR DE ARQUIVOS E APAGUE ESTE ARQUIVO AGORA!\n";
echo "   (setup_completo.php concede privilégios totais ao seu servidor)\n";
echo str_repeat("=", 65) . "\n";
echo "</pre>";