<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '7d';
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $tipoPedido = isset($_GET['tipo_pedido']) ? $_GET['tipo_pedido'] : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? max(1, min(100, (int)$_GET['per_page'])) : 20;

    $where = [];
    $params = [];

    switch ($periodo) {
        case 'hoje':
            $where[] = "p.created_at::date = CURRENT_DATE";
            break;
        case '30d':
            $where[] = "p.created_at >= (CURRENT_TIMESTAMP - INTERVAL '30 days')";
            break;
        case '7d':
        default:
            $where[] = "p.created_at >= (CURRENT_TIMESTAMP - INTERVAL '7 days')";
            break;
    }

    if ($status) {
        $statusValidos = ['criado','confirmado','em_preparo','a_caminho','entregue','cancelado'];
        if (in_array($status, $statusValidos, true)) {
            $where[] = 'p.status = :status';
            $params[':status'] = $status;
        }
    }

    if ($tipoPedido) {
        $tiposValidos = ['retirar','entrega'];
        if (in_array($tipoPedido, $tiposValidos, true)) {
            $where[] = 'p.tipo_pedido = :tipo_pedido';
            $params[':tipo_pedido'] = $tipoPedido;
        }
    }

    if ($search !== null && $search !== '') {
        $where[] = '(LOWER(c.nome) LIKE :search OR c.telefone LIKE :search)';
        $params[':search'] = '%' . mb_strtolower($search, 'UTF-8') . '%';
    }

    $whereSql = '';
    if (count($where) > 0) {
        $whereSql = 'WHERE ' . implode(' AND ', $where);
    }

    $sql = "
        SELECT
            p.id AS pedido_id,
            p.cliente_id,
            p.tipo_pedido,
            p.endereco_id,
            p.metodo_pagamento,
            p.troco_para,
            p.total,
            p.status,
            p.created_at,
            c.nome AS cliente_nome,
            c.telefone AS cliente_telefone,
            e.rua,
            e.numero,
            e.bairro,
            e.cidade,
            e.referencia,
            pi.id AS item_id,
            pi.produto_id,
            pi.quantidade,
            pi.preco_unitario,
            pi.subtotal,
            pr.nome AS produto_nome,
            cat.nome AS categoria_nome
        FROM pedidos p
        INNER JOIN clientes c ON p.cliente_id = c.id
        LEFT JOIN enderecos e ON p.endereco_id = e.id
        INNER JOIN pedido_itens pi ON pi.pedido_id = p.id
        INNER JOIN produtos pr ON pr.id = pi.produto_id
        LEFT JOIN categorias cat ON pr.categoria_id = cat.id
        $whereSql
        ORDER BY p.created_at DESC, p.id DESC, pi.id ASC
    ";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    $rows = $stmt->fetchAll();

    $pedidosMap = [];
    foreach ($rows as $row) {
        $pedidoId = (int)$row['pedido_id'];
        if (!isset($pedidosMap[$pedidoId])) {
            $enderecoResumo = null;
            if (!empty($row['rua'])) {
                $partes = [];
                $partes[] = $row['rua'];
                if (!empty($row['numero'])) {
                    $partes[count($partes) - 1] .= ', ' . $row['numero'];
                }
                if (!empty($row['bairro'])) {
                    $partes[] = $row['bairro'];
                }
                if (!empty($row['cidade'])) {
                    $partes[] = $row['cidade'];
                }
                $enderecoResumo = implode(' - ', $partes);
                if (!empty($row['referencia'])) {
                    $enderecoResumo .= ' (' . $row['referencia'] . ')';
                }
            }

            $pedidosMap[$pedidoId] = [
                'id' => $pedidoId,
                'cliente_id' => (int)$row['cliente_id'],
                'cliente_nome' => $row['cliente_nome'],
                'cliente_telefone' => $row['cliente_telefone'],
                'tipo_pedido' => $row['tipo_pedido'],
                'metodo_pagamento' => $row['metodo_pagamento'],
                'troco_para' => $row['troco_para'] !== null ? (float)$row['troco_para'] : null,
                'total' => (float)$row['total'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'endereco_resumo' => $enderecoResumo,
                'itens' => []
            ];
        }

        $pedidosMap[$pedidoId]['itens'][] = [
            'id' => (int)$row['item_id'],
            'produto_id' => (int)$row['produto_id'],
            'produto_nome' => $row['produto_nome'],
            'categoria_nome' => $row['categoria_nome'],
            'quantidade' => (int)$row['quantidade'],
            'preco_unitario' => (float)$row['preco_unitario'],
            'subtotal' => (float)$row['subtotal']
        ];
    }

    $pedidos = array_values($pedidosMap);
    $totalPedidos = count($pedidos);

    $offset = ($page - 1) * $perPage;
    $pedidosPagina = array_slice($pedidos, $offset, $perPage);

    echo json_encode([
        'success' => true,
        'data' => $pedidosPagina,
        'total' => $totalPedidos,
        'page' => $page,
        'per_page' => $perPage
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar pedidos',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
