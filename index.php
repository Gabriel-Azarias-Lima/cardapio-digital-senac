<?php
/**
 * Cardápio Digital - Pizzaria
 * Conectado com Supabase via PHP PDO
 */

// Incluir classes necessárias
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Categoria.php';
require_once __DIR__ . '/classes/Produto.php';

// Variável para avisos de ambiente
$erroAmbiente = '';

// Inicializar classes e buscar dados do banco com tolerância a falhas
try {
    if (!extension_loaded('pdo_pgsql')) {
        $erroAmbiente = 'A extensão pdo_pgsql não está ativa. Abra /cardapio/diagnostico.php para instruções de habilitação no XAMPP.';
        $categorias = [];
        $produtosPorCategoria = [];
        $todosProdutos = [];
    } else {
        $categoriaClass = new Categoria();
        $produtoClass = new Produto();

        $categorias = $categoriaClass->buscarTodas();
        $produtosPorCategoria = $produtoClass->organizarPorCategoria();
        $todosProdutos = $produtoClass->buscarTodos();
    }
} catch (Throwable $e) {
    error_log("Erro ao carregar dados: " . $e->getMessage());
    $erroAmbiente = $e->getMessage();
    $categorias = [];
    $produtosPorCategoria = [];
    $todosProdutos = [];
}

// Função para formatar preço
function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}

// Função para obter imagem padrão se não houver imagem
function obterImagemProduto($imagemUrl, $nomeProduto) {
    if (!empty($imagemUrl)) {
        return $imagemUrl;
    }
    // Retorna uma imagem padrão do Unsplash baseada no nome do produto
    $termo = urlencode(str_replace(' ', '+', $nomeProduto));
    return "https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop&q=80";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzaria - Cardápio Digital</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-house-heart-fill me-2"></i>
                Pizzaria
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cardapio">Cardápio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-cart" data-bs-toggle="modal" data-bs-target="#carrinhoModal">
                            <i class="bi bi-cart3"></i>
                            <span class="cart-badge" id="cartCount">0</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-section">
        <div class="hero-overlay">
            <div class="container text-center">
                <h1 class="display-3 fw-bold text-white mb-3">Pizzaria</h1>
                <p class="lead text-white mb-4">Sabor autêntico italiano direto para sua mesa! 🍕</p>
                <a href="#cardapio" class="btn btn-hero btn-lg">Ver Cardápio</a>
            </div>
        </div>
    </section>

    <!-- Mensagem de ambiente -->
    <?php if (!empty($erroAmbiente)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning" role="alert">
            ⚠️ <?= htmlspecialchars($erroAmbiente, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cardápio -->
    <section id="cardapio" class="py-5">
        <div class="container">
            <?php if (!empty($produtosPorCategoria)): ?>
                <?php foreach ($produtosPorCategoria as $categoriaSlug => $dadosCategoria): ?>
                    <?php 
                    $categoria = $dadosCategoria['categoria'];
                    $produtos = $dadosCategoria['produtos'];
                    
                    // Definir ícones por categoria
                    $icones = [
                        'pizzas-tradicionais' => 'bi-fire text-danger',
                        'pizzas-especiais' => 'bi-star-fill text-warning',
                        'pizzas-doces' => 'bi-heart-fill text-danger',
                        'combos' => 'bi-bag-check-fill text-success',
                        'promocoes' => 'bi-lightning-fill text-warning',
                        'bebidas' => 'bi-cup-straw text-info'
                    ];
                    
                    $icone = isset($icones[$categoriaSlug]) ? $icones[$categoriaSlug] : 'bi-circle-fill text-primary';
                    ?>
                    
                    <!-- <?= htmlspecialchars($categoria['nome']) ?> -->
                    <div class="section-header mb-4">
                        <h2><i class="bi <?= $icone ?>"></i> <?= htmlspecialchars($categoria['nome']) ?></h2>
                        <p class="text-muted">Confira nossos produtos desta categoria</p>
                    </div>
                    <div class="row g-4 mb-5" id="<?= htmlspecialchars($categoriaSlug) ?>">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="col-md-6 col-lg-3">
                                <div class="product-card">
                                    <img src="<?= htmlspecialchars(obterImagemProduto($produto['imagem_url'], $produto['nome'])) ?>" 
                                         alt="<?= htmlspecialchars($produto['nome']) ?>" 
                                         class="product-img">
                                    <div class="product-body">
                                        <h5 class="product-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                                        <p class="product-description"><?= htmlspecialchars($produto['descricao']) ?></p>
                                        <p class="product-price"><?= formatarPreco($produto['preco']) ?></p>
                                        <button class="btn btn-add-cart" onclick="adicionarAoCarrinho(<?= $produto['id'] ?>)">
                                            <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    <h3 class="mt-3">Cardápio em Manutenção</h3>
                    <p class="text-muted">Estamos atualizando nosso cardápio. Volte em breve!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contato -->
    <section id="contato" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="mb-4">Entre em Contato</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-3">
                    <i class="bi bi-telephone-fill fs-1 text-danger"></i>
                    <p class="mt-2"><strong>(11) 98765-4321</strong></p>
                </div>
                <div class="col-md-4 mb-3">
                    <i class="bi bi-whatsapp fs-1 text-success"></i>
                    <p class="mt-2"><strong>(11) 98765-4321</strong></p>
                </div>
                <div class="col-md-4 mb-3">
                    <i class="bi bi-geo-alt-fill fs-1 text-primary"></i>
                    <p class="mt-2"><strong>Rua das Pizzas, 123 - São Paulo</strong></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">&copy; 2025 Pizzaria - Feito com ❤️ e muita massa!</p>
    </footer>

    <!-- Modal Carrinho -->
    <div class="modal fade" id="carrinhoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cart3 me-2"></i>Meu Carrinho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="carrinhoVazio" class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                        <p class="mt-3 text-muted">Seu carrinho está vazio</p>
                    </div>
                    <div id="carrinhoItens" style="display: none;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qtd</th>
                                        <th>Preço</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="carrinhoLista"></tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <h4>Total: <span class="text-success" id="carrinhoTotal">R$ 0,00</span></h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuar Comprando</button>
                    <button type="button" class="btn btn-danger" id="limparCarrinho">
                        <i class="bi bi-trash"></i> Limpar Carrinho
                    </button>
                    <button type="button" class="btn btn-success" id="finalizarPedidoBtn" data-bs-toggle="modal" data-bs-target="#finalizarModal">
                        <i class="bi bi-check-circle"></i> Finalizar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Finalizar Pedido -->
    <div class="modal fade" id="finalizarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Finalizar Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPedido">
                        <!-- Dados Pessoais -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-person-fill me-2"></i>Seus Dados</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nomeCliente" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone/WhatsApp *</label>
                                    <input type="tel" class="form-control" id="telefoneCliente" placeholder="(11) 98765-4321" required>
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de Pedido -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-truck me-2"></i>Tipo de Pedido</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="tipoPedido" id="tipoRetirar" value="retirar" checked>
                                        <label class="form-check-label w-100" for="tipoRetirar">
                                            <i class="bi bi-shop me-2"></i>Retirar no estabelecimento
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="tipoPedido" id="tipoEntrega" value="entrega">
                                        <label class="form-check-label w-100" for="tipoEntrega">
                                            <i class="bi bi-house-door me-2"></i>Entrega em casa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço (Mostrado apenas para entrega) -->
                        <div id="enderecoSection" style="display: none;">
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Endereço de Entrega</h6>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Rua *</label>
                                    <input type="text" class="form-control" id="rua">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Número *</label>
                                    <input type="text" class="form-control" id="numero">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bairro *</label>
                                    <input type="text" class="form-control" id="bairro">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cidade *</label>
                                    <input type="text" class="form-control" id="cidade">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Ponto de Referência</label>
                                    <input type="text" class="form-control" id="referencia" placeholder="Ex: Próximo ao mercado">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Observações</label>
                                    <textarea class="form-control" id="observacoes" rows="2" placeholder="Ex: Sem cebola, portão azul"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pagamento -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-credit-card me-2"></i>Método de Pagamento</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="pagamento" id="pagDinheiro" value="dinheiro" checked>
                                        <label class="form-check-label w-100" for="pagDinheiro">
                                            💵 Dinheiro
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="pagamento" id="pagCartao" value="cartao">
                                        <label class="form-check-label w-100" for="pagCartao">
                                            💳 Cartão
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="pagamento" id="pagPix" value="pix">
                                        <label class="form-check-label w-100" for="pagPix">
                                            ⚡ PIX
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Troco (Mostrado apenas para dinheiro) -->
                        <div id="trocoSection" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Troco para quanto?</label>
                                <input type="text" class="form-control" id="troco" placeholder="Ex: R$ 100,00">
                            </div>
                        </div>

                        <!-- Resumo do Pedido -->
                        <div class="alert alert-info">
                            <h6 class="mb-2"><i class="bi bi-receipt me-2"></i>Resumo do Pedido</h6>
                            <div id="resumoPedido"></div>
                            <hr>
                            <h5 class="mb-0">Total: <span id="totalFinal" class="text-success">R$ 0,00</span></h5>
                        </div>

                        <div id="erroValidacao" class="alert alert-danger" style="display: none;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success btn-lg" id="enviarPedidoBtn">
                        <i class="bi bi-send"></i> Enviar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagem de Confirmação -->
    <div class="confirmacao-overlay" id="confirmacaoOverlay" style="display: none;">
        <div class="confirmacao-box">
            <div class="confirmacao-icon">
                <i class="bi bi-check-circle-fill text-success"></i>
            </div>
            <h3 class="mt-4">✅ Seu pedido está sendo preparado!</h3>
            <p class="lead" id="tempoEstimado">Tempo estimado: entre 30 e 40 minutos</p>
            <p class="text-muted">Obrigado por escolher a Pizzaria! 🍕</p>
            <button class="btn btn-success mt-3" id="fecharConfirmacao">OK, Entendi!</button>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="script.js"></script>
</body>
</html>
