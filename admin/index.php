<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/fivicon.ico" />
    <title>Senac Pizzaria - Admin Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css" />
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
            <a href="index.php" class="nav-item active">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="pedidos.php" class="nav-item">
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
                <h1 class="page-title">Dashboard</h1>
            </div>
            <div class="topbar-right">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Buscar...">
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
                <label>Período</label>
                <select id="filtroPeriodo" class="filter-select">
                    <option value="hoje">Hoje</option>
                    <option value="7d" selected>Últimos 7 dias</option>
                    <option value="30d">Últimos 30 dias</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Tipo de Pedido</label>
                <select id="filtroTipoPedido" class="filter-select">
                    <option value="">Todos</option>
                    <option value="retirar">Retirar no Local</option>
                    <option value="entrega">Entrega</option>
                </select>
            </div>
            <button class="btn-primary">
                <i class="bi bi-funnel"></i>
                Aplicar Filtros
            </button>
        </div>

        <!-- Loading State -->
        <div id="dashboardLoading" class="loading-state">
            <div class="spinner"></div>
            <p>Carregando dados...</p>
        </div>

        <!-- Error State -->
        <div id="dashboardError" class="error-state hidden">
            <i class="bi bi-exclamation-triangle"></i>
            <p id="dashboardErrorMessage">Não foi possível carregar os dados.</p>
            <button id="dashboardRetry" class="btn-secondary">Tentar novamente</button>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboardContent" class="hidden">
            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card gradient-blue">
                    <div class="kpi-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-label">Pedidos do Dia</div>
                        <div class="kpi-value" id="kpiTotalPedidosDia">-</div>
                        <div class="kpi-trend positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>12% vs ontem</span>
                        </div>
                    </div>
                </div>

                <div class="kpi-card gradient-orange">
                    <div class="kpi-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-label">Faturamento do Dia</div>
                        <div class="kpi-value" id="kpiFaturamentoDia">-</div>
                        <div class="kpi-trend positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>8% vs ontem</span>
                        </div>
                    </div>
                </div>

                <div class="kpi-card gradient-green">
                    <div class="kpi-icon">
                        <i class="bi bi-pizza"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-label">Pizzas Vendidas</div>
                        <div class="kpi-value" id="kpiPizzasVendidas">-</div>
                        <div class="kpi-trend positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>15% vs ontem</span>
                        </div>
                    </div>
                </div>

                <div class="kpi-card gradient-purple">
                    <div class="kpi-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-label">Sabor Mais Pedido</div>
                        <div class="kpi-value kpi-small" id="kpiSaborMaisPedido">-</div>
                        <div class="kpi-trend">
                            <span>Top seller</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <h3>Pedidos por Dia</h3>
                            <p>Quantidade de pedidos no período</p>
                        </div>
                        <button class="icon-btn">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartPedidosDia"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <h3>Faturamento por Dia</h3>
                            <p>Total em R$ por dia</p>
                        </div>
                        <button class="icon-btn">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartFaturamentoDia"></canvas>
                    </div>
                </div>

                <div class="chart-card chart-card-large">
                    <div class="chart-header">
                        <div>
                            <h3>Sabores Mais Vendidos</h3>
                            <p>Top sabores por quantidade</p>
                        </div>
                        <button class="icon-btn">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartSabores"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/dashboard.js"></script>
</body>
</html>
