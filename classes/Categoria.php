<?php
/**
 * Classe Categoria - Gerenciamento de Categorias
 */

require_once __DIR__ . '/Database.php';

class Categoria {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Busca todas as categorias ativas
     */
    public function buscarTodas() {
        try {
            $sql = "SELECT id, nome, slug, created_at 
                    FROM categorias 
                    ORDER BY nome ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar categorias: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca categoria por ID
     */
    public function buscarPorId($id) {
        try {
            $sql = "SELECT id, nome, slug, created_at 
                    FROM categorias 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar categoria por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca categoria por slug
     */
    public function buscarPorSlug($slug) {
        try {
            $sql = "SELECT id, nome, slug, created_at 
                    FROM categorias 
                    WHERE slug = :slug";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar categoria por slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Cria uma nova categoria
     */
    public function criar($nome, $slug) {
        try {
            $sql = "INSERT INTO categorias (nome, slug, created_at) 
                    VALUES (:nome, :slug, NOW()) 
                    RETURNING id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ? $result['id'] : false;
        } catch (PDOException $e) {
            error_log("Erro ao criar categoria: " . $e->getMessage());
            return false;
        }
    }
}
