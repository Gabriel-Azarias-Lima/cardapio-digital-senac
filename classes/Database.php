<?php
/**
 * Classe Database - Gerenciamento de Conexão com Supabase
 * Implementa padrão Singleton para conexão única
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static $instance = null;
    private $connection = null;
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Método para obter instância única da classe
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Estabelece conexão com o banco de dados Supabase
     */
    private function connect() {
        try {
            global $dsn, $pdo_options;
            
            // Log das configurações para debug
            error_log("Tentando conectar com DSN: " . $dsn);
            error_log("Usuário: " . DB_USER);
            
            // Verificar se as extensões necessárias estão carregadas
            if (!extension_loaded('pdo')) {
                throw new Exception("Extensão PDO não está instalada");
            }
            
            if (!extension_loaded('pdo_pgsql')) {
                throw new Exception("Extensão PDO PostgreSQL não está instalada");
            }
            
            $this->connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                $pdo_options
            );
            
            // Configurações adicionais para PostgreSQL
            $this->connection->exec("SET client_encoding TO 'UTF8'");
            
        } catch (PDOException $e) {
            $errorMsg = "Erro de conexão PDO: " . $e->getMessage();
            error_log($errorMsg);
            throw new Exception($errorMsg);
        } catch (Exception $e) {
            $errorMsg = "Erro geral de conexão: " . $e->getMessage();
            error_log($errorMsg);
            throw new Exception($errorMsg);
        }
    }
    
    /**
     * Retorna a conexão PDO
     */
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    /**
     * Testa a conexão com o banco
     */
    public function testConnection() {
        try {
            $stmt = $this->connection->query("SELECT 1 as test");
            return $stmt->fetch()['test'] === 1;
        } catch (PDOException $e) {
            error_log("Erro no teste de conexão: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Fecha a conexão
     */
    public function closeConnection() {
        $this->connection = null;
    }
    
    /**
     * Previne clonagem da instância
     */
    private function __clone() {}
    
    /**
     * Previne deserialização da instância
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
