// ========== GLOBAL VARIABLES ==========
let pedidosDashboardCache = [];
let chartPedidosDia = null;
let chartFaturamentoDia = null;
let chartSabores = null;

// ========== UTILITY FUNCTIONS ==========
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);

const formatCurrency = (valor) => {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const normalizeDate = (iso) => {
    const d = new Date(iso);
    const ano = d.getFullYear();
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const dia = String(d.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
};

const getTodayKey = () => {
    const d = new Date();
    const ano = d.getFullYear();
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const dia = String(d.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
};

// ========== UI STATE MANAGEMENT ==========
const toggleState = (estado) => {
    const loading = $('#dashboardLoading');
    const error = $('#dashboardError');
    const content = $('#dashboardContent');

    [loading, error, content].forEach(el => el?.classList.add('hidden'));

    if (estado === 'loading' && loading) loading.classList.remove('hidden');
    if (estado === 'error' && error) error.classList.remove('hidden');
    if (estado === 'content' && content) content.classList.remove('hidden');
};

// ========== DATA FETCHING ==========
const carregarPedidos = async () => {
    const periodo = $('#filtroPeriodo')?.value || '7d';
    const tipo = $('#filtroTipoPedido')?.value || '';

    toggleState('loading');

    const params = new URLSearchParams();
    params.set('periodo', periodo);
    if (tipo) params.set('tipo_pedido', tipo);
    params.set('per_page', '500');

    try {
        const response = await fetch(`../api/pedidos.php?${params.toString()}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            const msg = result?.message || 'Erro ao carregar pedidos.';
            const msgEl = $('#dashboardErrorMessage');
            if (msgEl) msgEl.textContent = msg;
            toggleState('error');
            return;
        }

        pedidosDashboardCache = Array.isArray(result.data) ? result.data : [];

        atualizarKpis(pedidosDashboardCache);
        atualizarGraficos(pedidosDashboardCache);
        toggleState('content');
    } catch (e) {
        const msgEl = $('#dashboardErrorMessage');
        if (msgEl) msgEl.textContent = 'Não foi possível carregar pedidos. Verifique sua conexão.';
        toggleState('error');
    }
};

// ========== KPI UPDATES ==========
const atualizarKpis = (pedidos) => {
    const hoje = getTodayKey();
    let totalPedidosDia = 0;
    let faturamentoDia = 0;
    let pizzasVendidas = 0;
    const saboresCount = new Map();

    pedidos.forEach((pedido) => {
        const dataKey = normalizeDate(pedido.created_at);
        if (dataKey === hoje) {
            totalPedidosDia += 1;
            faturamentoDia += Number(pedido.total) || 0;
        }

        if (Array.isArray(pedido.itens)) {
            pedido.itens.forEach((item) => {
                const nome = item.produto_nome || 'Desconhecido';
                const categoria = item.categoria_nome || '';
                const qt = Number(item.quantidade) || 0;
                
                // Filtrar bebidas (refrigerantes) pela categoria
                const isBebida = categoria.toLowerCase() === 'bebidas';
                
                // Não contar bebidas nas pizzas vendidas
                if (!isBebida) {
                    pizzasVendidas += qt;
                }
                
                // Não incluir bebidas no ranking de sabores
                if (!isBebida) {
                    const atual = saboresCount.get(nome) || 0;
                    saboresCount.set(nome, atual + qt);
                }
            });
        }
    });

    let saborMaisPedido = '-';
    let maiorQt = 0;
    saboresCount.forEach((qt, nome) => {
        if (qt > maiorQt) {
            maiorQt = qt;
            saborMaisPedido = nome;
        }
    });

    // Update KPI values with animation
    animateValue($('#kpiTotalPedidosDia'), 0, totalPedidosDia, 1000);
    
    const elFat = $('#kpiFaturamentoDia');
    if (elFat) {
        elFat.textContent = faturamentoDia > 0 ? formatCurrency(faturamentoDia) : 'R$ 0,00';
    }
    
    animateValue($('#kpiPizzasVendidas'), 0, pizzasVendidas, 1000);
    
    const elSabor = $('#kpiSaborMaisPedido');
    if (elSabor) elSabor.textContent = saborMaisPedido;
};

// Animate number counting
const animateValue = (element, start, end, duration) => {
    if (!element) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
};

// ========== CHART DATA PREPARATION ==========
const prepararSeriePorDia = (pedidos) => {
    const porDia = new Map();
    const porDiaFat = new Map();

    pedidos.forEach((pedido) => {
        const dataKey = normalizeDate(pedido.created_at);
        const atualQt = porDia.get(dataKey) || 0;
        const atualFat = porDiaFat.get(dataKey) || 0;
        porDia.set(dataKey, atualQt + 1);
        porDiaFat.set(dataKey, atualFat + (Number(pedido.total) || 0));
    });

    const datasOrdenadas = Array.from(porDia.keys()).sort();

    return {
        labels: datasOrdenadas.map(d => {
            const [ano, mes, dia] = d.split('-');
            return `${dia}/${mes}`;
        }),
        pedidos: datasOrdenadas.map((d) => porDia.get(d) || 0),
        faturamento: datasOrdenadas.map((d) => porDiaFat.get(d) || 0)
    };
};

const prepararSerieSabores = (pedidos) => {
    const sabores = new Map();

    pedidos.forEach((pedido) => {
        if (!Array.isArray(pedido.itens)) return;
        pedido.itens.forEach((item) => {
            const nome = item.produto_nome || 'Desconhecido';
            const categoria = item.categoria_nome || '';
            
            // Filtrar bebidas (refrigerantes) do gráfico pela categoria
            const isBebida = categoria.toLowerCase() === 'bebidas';
            if (isBebida) {
                return;
            }
            
            const qt = Number(item.quantidade) || 0;
            const atual = sabores.get(nome) || 0;
            sabores.set(nome, atual + qt);
        });
    });

    const entrada = Array.from(sabores.entries()).sort((a, b) => b[1] - a[1]).slice(0, 6);
    const labels = entrada.map(([nome]) => nome);
    const valores = entrada.map(([, qt]) => qt);

    return { labels, valores };
};

// ========== CHART RENDERING ==========
const atualizarGraficos = (pedidos) => {
    const serieDia = prepararSeriePorDia(pedidos);
    const serieSabores = prepararSerieSabores(pedidos);

    renderChartPedidos(serieDia);
    renderChartFaturamento(serieDia);
    renderChartSabores(serieSabores);
};

const renderChartPedidos = (serieDia) => {
    const ctx = $('#chartPedidosDia');
    if (!ctx) return;

    if (chartPedidosDia) chartPedidosDia.destroy();

    if (serieDia.labels.length > 0) {
        chartPedidosDia = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: serieDia.labels,
                datasets: [{
                    label: 'Pedidos',
                    data: serieDia.pedidos,
                    backgroundColor: 'rgba(0, 60, 125, 0.8)',
                    borderColor: 'rgba(0, 60, 125, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
};

const renderChartFaturamento = (serieDia) => {
    const ctx = $('#chartFaturamentoDia');
    if (!ctx) return;

    if (chartFaturamentoDia) chartFaturamentoDia.destroy();

    if (serieDia.labels.length > 0) {
        chartFaturamentoDia = new Chart(ctx, {
            type: 'line',
            data: {
                labels: serieDia.labels,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: serieDia.faturamento,
                    borderColor: 'rgba(224, 123, 46, 1)',
                    backgroundColor: 'rgba(224, 123, 46, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgba(224, 123, 46, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(0);
                            },
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
};

const renderChartSabores = (serieSabores) => {
    const ctx = $('#chartSabores');
    if (!ctx) return;

    if (chartSabores) chartSabores.destroy();

    if (serieSabores.labels.length > 0) {
        chartSabores = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: serieSabores.labels,
                datasets: [{
                    data: serieSabores.valores,
                    backgroundColor: [
                        'rgba(0, 60, 125, 0.9)',
                        'rgba(224, 123, 46, 0.9)',
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(139, 92, 246, 0.9)',
                        'rgba(245, 158, 11, 0.9)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                }
            }
        });
    }
};

// ========== EVENT HANDLERS ==========
const initEventos = () => {
    const filtroPeriodo = $('#filtroPeriodo');
    const filtroTipo = $('#filtroTipoPedido');
    const retry = $('#dashboardRetry');
    const mobileMenuBtn = $('#mobileMenuBtn');
    const sidebar = $('#sidebar');

    if (filtroPeriodo) filtroPeriodo.addEventListener('change', carregarPedidos);
    if (filtroTipo) filtroTipo.addEventListener('change', carregarPedidos);
    if (retry) retry.addEventListener('click', carregarPedidos);

    // Mobile menu toggle
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && sidebar?.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !mobileMenuBtn?.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
};

// ========== INITIALIZATION ==========
const init = () => {
    initEventos();
    carregarPedidos();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
