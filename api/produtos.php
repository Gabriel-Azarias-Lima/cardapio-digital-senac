<?php
/**
 * API para fornecer dados dos produtos em JSON
 * Para uso com JavaScript
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir classes necessárias
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Categoria.php';
require_once __DIR__ . '/../classes/Produto.php';

try {
    // Inicializar classes
    $produtoClass = new Produto();
    
    // Buscar todos os produtos
    $produtos = $produtoClass->buscarTodos();
    
    // Organizar produtos no formato esperado pelo JavaScript
    $produtosFormatados = [];
    
    foreach ($produtos as $produto) {
        $produtoFormatado = [
            'id' => (int)$produto['id'],
            'nome' => $produto['nome'],
            'descricao' => $produto['descricao'],
            'preco' => (float)$produto['preco'],
            'imagem' => !empty($produto['imagem_url']) ? $produto['imagem_url'] : '',
            'categoria_slug' => $produto['categoria_slug'],
            'categoria_nome' => $produto['categoria_nome']
        ];
        
        $produtosFormatados[] = $produtoFormatado;
    }
    
    // Retornar resposta JSON
    echo json_encode([
        'success' => true,
        'data' => $produtosFormatados,
        'total' => count($produtosFormatados)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Retornar erro em JSON
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno do servidor',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
