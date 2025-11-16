let pedidosListaCache = [];
let pedidosPaginaAtual = 1;
let pedidosPorPagina = 20;
let pedidosTotal = 0;

const pedidosSel = (selector) => document.querySelector(selector);

const pedidosFormatoReal = (valor) => {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const pedidosFormatarDataHora = (iso) => {
    const d = new Date(iso);
    return d.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const pedidosToggleEstado = (estado) => {
    const loading = pedidosSel('#pedidosLoading');
    const error = pedidosSel('#pedidosError');
    const content = pedidosSel('#pedidosContent');
    const semResultados = pedidosSel('#pedidosSemResultados');

    if (loading) loading.classList.add('d-none');
    if (error) error.classList.add('d-none');
    if (content) content.classList.add('d-none');
    if (semResultados) semResultados.classList.add('d-none');

    if (estado === 'loading' && loading) loading.classList.remove('d-none');
    if (estado === 'error' && error) error.classList.remove('d-none');
    if (estado === 'content' && content) content.classList.remove('d-none');
    if (estado === 'empty' && semResultados) semResultados.classList.remove('d-none');
};

const pedidosCarregar = async () => {
    const periodo = pedidosSel('#filtroPeriodoPedidos')?.value || '7d';
    const status = pedidosSel('#filtroStatus')?.value || '';
    const busca = pedidosSel('#buscaCliente')?.value.trim() || '';

    pedidosToggleEstado('loading');

    const params = new URLSearchParams();
    params.set('periodo', periodo);
    if (status) params.set('status', status);
    if (busca) params.set('search', busca);
    params.set('page', String(pedidosPaginaAtual));
    params.set('per_page', String(pedidosPorPagina));

    try {
        const response = await fetch(`api/pedidos.php?${params.toString()}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            const msg = result && result.message ? result.message : 'Erro ao carregar pedidos.';
            const msgEl = pedidosSel('#pedidosErrorMessage');
            if (msgEl) msgEl.textContent = msg;
            pedidosToggleEstado('error');
            return;
        }

        pedidosListaCache = Array.isArray(result.data) ? result.data : [];
        pedidosTotal = typeof result.total === 'number' ? result.total : pedidosListaCache.length;

        if (pedidosListaCache.length === 0) {
            pedidosToggleEstado('empty');
            pedidosAtualizarResumo();
            pedidosAtualizarPaginacao();
            return;
        }

        pedidosRenderTabela();
        pedidosAtualizarResumo();
        pedidosAtualizarPaginacao();
        pedidosToggleEstado('content');
    } catch (e) {
        const msgEl = pedidosSel('#pedidosErrorMessage');
        if (msgEl) msgEl.textContent = 'Não foi possível carregar pedidos. Verifique sua conexão.';
        pedidosToggleEstado('error');
    }
};

const pedidosRenderTabela = () => {
    const tbody = pedidosSel('#ordersTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    pedidosListaCache.forEach((pedido) => {
        const tr = document.createElement('tr');

        const dataHora = pedidosFormatarDataHora(pedido.created_at);
        const total = Number(pedido.total) || 0;
        const tipoLabel = pedido.tipo_pedido === 'entrega' ? 'Entrega' : 'Retirada';
        const status = pedido.status || '';
        const metodo = pedido.metodo_pagamento || '';
        const endereco = pedido.endereco_resumo || '';

        let itensHtml = '';
        if (Array.isArray(pedido.itens)) {
            itensHtml = pedido.itens.map((item) => {
                const nome = item.produto_nome || 'Item';
                const qt = Number(item.quantidade) || 0;
                const sub = Number(item.subtotal) || 0;
                return `<div>${qt}x ${nome} <span class="text-muted">(${pedidosFormatoReal(sub)})</span></div>`;
            }).join('');
        }

        const statusBadgeClass = (() => {
            if (status === 'entregue') return 'bg-success';
            if (status === 'cancelado') return 'bg-danger';
            if (status === 'em_preparo' || status === 'a_caminho') return 'bg-warning text-dark';
            if (status === 'confirmado') return 'bg-info text-dark';
            return 'bg-secondary';
        })();

        tr.innerHTML = `
            <td class="text-muted">#${pedido.id}</td>
            <td>${dataHora}</td>
            <td>
                <div class="fw-semibold">${pedido.cliente_nome || ''}</div>
                <div class="text-muted small">${pedido.cliente_telefone || ''}</div>
            </td>
            <td>${itensHtml}</td>
            <td class="fw-semibold">${pedidosFormatoReal(total)}</td>
            <td>${tipoLabel}</td>
            <td>
                <span class="badge ${statusBadgeClass}">${status}</span>
            </td>
            <td>${metodo}</td>
            <td><div class="small">${endereco}</div></td>
        `;

        tbody.appendChild(tr);
    });
};

const pedidosAtualizarResumo = () => {
    const resumo = pedidosSel('#ordersSummary');
    if (!resumo) return;

    if (pedidosTotal === 0) {
        resumo.textContent = 'Nenhum pedido encontrado.';
        return;
    }

    const inicio = (pedidosPaginaAtual - 1) * pedidosPorPagina + 1;
    const fim = Math.min(pedidosPaginaAtual * pedidosPorPagina, pedidosTotal);
    resumo.textContent = `Mostrando ${inicio} a ${fim} de ${pedidosTotal} pedidos`;
};

const pedidosAtualizarPaginacao = () => {
    const totalPaginas = Math.max(1, Math.ceil(pedidosTotal / pedidosPorPagina));
    const prevItem = pedidosSel('#pagePrevItem');
    const nextItem = pedidosSel('#pageNextItem');

    if (prevItem) {
        if (pedidosPaginaAtual <= 1) prevItem.classList.add('disabled');
        else prevItem.classList.remove('disabled');
    }

    if (nextItem) {
        if (pedidosPaginaAtual >= totalPaginas) nextItem.classList.add('disabled');
        else nextItem.classList.remove('disabled');
    }
};

const pedidosInitEventos = () => {
    const periodo = pedidosSel('#filtroPeriodoPedidos');
    const status = pedidosSel('#filtroStatus');
    const busca = pedidosSel('#buscaCliente');
    const retry = pedidosSel('#pedidosRetry');
    const prev = pedidosSel('#pagePrev');
    const next = pedidosSel('#pageNext');

    if (periodo) periodo.addEventListener('change', () => {
        pedidosPaginaAtual = 1;
        pedidosCarregar();
    });

    if (status) status.addEventListener('change', () => {
        pedidosPaginaAtual = 1;
        pedidosCarregar();
    });

    if (busca) busca.addEventListener('input', () => {
        pedidosPaginaAtual = 1;
        const timeoutKey = '__pedidos_busca_timeout__';
        if (window[timeoutKey]) clearTimeout(window[timeoutKey]);
        window[timeoutKey] = setTimeout(() => {
            pedidosCarregar();
        }, 400);
    });

    if (retry) retry.addEventListener('click', pedidosCarregar);

    if (prev) prev.addEventListener('click', () => {
        if (pedidosPaginaAtual > 1) {
            pedidosPaginaAtual -= 1;
            pedidosCarregar();
        }
    });

    if (next) next.addEventListener('click', () => {
        const totalPaginas = Math.max(1, Math.ceil(pedidosTotal / pedidosPorPagina));
        if (pedidosPaginaAtual < totalPaginas) {
            pedidosPaginaAtual += 1;
            pedidosCarregar();
        }
    });
};

const pedidosInit = () => {
    pedidosInitEventos();
    pedidosCarregar();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', pedidosInit);
} else {
    pedidosInit();
}
