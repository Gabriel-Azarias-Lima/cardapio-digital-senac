<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/fivicon.ico" />

    <title>Senac Pizzaria - Delivery</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- modelos de pizza na lista e no carrinho -->
    <div class="models">

        <!-- pizza na lista -->
        <div class="pizza-item">
            <a href="">
                <div class="pizza-item--img">
                    <img src="" />
                    <div class="pizza-item--add">+</div>
                </div>
                <div class="pizza-item--content">
                    <div class="pizza-item--price">R$ --</div>
                    <div class="pizza-item--name">--</div>
                    <div class="pizza-item--desc">--</div>
                </div>
            </a>
            <div class="pizza-item--footer">
                <div class="pizza-item--rating">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <span>4.8</span>
                </div>
            </div>
        </div>

        <!-- pizza no carrinho -->
        <div class="cart--item">
            <img src="" />
            <div class="cart--item-nome">--</div>
            <div class="cart--item--qtarea">
                <button class="cart--item-qtmenos">-</button>
                <div class="cart--item--qt">1</div>
                <button class="cart--item-qtmais">+</button>
            </div>
        </div>

    </div>
    <!-- /models -->

    <!-- Header Moderno -->
    <header class="navbar navbar-expand-lg navbar-dark fixed-top custom-header">
        <div class="container-fluid px-4">
            <div class="navbar-brand d-flex align-items-center">
                <div class="location-info">
                    <div class="restaurant-info-header">
                        <h4 class="mb-0 fw-bold text-white">Senac Pizzaria</h4>
                        <small class="text-light opacity-75">Delivery & Balcão</small>
                    </div>
                    <div class="location-details">
                        <div class="location-address">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                            <small class="text-light">Rua das Pizzas, 123 - Centro</small>
                        </div>
                        <div class="service-hours">
                            <i class="bi bi-clock-fill text-success me-1"></i>
                            <small class="text-light">Seg-Dom: 18h às 23h</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-actions d-flex align-items-center">
                <div class="delivery-info me-4 d-none d-md-block">
                    <div class="text-end">
                        <div class="delivery-time">
                            <i class="bi bi-clock-fill text-warning me-1"></i>
                            <small class="text-light">Entrega em até 45min</small>
                        </div>
                        <div class="delivery-phone">
                            <i class="bi bi-telephone-fill text-success me-1"></i>
                            <small class="text-light">(11) 9999-9999</small>
                        </div>
                    </div>
                </div>
                
                <div class="cart-button menu-openner">
                    <div class="cart-icon-wrapper">
                        <i class="bi bi-bag-fill"></i>
                        <span class="cart-badge">0</span>
                    </div>
                    <div class="cart-text d-none d-sm-block">
                        <small>Meu Pedido</small>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- /Header -->

    <!-- conteudo principal -->
    <main class="container-fluid">
        <!-- Banner Promocional -->
        <div class="promotional-banner">
            <div class="banner-content">
                <div class="banner-image">
                    <img src="images/pizza-mussarela.png" alt="Pizza Deliciosa" class="pizza-hero-img">
                </div>
                <div class="banner-text">
                    <h1 class="banner-title">UM NOVO<br><span class="highlight">CONCEITO</span></h1>
                    <h2 class="banner-subtitle">COM SABOR<br><span class="sem-igual">SEM IGUAL</span></h2>
                    <div class="banner-description">
                        <p>Pizzas artesanais feitas com ingredientes frescos e receitas exclusivas</p>
                    </div>
                </div>
                <div class="banner-logo">
                    <img src="images/senac-pizzaria.png" alt="Senac Pizzaria">
                </div>
            </div>
        </div>

        <!-- Informações da Pizzaria -->
        <div class="restaurant-info">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <div class="restaurant-logo">
                            <img src="images/senac-pizzaria.png" alt="Senac Pizzaria">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="restaurant-name">SENAC PIZZARIA</h3>
                        <div class="restaurant-details">
                            <span class="badge bg-success me-2"><i class="bi bi-check-circle"></i> Aberto</span>
                            <span class="delivery-time"><i class="bi bi-clock"></i> 30-45 min</span>
                            <span class="delivery-fee ms-3"><i class="bi bi-truck"></i> Entrega R$ 5,00</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-info-circle"></i> Informações
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Pesquisa -->
        <div class="search-section">
            <div class="container">
                <div class="search-bar">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Busque por um produto..." id="searchInput">
                </div>
            </div>
        </div>

        <!-- Categorias -->
        <div class="categories-section">
            <div class="container">
                <div class="categories-scroll">
                    <button class="category-btn active" data-category="all">
                        <i class="bi bi-grid"></i>
                        Oferta do dia
                    </button>
                    <button class="category-btn" data-category="tradicional">
                        <i class="bi bi-award"></i>
                        Tradicionais
                    </button>
                    <button class="category-btn" data-category="especial">
                        <i class="bi bi-star"></i>
                        Especiais
                    </button>
                    <button class="category-btn" data-category="doce">
                        <i class="bi bi-heart"></i>
                        Doces
                    </button>
                    <button class="category-btn" data-category="bebida">
                        <i class="bi bi-cup-straw"></i>
                        Bebidas
                    </button>
                    <!-- <button class="category-btn" data-category="refrigerante">
                        <i class="bi bi-droplet"></i>
                        Refrigerantes
                    </button> -->
                </div>
            </div>
        </div>

        <!-- Cardápio -->
        <div class="menu-section">
            <div class="container">
                <h2 class="section-title">
                    <i class="bi bi-fire text-danger"></i> 
                    Nosso Cardápio
                </h2>
                <div class="pizza-area"></div>
            </div>
        </div>
    </main>
    <!-- /conteudo principal -->

    <!-- aside do carrinho -->
    <aside class="cart-sidebar">
        <div class="cart--area">
            <div class="cart-header">
                <div class="cart-title">
                    <i class="bi bi-bag-check-fill text-primary me-2"></i>
                    <h3 class="mb-0 fw-bold">Meu Pedido</h3>
                </div>
                <button class="menu-closer btn-close-custom">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="cart-content">
                <div class="cart-items-wrapper">
                    <div class="cart"></div>
                    <div class="empty-cart d-none">
                        <div class="text-center py-5">
                            <i class="bi bi-bag text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Seu carrinho está vazio</p>
                            <small class="text-muted">Adicione algumas pizzas deliciosas!</small>
                        </div>
                    </div>
                </div>
                
                <div class="cart--details">
                    <div class="cart-summary">
                        <div class="cart--totalitem subtotal">
                            <span><i class="bi bi-receipt me-2"></i>Subtotal</span>
                            <span class="fw-bold">R$ --</span>
                        </div>
                        <div class="cart--totalitem desconto">
                            <span><i class="bi bi-percent me-2"></i>Desconto</span>
                            <span class="fw-bold text-success">R$ --</span>
                        </div>
                        <div class="cart--totalitem total big">
                            <span><i class="bi bi-calculator me-2"></i>Total</span>
                            <span class="fw-bold">R$ --</span>
                        </div>
                    </div>
                    
                    <div class="cart-actions">
                        <div class="cart--finalizar">
                            <i class="bi bi-credit-card me-2"></i>
                            <span>Finalizar Pedido</span>
                            <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                        
                        <div class="delivery-info-cart mt-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-truck text-primary me-2"></i>
                                <small class="text-muted">Entrega grátis acima de R$ 30,00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <!-- /aside do carrinho -->

    <!-- janela modal .pizzaWindowArea -->
    <div class="pizzaWindowArea">
        <div class="pizzaWindowBody">
            <div class="pizzaInfo--cancelMobileButton">Voltar</div>
            <div class="pizzaBig">
                <img src="" />
            </div>
            <div class="pizzaInfo">
                <h1>--</h1>
                <div class="pizzaInfo--desc">--</div>
                <div class="pizzaInfo--sizearea">
                    <div class="pizzaInfo--sector">Tamanho</div>
                    <div class="pizzaInfo--sizes">
                        <div data-key="P" class="pizzaInfo--size">PEQUENA <span>--</span></div>
                        <div data-key="M" class="pizzaInfo--size">MÉDIA <span>--</span></div>
                        <div data-key="G" class="pizzaInfo--size selected">GRANDE <span>--</span></div>
                    </div>
                </div>
                <div class="pizzaInfo--pricearea">
                    <div class="pizzaInfo--sector">Preço</div>
                    <div class="pizzaInfo--price">
                        <div class="pizzaInfo--actualPrice">R$ --</div>
                        <div class="pizzaInfo--qtarea">
                            <button class="pizzaInfo--qtmenos">-</button>
                            <div class="pizzaInfo--qt">1</div>
                            <button class="pizzaInfo--qtmais">+</button>
                        </div>
                    </div>
                </div>
                <div class="pizzaInfo--addButton">Adicionar ao carrinho</div>
                <div class="pizzaInfo--cancelButton">Cancelar</div>
            </div>
        </div>
    </div>
    <!-- /janela modal .pizzaWindowArea -->

    <!-- Modal de Checkout -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="checkoutModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>Finalizar Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="checkoutForm">
                        <!-- Dados Pessoais -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-person-circle me-2"></i>Dados Pessoais
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nomeCompleto" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nomeCompleto" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label">Telefone *</label>
                                    <input type="tel" class="form-control" id="telefone" placeholder="(00) 00000-0000" required>
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de Pedido -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-truck me-2"></i>Tipo de Pedido
                            </h6>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipoPedido" id="retirar" value="retirar" checked>
                                <label class="btn btn-outline-primary" for="retirar">
                                    <i class="bi bi-shop me-2"></i>Retirar no Local
                                </label>

                                <input type="radio" class="btn-check" name="tipoPedido" id="entrega" value="entrega">
                                <label class="btn btn-outline-primary" for="entrega">
                                    <i class="bi bi-house-door me-2"></i>Entrega em Casa
                                </label>
                            </div>
                        </div>

                        <!-- Endereço (aparece apenas se for entrega) -->
                        <div id="enderecoArea" class="mb-4" style="display: none;">
                            <label for="endereco" class="form-label">Endereço de Entrega *</label>
                            <textarea class="form-control" id="endereco" rows="2" placeholder="Rua, número, bairro, complemento..."></textarea>
                        </div>

                        <!-- Método de Pagamento -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-credit-card me-2"></i>Método de Pagamento
                            </h6>
                            <select class="form-select" id="metodoPagamento" required>
                                <option value="" selected disabled>Selecione o método</option>
                                <option value="dinheiro">💵 Dinheiro</option>
                                <option value="cartao">💳 Cartão</option>
                                <option value="pix">📱 PIX</option>
                            </select>
                        </div>

                        <!-- Campo Troco (aparece apenas se for dinheiro) -->
                        <div id="trocoArea" class="mb-4" style="display: none;">
                            <label for="troco" class="form-label">Troco para quanto? *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="troco" step="0.01" placeholder="0,00">
                            </div>
                        </div>

                        <!-- Resumo do Pedido -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-receipt me-2"></i>Resumo do Pedido
                                </h6>
                                <div id="resumoPedido" class="mb-3"></div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-5">Total:</span>
                                    <span class="fw-bold fs-4 text-primary" id="totalCheckout">R$ 0,00</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-lg" id="confirmarPedido">
                        <i class="bi bi-check-circle me-2"></i>Confirmar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmacaoModal" tabindex="-1" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Pedido Confirmado!</h3>
                    <p class="text-muted mb-4">Seu pedido está sendo preparado com carinho!</p>
                    <div class="alert alert-info">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Tempo estimado:</strong> <span id="tempoEstimado">47 minutos</span>
                    </div>
                    <p class="lead mb-4">Obrigado por escolher a <strong>Senac Pizzaria</strong>! 🍕</p>
                    <button type="button" class="btn btn-primary btn-lg" data-bs-dismiss="modal" onclick="location.reload()">
                        <i class="bi bi-house-door me-2"></i>Fazer Novo Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/pizzas.js"></script>
    <script src="js/sript.js"></script>
</body>
</html>
