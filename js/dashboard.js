let pedidosDashboardCache = [];
let chartPedidosDia = null;
let chartFaturamentoDia = null;
let chartSabores = null;

const dashSeleciona = (selector) => document.querySelector(selector);

const dashFormatoReal = (valor) => {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const dashNormalizarData = (iso) => {
    const d = new Date(iso);
    const ano = d.getFullYear();
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const dia = String(d.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
};

const dashHojeKey = () => {
    const d = new Date();
    const ano = d.getFullYear();
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const dia = String(d.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
};

const dashToggleEstado = (estado) => {
    const loading = dashSeleciona('#dashboardLoading');
    const error = dashSeleciona('#dashboardError');
    const content = dashSeleciona('#dashboardContent');

    if (loading) loading.classList.add('d-none');
    if (error) error.classList.add('d-none');
    if (content) content.classList.add('d-none');

    if (estado === 'loading' && loading) loading.classList.remove('d-none');
    if (estado === 'error' && error) error.classList.remove('d-none');
    if (estado === 'content' && content) content.classList.remove('d-none');
};

const dashCarregarPedidos = async () => {
    const periodo = dashSeleciona('#filtroPeriodo')?.value || '7d';
    const tipo = dashSeleciona('#filtroTipoPedido')?.value || '';

    dashToggleEstado('loading');

    const params = new URLSearchParams();
    params.set('periodo', periodo);
    if (tipo) params.set('tipo_pedido', tipo);
    params.set('per_page', '500');

    try {
        const response = await fetch(`api/pedidos.php?${params.toString()}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            const msg = result && result.message ? result.message : 'Erro ao carregar pedidos.';
            const msgEl = dashSeleciona('#dashboardErrorMessage');
            if (msgEl) msgEl.textContent = msg;
            dashToggleEstado('error');
            return;
        }

        pedidosDashboardCache = Array.isArray(result.data) ? result.data : [];

        dashAtualizarKpis(pedidosDashboardCache);
        dashAtualizarGraficos(pedidosDashboardCache);
        dashToggleEstado('content');
    } catch (e) {
        const msgEl = dashSeleciona('#dashboardErrorMessage');
        if (msgEl) msgEl.textContent = 'Não foi possível carregar pedidos. Verifique sua conexão.';
        dashToggleEstado('error');
    }
};

const dashAtualizarKpis = (pedidos) => {
    const hoje = dashHojeKey();
    let totalPedidosDia = 0;
    let faturamentoDia = 0;
    let pizzasVendidas = 0;
    const saboresCount = new Map();

    pedidos.forEach((pedido) => {
        const dataKey = dashNormalizarData(pedido.created_at);
        if (dataKey === hoje) {
            totalPedidosDia += 1;
            faturamentoDia += Number(pedido.total) || 0;
        }

        if (Array.isArray(pedido.itens)) {
            pedido.itens.forEach((item) => {
                const qt = Number(item.quantidade) || 0;
                pizzasVendidas += qt;
                const nome = item.produto_nome || 'Desconhecido';
                const atual = saboresCount.get(nome) || 0;
                saboresCount.set(nome, atual + qt);
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

    const elPedidos = dashSeleciona('#kpiTotalPedidosDia');
    const elFat = dashSeleciona('#kpiFaturamentoDia');
    const elPizzas = dashSeleciona('#kpiPizzasVendidas');
    const elSabor = dashSeleciona('#kpiSaborMaisPedido');

    if (elPedidos) elPedidos.textContent = String(totalPedidosDia);
    if (elFat) elFat.textContent = faturamentoDia > 0 ? dashFormatoReal(faturamentoDia) : '-';
    if (elPizzas) elPizzas.textContent = String(pizzasVendidas);
    if (elSabor) elSabor.textContent = saborMaisPedido;
};

const dashPrepararSeriePorDia = (pedidos) => {
    const porDia = new Map();
    const porDiaFat = new Map();

    pedidos.forEach((pedido) => {
        const dataKey = dashNormalizarData(pedido.created_at);
        const atualQt = porDia.get(dataKey) || 0;
        const atualFat = porDiaFat.get(dataKey) || 0;
        porDia.set(dataKey, atualQt + 1);
        porDiaFat.set(dataKey, atualFat + (Number(pedido.total) || 0));
    });

    const datasOrdenadas = Array.from(porDia.keys()).sort();

    return {
        labels: datasOrdenadas,
        pedidos: datasOrdenadas.map((d) => porDia.get(d) || 0),
        faturamento: datasOrdenadas.map((d) => porDiaFat.get(d) || 0)
    };
};

const dashPrepararSerieSabores = (pedidos) => {
    const sabores = new Map();

    pedidos.forEach((pedido) => {
        if (!Array.isArray(pedido.itens)) return;
        pedido.itens.forEach((item) => {
            const nome = item.produto_nome || 'Desconhecido';
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

const dashAtualizarGraficos = (pedidos) => {
    const serieDia = dashPrepararSeriePorDia(pedidos);
    const serieSabores = dashPrepararSerieSabores(pedidos);

    const ctxPedidos = dashSeleciona('#chartPedidosDia');
    const ctxFat = dashSeleciona('#chartFaturamentoDia');
    const ctxSabores = dashSeleciona('#chartSabores');

    if (chartPedidosDia) chartPedidosDia.destroy();
    if (chartFaturamentoDia) chartFaturamentoDia.destroy();
    if (chartSabores) chartSabores.destroy();

    if (ctxPedidos && serieDia.labels.length > 0) {
        chartPedidosDia = new Chart(ctxPedidos, {
            type: 'bar',
            data: {
                labels: serieDia.labels,
                datasets: [
                    {
                        label: 'Pedidos',
                        data: serieDia.pedidos,
                        backgroundColor: 'rgba(0, 60, 125, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    if (ctxFat && serieDia.labels.length > 0) {
        chartFaturamentoDia = new Chart(ctxFat, {
            type: 'line',
            data: {
                labels: serieDia.labels,
                datasets: [
                    {
                        label: 'Faturamento (R$)',
                        data: serieDia.faturamento,
                        borderColor: 'rgba(224, 123, 46, 0.9)',
                        backgroundColor: 'rgba(224, 123, 46, 0.2)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    if (ctxSabores && serieSabores.labels.length > 0) {
        chartSabores = new Chart(ctxSabores, {
            type: 'doughnut',
            data: {
                labels: serieSabores.labels,
                datasets: [
                    {
                        data: serieSabores.valores,
                        backgroundColor: [
                            'rgba(0, 60, 125, 0.9)',
                            'rgba(224, 123, 46, 0.9)',
                            'rgba(40, 167, 69, 0.9)',
                            'rgba(220, 53, 69, 0.9)',
                            'rgba(23, 162, 184, 0.9)',
                            'rgba(111, 66, 193, 0.9)'
                        ]
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
};

const dashInitEventos = () => {
    const filtroPeriodo = dashSeleciona('#filtroPeriodo');
    const filtroTipo = dashSeleciona('#filtroTipoPedido');
    const retry = dashSeleciona('#dashboardRetry');

    if (filtroPeriodo) filtroPeriodo.addEventListener('change', dashCarregarPedidos);
    if (filtroTipo) filtroTipo.addEventListener('change', dashCarregarPedidos);
    if (retry) retry.addEventListener('click', dashCarregarPedidos);
};

const dashInit = () => {
    dashInitEventos();
    dashCarregarPedidos();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', dashInit);
} else {
    dashInit();
}
