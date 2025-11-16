<?php
/**
 * API para buscar categorias do banco de dados
 * Retorna categorias em formato JSON
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT id, nome, slug, created_at 
            FROM categorias 
            ORDER BY nome ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $categorias = $stmt->fetchAll();
    
    // Retornar resposta de sucesso
    echo json_encode([
        'success' => true,
        'data' => $categorias,
        'count' => count($categorias)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Retornar erro
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar categorias',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
