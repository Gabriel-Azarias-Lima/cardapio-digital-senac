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
     * Verifica se a imagem existe na pasta images
     * @param string $imagemUrl URL da imagem do banco
     * @return string Caminho da imagem se existir, ou imagem padrão
     */
    public function verificarImagem($imagemUrl) {
        if (empty($imagemUrl)) {
            // Caminho relativo a partir do index.php na raiz
            return 'images/pizza.png';
        }

        // Extrair apenas o nome do arquivo da URL
        $nomeArquivo = basename($imagemUrl);

        // Caminho completo da imagem (classes/ -> images/)
        $caminhoImagem = __DIR__ . '/../images/' . $nomeArquivo;

        // Verificar se o arquivo existe
        if (file_exists($caminhoImagem)) {
            // Caminho relativo para ser usado no frontend
            return 'images/' . $nomeArquivo;
        }

        // Retornar imagem padrão se não existir
        return 'images/pizza.png';
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
                    'imagem_url' => $this->verificarImagem($produto['imagem_url']),
                    'created_at' => $produto['created_at']
                ];
            }
            
            return $produtosPorCategoria;
        } catch (Exception $e) {
            error_log("Erro ao organizar produtos por categoria: " . $e->getMessage());
            return [];
        }
    }
}
