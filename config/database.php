<?php
/**
 * Configuração do Banco de Dados Supabase
 * Usando PHP PDO com SSL
 */

// Configurações do banco de dados
define('DB_HOST', 'aws-1-sa-east-1.pooler.supabase.com');
define('DB_PORT', '6543');
define('DB_NAME', 'postgres');
define('DB_USER', 'postgres.zxodjtmgwzsjhmpxsbzt');
define('DB_PASS', 'ProjetoSenac1247');

// URL completa de conexão
define('DATABASE_URL', 'postgresql://postgres.zxodjtmgwzsjhmpxsbzt:ProjetoSenac1247@aws-1-sa-east-1.pooler.supabase.com:6543/postgres?pgbouncer=true');

// Configurações SSL
define('SSL_MODE', 'require');

// Configurações de conexão PDO
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,
    PDO::ATTR_TIMEOUT => 30
];

// DSN para conexão PostgreSQL com SSL
$dsn = sprintf(
    "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
    DB_HOST,
    DB_PORT,
    DB_NAME,
    SSL_MODE
);
