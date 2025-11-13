<?php
/**
 * Diagnóstico do Ambiente PHP para Conexão Supabase
 */

echo "<!DOCTYPE html>\n";
echo "<html lang='pt-BR'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <title>Diagnóstico PHP - Supabase</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }\n";
echo "        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }\n";
echo "        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 5px 0; }\n";
echo "        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 5px 0; }\n";
echo "        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 5px 0; }\n";
echo "        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 5px 0; }\n";
echo "        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }\n";
echo "        h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px; }\n";
echo "        table { width: 100%; border-collapse: collapse; margin: 10px 0; }\n";
echo "        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }\n";
echo "        th { background-color: #f2f2f2; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <h1>🔍 Diagnóstico PHP para Supabase</h1>\n";

// 1. Informações básicas do PHP
echo "<h2>1. Informações do PHP</h2>\n";
echo "<table>\n";
echo "<tr><th>Item</th><th>Valor</th><th>Status</th></tr>\n";
echo "<tr><td>Versão PHP</td><td>" . phpversion() . "</td><td>";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "<span class='success'>✅ OK</span>";
} else {
    echo "<span class='error'>❌ Versão muito antiga</span>";
}
echo "</td></tr>\n";

echo "<tr><td>Sistema Operacional</td><td>" . php_uname() . "</td><td><span class='info'>ℹ️</span></td></tr>\n";
echo "<tr><td>SAPI</td><td>" . php_sapi_name() . "</td><td><span class='info'>ℹ️</span></td></tr>\n";
echo "</table>\n";

// 2. Extensões necessárias
echo "<h2>2. Extensões PHP Necessárias</h2>\n";
$extensoes = [
    'pdo' => 'PDO (PHP Data Objects)',
    'pdo_pgsql' => 'PDO PostgreSQL Driver',
    'openssl' => 'OpenSSL (para SSL/TLS)',
    'curl' => 'cURL (recomendado)',
    'json' => 'JSON'
];

echo "<table>\n";
echo "<tr><th>Extensão</th><th>Descrição</th><th>Status</th></tr>\n";
foreach ($extensoes as $ext => $desc) {
    echo "<tr><td>$ext</td><td>$desc</td><td>";
    if (extension_loaded($ext)) {
        echo "<span class='success'>✅ Instalada</span>";
    } else {
        echo "<span class='error'>❌ Não instalada</span>";
    }
    echo "</td></tr>\n";
}
echo "</table>\n";

// 3. Verificar drivers PDO disponíveis
echo "<h2>3. Drivers PDO Disponíveis</h2>\n";
if (extension_loaded('pdo')) {
    $drivers = PDO::getAvailableDrivers();
    echo "<div class='info'>Drivers encontrados: " . implode(', ', $drivers) . "</div>\n";
    
    if (in_array('pgsql', $drivers)) {
        echo "<div class='success'>✅ Driver PostgreSQL (pgsql) está disponível</div>\n";
    } else {
        echo "<div class='error'>❌ Driver PostgreSQL (pgsql) NÃO está disponível</div>\n";
        echo "<div class='warning'>⚠️ Você precisa instalar a extensão php-pdo-pgsql</div>\n";
    }
} else {
    echo "<div class='error'>❌ PDO não está instalado</div>\n";
}

// 4. Teste de conexão básica
echo "<h2>4. Teste de Conexão</h2>\n";

// Incluir configurações
$configFile = __DIR__ . '/config/database.php';
if (file_exists($configFile)) {
    require_once $configFile;
    echo "<div class='success'>✅ Arquivo de configuração encontrado</div>\n";
    
    echo "<h3>Configurações:</h3>\n";
    echo "<table>\n";
    echo "<tr><th>Parâmetro</th><th>Valor</th></tr>\n";
    echo "<tr><td>Host</td><td>" . DB_HOST . "</td></tr>\n";
    echo "<tr><td>Porta</td><td>" . DB_PORT . "</td></tr>\n";
    echo "<tr><td>Banco</td><td>" . DB_NAME . "</td></tr>\n";
    echo "<tr><td>Usuário</td><td>" . DB_USER . "</td></tr>\n";
    echo "<tr><td>SSL Mode</td><td>" . SSL_MODE . "</td></tr>\n";
    echo "<tr><td>DSN</td><td>" . htmlspecialchars($dsn) . "</td></tr>\n";
    echo "</table>\n";
    
    // Tentar conexão se as extensões estiverem disponíveis
    if (extension_loaded('pdo') && extension_loaded('pdo_pgsql')) {
        echo "<h3>Tentativa de Conexão:</h3>\n";
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
            echo "<div class='success'>✅ Conexão estabelecida com sucesso!</div>\n";
            
            // Testar query simples
            $stmt = $pdo->query("SELECT version() as version, now() as current_time");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<div class='info'>\n";
            echo "<strong>Versão PostgreSQL:</strong> " . htmlspecialchars($result['version']) . "<br>\n";
            echo "<strong>Hora atual do servidor:</strong> " . htmlspecialchars($result['current_time']) . "\n";
            echo "</div>\n";
            
        } catch (PDOException $e) {
            echo "<div class='error'>❌ Erro na conexão: " . htmlspecialchars($e->getMessage()) . "</div>\n";
            
            // Análise do erro
            $errorCode = $e->getCode();
            echo "<div class='warning'>\n";
            echo "<strong>Código do erro:</strong> $errorCode<br>\n";
            
            if (strpos($e->getMessage(), 'could not find driver') !== false) {
                echo "<strong>Solução:</strong> Instale a extensão pdo_pgsql<br>\n";
                echo "<strong>XAMPP:</strong> Descomente ;extension=pdo_pgsql no php.ini\n";
            } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
                echo "<strong>Solução:</strong> Verifique se o host e porta estão corretos<br>\n";
                echo "<strong>Firewall:</strong> Certifique-se que a porta 6543 está liberada\n";
            } elseif (strpos($e->getMessage(), 'authentication failed') !== false) {
                echo "<strong>Solução:</strong> Verifique usuário e senha<br>\n";
                echo "<strong>Supabase:</strong> Confirme as credenciais no painel\n";
            } elseif (strpos($e->getMessage(), 'SSL') !== false) {
                echo "<strong>Solução:</strong> Problema com SSL/TLS<br>\n";
                echo "<strong>OpenSSL:</strong> Verifique se está instalado e configurado\n";
            }
            echo "</div>\n";
        }
    } else {
        echo "<div class='error'>❌ Não é possível testar conexão - extensões PDO ausentes</div>\n";
    }
    
} else {
    echo "<div class='error'>❌ Arquivo de configuração não encontrado: $configFile</div>\n";
}

// 5. Informações do php.ini
echo "<h2>5. Configurações PHP.ini</h2>\n";
echo "<table>\n";
echo "<tr><th>Configuração</th><th>Valor</th></tr>\n";
echo "<tr><td>php.ini carregado</td><td>" . php_ini_loaded_file() . "</td></tr>\n";
echo "<tr><td>extension_dir</td><td>" . ini_get('extension_dir') . "</td></tr>\n";
echo "<tr><td>allow_url_fopen</td><td>" . (ini_get('allow_url_fopen') ? 'On' : 'Off') . "</td></tr>\n";
echo "<tr><td>default_socket_timeout</td><td>" . ini_get('default_socket_timeout') . "s</td></tr>\n";
echo "</table>\n";

// 6. Soluções recomendadas
echo "<h2>6. Soluções para XAMPP</h2>\n";
echo "<div class='info'>\n";
echo "<h3>Para habilitar PostgreSQL no XAMPP:</h3>\n";
echo "<ol>\n";
echo "<li>Abra o arquivo <code>php.ini</code> (geralmente em <code>C:\\xampp\\php\\php.ini</code>)</li>\n";
echo "<li>Procure por <code>;extension=pdo_pgsql</code></li>\n";
echo "<li>Remova o <code>;</code> para descomentar: <code>extension=pdo_pgsql</code></li>\n";
echo "<li>Procure por <code>;extension=pgsql</code></li>\n";
echo "<li>Remova o <code>;</code> para descomentar: <code>extension=pgsql</code></li>\n";
echo "<li>Reinicie o Apache no XAMPP Control Panel</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<div class='warning'>\n";
echo "<h3>Se as extensões não estiverem disponíveis:</h3>\n";
echo "<ol>\n";
echo "<li>Baixe as DLLs do PostgreSQL para PHP</li>\n";
echo "<li>Coloque na pasta <code>C:\\xampp\\php\\ext\\</code></li>\n";
echo "<li>Ou instale um pacote PHP completo com PostgreSQL</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?>
