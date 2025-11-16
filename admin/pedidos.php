<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/fivicon.ico" />
    <title>Senac Pizzaria - Pedidos</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css" />
    <link rel="stylesheet" href="css/pedidos.css" />
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span>Senac Pizzaria</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="pedidos.php" class="nav-item active">
                <i class="bi bi-receipt"></i>
                <span>Pedidos</span>
                <span class="badge">3</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../index.php" class="nav-item">
                <i class="bi bi-box-arrow-left"></i>
                <span>Voltar para Loja</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">Pedidos</h1>
            </div>
            <div class="topbar-right">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="buscaCliente" placeholder="Buscar por cliente...">
                </div>
                <button class="icon-btn">
                    <i class="bi bi-bell"></i>
                    <span class="notification-dot"></span>
                </button>
                <div class="user-menu">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=003c7d&color=fff" alt="Admin">
                    <span>Admin</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>
        </header>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-group">
                <label>Status</label>
                <select id="filtroStatus" class="filter-select">
                    <option value="">Todos</option>
                    <option value="criado">Criado</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="em_preparo">Em Preparo</option>
                    <option value="a_caminho">A Caminho</option>
                    <option value="entregue">Entregue</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Período</label>
                <select id="filtroPeriodoPedidos" class="filter-select">
                    <option value="hoje">Hoje</option>
                    <option value="7d" selected>Últimos 7 dias</option>
                    <option value="30d">Últimos 30 dias</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div id="pedidosLoading" class="loading-state">
            <div class="spinner"></div>
            <p>Carregando pedidos...</p>
        </div>

        <!-- Error State -->
        <div id="pedidosError" class="error-state hidden">
            <i class="bi bi-exclamation-triangle"></i>
            <p id="pedidosErrorMessage">Não foi possível carregar os pedidos.</p>
            <button id="pedidosRetry" class="btn-secondary">Tentar novamente</button>
        </div>

        <!-- Empty State -->
        <div id="pedidosSemResultados" class="empty-state hidden">
            <i class="bi bi-inbox"></i>
            <p>Nenhum pedido encontrado</p>
            <small>Tente ajustar os filtros de busca</small>
        </div>

        <!-- Pedidos Content -->
        <div id="pedidosContent" class="hidden">
            <div class="pedidos-container">
                <div class="pedidos-grid" id="pedidosGrid">
                    <!-- Pedidos serão inseridos aqui via JavaScript -->
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info" id="paginationInfo">
                    Mostrando 1-10 de 50 pedidos
                </div>
                <div class="pagination-buttons">
                    <button class="btn-secondary" id="pagePrev" disabled>
                        <i class="bi bi-chevron-left"></i>
                        Anterior
                    </button>
                    <button class="btn-secondary" id="pageNext">
                        Próxima
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="js/pedidos.js"></script>
</body>
</html>
