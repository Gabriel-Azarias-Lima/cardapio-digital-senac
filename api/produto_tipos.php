<?php
/**
 * API para buscar tipos/sabores de produtos (bebidas)
 * Retorna tipos em formato JSON
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar se foi solicitado tipos de um produto específico
    $produtoId = isset($_GET['produto_id']) ? intval($_GET['produto_id']) : null;
    
    if ($produtoId) {
        // Buscar tipos de um produto específico
        $sql = "SELECT id, produto_id, nome, ordem, created_at 
                FROM produto_tipos 
                WHERE produto_id = :produto_id
                ORDER BY ordem ASC, nome ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
        $stmt->execute();
        
        $tipos = $stmt->fetchAll();
    } else {
        // Buscar todos os tipos agrupados por produto
        $sql = "SELECT pt.id, pt.produto_id, pt.nome, pt.ordem, pt.created_at,
                       p.nome as produto_nome
                FROM produto_tipos pt
                INNER JOIN produtos p ON pt.produto_id = p.id
                ORDER BY p.nome ASC, pt.ordem ASC, pt.nome ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        $tipos = $stmt->fetchAll();
    }
    
    // Retornar resposta de sucesso
    echo json_encode([
        'success' => true,
        'data' => $tipos,
        'count' => count($tipos)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Retornar erro
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar tipos de produtos',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
