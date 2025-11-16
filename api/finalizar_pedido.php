<?php
/**
 * API para finalizar pedido
 * Recebe JSON com dados do cliente, endereço (texto), itens e total
 * Salva em: clientes, enderecos (se entrega), pedidos, pedido_itens
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    $rawBody = file_get_contents('php://input');
    $data = json_decode($rawBody, true);

    if (!is_array($data)) {
        throw new Exception('Payload inválido');
    }

    if (empty($data['cliente']['nome']) || empty($data['cliente']['telefone'])) {
        throw new Exception('Nome e telefone do cliente são obrigatórios');
    }

    if (empty($data['tipo_pedido']) || !in_array($data['tipo_pedido'], ['retirar', 'entrega'], true)) {
        throw new Exception('Tipo de pedido inválido');
    }

    if (empty($data['metodo_pagamento']) || !in_array($data['metodo_pagamento'], ['dinheiro', 'cartao', 'pix'], true)) {
        throw new Exception('Método de pagamento inválido');
    }

    if (empty($data['itens']) || !is_array($data['itens'])) {
        throw new Exception('Itens do pedido são obrigatórios');
    }

    $db->beginTransaction();

    $nome = trim($data['cliente']['nome']);
    $telefone = trim($data['cliente']['telefone']);

    $stmt = $db->prepare('SELECT id FROM clientes WHERE telefone = :telefone');
    $stmt->bindValue(':telefone', $telefone, PDO::PARAM_STR);
    $stmt->execute();
    $cliente = $stmt->fetch();

    if ($cliente) {
        $clienteId = (int)$cliente['id'];
    } else {
        $stmt = $db->prepare('INSERT INTO clientes (nome, telefone) VALUES (:nome, :telefone) RETURNING id');
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':telefone', $telefone, PDO::PARAM_STR);
        $stmt->execute();
        $clienteId = (int)$stmt->fetchColumn();
    }

    $enderecoId = null;
    if ($data['tipo_pedido'] === 'entrega') {
        if (empty($data['endereco']) || !is_array($data['endereco'])) {
            throw new Exception('Dados de endereço de entrega são obrigatórios');
        }

        $rua = trim($data['endereco']['rua'] ?? '');
        $numero = trim($data['endereco']['numero'] ?? '');
        $bairro = trim($data['endereco']['bairro'] ?? '');
        $cidade = trim($data['endereco']['cidade'] ?? '');
        $referencia = trim($data['endereco']['referencia'] ?? '');
        $observacoes = trim($data['endereco']['observacoes'] ?? '');

        if ($rua === '' || $numero === '' || $bairro === '' || $cidade === '') {
            throw new Exception('Rua, número, bairro e cidade são obrigatórios para entrega');
        }

        $stmt = $db->prepare('INSERT INTO enderecos (cliente_id, rua, numero, bairro, cidade, referencia, observacoes)
                              VALUES (:cliente_id, :rua, :numero, :bairro, :cidade, :referencia, :observacoes)
                              RETURNING id');
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->bindValue(':rua', $rua, PDO::PARAM_STR);
        $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindValue(':bairro', $bairro, PDO::PARAM_STR);
        $stmt->bindValue(':cidade', $cidade, PDO::PARAM_STR);
        if ($referencia !== '') {
            $stmt->bindValue(':referencia', $referencia, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':referencia', null, PDO::PARAM_NULL);
        }
        if ($observacoes !== '') {
            $stmt->bindValue(':observacoes', $observacoes, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':observacoes', null, PDO::PARAM_NULL);
        }
        $stmt->execute();
        $enderecoId = (int)$stmt->fetchColumn();
    }

    $total = isset($data['total']) ? (float)$data['total'] : 0.0;
    if ($total <= 0) {
        throw new Exception('Total do pedido inválido');
    }

    $trocoPara = isset($data['troco_para']) && $data['troco_para'] !== null
        ? (float)$data['troco_para']
        : null;

    $stmt = $db->prepare('INSERT INTO pedidos (cliente_id, tipo_pedido, endereco_id, metodo_pagamento, troco_para, total, status)
                          VALUES (:cliente_id, :tipo_pedido, :endereco_id, :metodo_pagamento, :troco_para, :total, :status)
                          RETURNING id');
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->bindValue(':tipo_pedido', $data['tipo_pedido'], PDO::PARAM_STR);
    if ($enderecoId !== null) {
        $stmt->bindValue(':endereco_id', $enderecoId, PDO::PARAM_INT);
    } else {
        $stmt->bindValue(':endereco_id', null, PDO::PARAM_NULL);
    }
    $stmt->bindValue(':metodo_pagamento', $data['metodo_pagamento'], PDO::PARAM_STR);
    if ($trocoPara !== null) {
        $stmt->bindValue(':troco_para', $trocoPara);
    } else {
        $stmt->bindValue(':troco_para', null, PDO::PARAM_NULL);
    }
    $stmt->bindValue(':total', $total);
    $stmt->bindValue(':status', 'criado', PDO::PARAM_STR);
    $stmt->execute();
    $pedidoId = (int)$stmt->fetchColumn();

    $stmtItem = $db->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario)
                              VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)');

    foreach ($data['itens'] as $item) {
        $produtoId = isset($item['produto_id']) ? (int)$item['produto_id'] : 0;
        $quantidade = isset($item['quantidade']) ? (int)$item['quantidade'] : 0;
        $precoUnitario = isset($item['preco_unitario']) ? (float)$item['preco_unitario'] : 0.0;

        if ($produtoId <= 0 || $quantidade <= 0 || $precoUnitario <= 0) {
            throw new Exception('Dados de item inválidos');
        }

        $stmtItem->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
        $stmtItem->bindValue(':produto_id', $produtoId, PDO::PARAM_INT);
        $stmtItem->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmtItem->bindValue(':preco_unitario', $precoUnitario);
        $stmtItem->execute();
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'pedido_id' => $pedidoId
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao finalizar pedido',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
