<?php
/**
 * Classe Produto - Gerenciamento de Produtos
 */

require_once __DIR__ . '/Database.php';

class Produto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Busca todos os produtos ativos
     */
    public function buscarTodos() {
        try {
            $sql = "SELECT p.id, p.categoria_id, p.nome, p.descricao, p.preco, 
                           p.imagem_url, p.ativo, p.created_at,
                           c.nome as categoria_nome, c.slug as categoria_slug
                    FROM produtos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.ativo = true
                    ORDER BY c.nome ASC, p.nome ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca produtos por categoria
     */
    public function buscarPorCategoria($categoriaId) {
        try {
            $sql = "SELECT p.id, p.categoria_id, p.nome, p.descricao, p.preco, 
                           p.imagem_url, p.ativo, p.created_at,
                           c.nome as categoria_nome, c.slug as categoria_slug
                    FROM produtos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.categoria_id = :categoria_id AND p.ativo = true
                    ORDER BY p.nome ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos por categoria: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca produtos por slug da categoria
     */
    public function buscarPorCategoriaSlug($categoriaSlug) {
        try {
            $sql = "SELECT p.id, p.categoria_id, p.nome, p.descricao, p.preco, 
                           p.imagem_url, p.ativo, p.created_at,
                           c.nome as categoria_nome, c.slug as categoria_slug
                    FROM produtos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE c.slug = :categoria_slug AND p.ativo = true
                    ORDER BY p.nome ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':categoria_slug', $categoriaSlug, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos por categoria slug: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca produto por ID
     */
    public function buscarPorId($id) {
        try {
            $sql = "SELECT p.id, p.categoria_id, p.nome, p.descricao, p.preco, 
                           p.imagem_url, p.ativo, p.created_at,
                           c.nome as categoria_nome, c.slug as categoria_slug
                    FROM produtos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.id = :id AND p.ativo = true";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Organiza produtos por categoria
     */
    public function organizarPorCategoria() {
        try {
            $produtos = $this->buscarTodos();
            $produtosPorCategoria = [];
            
            foreach ($produtos as $produto) {
                $categoriaSlug = $produto['categoria_slug'];
                
                if (!isset($produtosPorCategoria[$categoriaSlug])) {
                    $produtosPorCategoria[$categoriaSlug] = [
                        'categoria' => [
                            'id' => $produto['categoria_id'],
                            'nome' => $produto['categoria_nome'],
                            'slug' => $produto['categoria_slug']
                        ],
                        'produtos' => []
                    ];
                }
                
                $produtosPorCategoria[$categoriaSlug]['produtos'][] = [
                    'id' => $produto['id'],
                    'nome' => $produto['nome'],
                    'descricao' => $produto['descricao'],
                    'preco' => floatval($produto['preco']),
                    'imagem_url' => $produto['imagem_url'],
                    'created_at' => $produto['created_at']
                ];
            }
            
            return $produtosPorCategoria;
        } catch (Exception $e) {
            error_log("Erro ao organizar produtos por categoria: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cria um novo produto
     */
    public function criar($categoriaId, $nome, $descricao, $preco, $imagemUrl = null) {
        try {
            $sql = "INSERT INTO produtos (categoria_id, nome, descricao, preco, imagem_url, ativo, created_at) 
                    VALUES (:categoria_id, :nome, :descricao, :preco, :imagem_url, true, NOW()) 
                    RETURNING id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
            $stmt->bindParam(':imagem_url', $imagemUrl, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ? $result['id'] : false;
        } catch (PDOException $e) {
            error_log("Erro ao criar produto: " . $e->getMessage());
            return false;
        }
    }
}
