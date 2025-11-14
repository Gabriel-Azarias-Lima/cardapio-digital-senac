<?php
/**
 * API para buscar produtos do banco de dados
 * Retorna produtos em formato JSON
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../classes/Produto.php';

try {
    $produto = new Produto();
    
    // Verificar se foi solicitado uma categoria específica
    $categoriaSlug = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    
    if ($categoriaSlug) {
        // Buscar produtos por categoria
        $produtos = $produto->buscarPorCategoriaSlug($categoriaSlug);
    } else {
        // Buscar todos os produtos
        $produtos = $produto->buscarTodos();
    }
    
    // Processar produtos para incluir verificação de imagem
    $produtosProcessados = [];
    foreach ($produtos as $prod) {
        $produtosProcessados[] = [
            'id' => (int)$prod['id'],
            'nome' => $prod['nome'],
            'descricao' => $prod['descricao'],
            'preco' => floatval($prod['preco']),
            'imagem_url' => $produto->verificarImagem($prod['imagem_url']),
            'categoria_nome' => $prod['categoria_nome'],
            'categoria_slug' => $prod['categoria_slug'],
            'ativo' => $prod['ativo']
        ];
    }
    
    // Retornar resposta de sucesso
    echo json_encode([
        'success' => true,
        'data' => $produtosProcessados,
        'count' => count($produtosProcessados)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Retornar erro
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar produtos',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
