// ========== GLOBAL VARIABLES ==========
let pedidosCache = [];
let currentPage = 1;
let perPage = 10;
let totalPedidos = 0;

// ========== UTILITY FUNCTIONS ==========
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);

const formatCurrency = (valor) => {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const formatDate = (iso) => {
    const d = new Date(iso);
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const ano = d.getFullYear();
    const hora = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return `${dia}/${mes}/${ano}, ${hora}:${min}`;
};

const getInitials = (nome) => {
    const parts = nome.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return nome.substring(0, 2).toUpperCase();
};

// ========== UI STATE MANAGEMENT ==========
const toggleState = (estado) => {
    const loading = $('#pedidosLoading');
    const error = $('#pedidosError');
    const empty = $('#pedidosSemResultados');
    const content = $('#pedidosContent');

    [loading, error, empty, content].forEach(el => el?.classList.add('hidden'));

    if (estado === 'loading' && loading) loading.classList.remove('hidden');
    if (estado === 'error' && error) error.classList.remove('hidden');
    if (estado === 'empty' && empty) empty.classList.remove('hidden');
    if (estado === 'content' && content) content.classList.remove('hidden');
};

// ========== DATA FETCHING ==========
const carregarPedidos = async () => {
    const periodo = $('#filtroPeriodoPedidos')?.value || '7d';
    const status = $('#filtroStatus')?.value || '';
    const search = $('#buscaCliente')?.value.trim() || '';

    toggleState('loading');

    const params = new URLSearchParams();
    params.set('periodo', periodo);
    if (status) params.set('status', status);
    if (search) params.set('search', search);
    params.set('page', currentPage);
    params.set('per_page', perPage);

    try {
        const response = await fetch(`../api/pedidos.php?${params.toString()}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            const msg = result?.message || 'Erro ao carregar pedidos.';
            const msgEl = $('#pedidosErrorMessage');
            if (msgEl) msgEl.textContent = msg;
            toggleState('error');
            return;
        }

        pedidosCache = Array.isArray(result.data) ? result.data : [];
        totalPedidos = result.total || 0;

        if (pedidosCache.length === 0) {
            toggleState('empty');
        } else {
            renderPedidos(pedidosCache);
            updatePagination();
            toggleState('content');
        }
    } catch (e) {
        const msgEl = $('#pedidosErrorMessage');
        if (msgEl) msgEl.textContent = 'Não foi possível carregar pedidos. Verifique sua conexão.';
        toggleState('error');
    }
};

// ========== STATUS MAPPING ==========
const getStatusInfo = (status) => {
    const statusMap = {
        'criado': { label: 'Criado', icon: 'bi-circle' },
        'confirmado': { label: 'Confirmado', icon: 'bi-check-circle' },
        'em_preparo': { label: 'Em Preparo', icon: 'bi-clock' },
        'a_caminho': { label: 'A Caminho', icon: 'bi-truck' },
        'entregue': { label: 'Entregue', icon: 'bi-check-circle-fill' },
        'cancelado': { label: 'Cancelado', icon: 'bi-x-circle' }
    };
    return statusMap[status] || { label: status, icon: 'bi-circle' };
};

const getTipoPedidoInfo = (tipo) => {
    const tipoMap = {
        'retirar': { label: 'Retirar', icon: 'bi-bag' },
        'entrega': { label: 'Entrega', icon: 'bi-truck' }
    };
    return tipoMap[tipo] || { label: tipo, icon: 'bi-circle' };
};

const getMetodoPagamentoLabel = (metodo) => {
    const metodoMap = {
        'dinheiro': 'Dinheiro',
        'cartao': 'Cartão',
        'pix': 'PIX'
    };
    return metodoMap[metodo] || metodo;
};

// ========== RENDER FUNCTIONS ==========
const renderPedidos = (pedidos) => {
    const container = $('#pedidosGrid');
    if (!container) return;

    container.innerHTML = '';

    pedidos.forEach(pedido => {
        const card = createPedidoCard(pedido);
        container.appendChild(card);
    });
};

const createPedidoCard = (pedido) => {
    const card = document.createElement('div');
    card.className = 'pedido-card';

    const statusInfo = getStatusInfo(pedido.status);
    const tipoInfo = getTipoPedidoInfo(pedido.tipo_pedido);
    const initials = getInitials(pedido.cliente_nome);

    // Itens HTML
    const itensHTML = pedido.itens.map(item => `
        <div class="item-pedido">
            <div class="item-info">
                <div class="item-quantidade">${item.quantidade}x</div>
                <div class="item-nome">${item.produto_nome}</div>
            </div>
            <div class="item-preco">${formatCurrency(item.subtotal)}</div>
        </div>
    `).join('');

    // Endereço (se for entrega)
    const enderecoHTML = pedido.tipo_pedido === 'entrega' && pedido.endereco_resumo ? `
        <div class="endereco-info">
            <i class="bi bi-geo-alt-fill"></i>
            <div class="endereco-texto">${pedido.endereco_resumo}</div>
        </div>
    ` : '';

    // Troco (se pagamento em dinheiro)
    const trocoHTML = pedido.metodo_pagamento === 'dinheiro' && pedido.troco_para ? `
        <span> - Troco para ${formatCurrency(pedido.troco_para)}</span>
    ` : '';

    card.innerHTML = `
        <div class="pedido-header">
            <div class="pedido-info">
                <div class="pedido-numero">Pedido #${pedido.id}</div>
                <div class="pedido-data">
                    <i class="bi bi-clock"></i>
                    ${formatDate(pedido.created_at)}
                </div>
            </div>
            <div class="pedido-status">
                <span class="status-badge status-${pedido.status}">
                    <i class="${statusInfo.icon}"></i>
                    ${statusInfo.label}
                </span>
                <span class="tipo-badge">
                    <i class="${tipoInfo.icon}"></i>
                    ${tipoInfo.label}
                </span>
            </div>
        </div>

        <div class="pedido-body">
            <div class="cliente-info">
                <div class="cliente-avatar">${initials}</div>
                <div class="cliente-detalhes">
                    <div class="cliente-nome">${pedido.cliente_nome}</div>
                    <div class="cliente-telefone">
                        <i class="bi bi-telephone"></i>
                        ${pedido.cliente_telefone}
                    </div>
                </div>
            </div>

            <div class="itens-lista">
                ${itensHTML}
            </div>

            ${enderecoHTML}

            <div class="pagamento-info">
                <i class="bi bi-credit-card"></i>
                <div class="pagamento-texto">
                    <span class="pagamento-metodo">${getMetodoPagamentoLabel(pedido.metodo_pagamento)}</span>
                    ${trocoHTML}
                </div>
            </div>
        </div>

        <div class="pedido-footer">
            <div class="pedido-total">
                <div class="total-label">Total</div>
                <div class="total-valor">${formatCurrency(pedido.total)}</div>
            </div>
        </div>
    `;

    return card;
};

// ========== PAGINATION ==========
const updatePagination = () => {
    const info = $('#paginationInfo');
    const prevBtn = $('#pagePrev');
    const nextBtn = $('#pageNext');

    if (info) {
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, totalPedidos);
        info.textContent = `Mostrando ${start}-${end} de ${totalPedidos} pedidos`;
    }

    if (prevBtn) {
        prevBtn.disabled = currentPage === 1;
    }

    if (nextBtn) {
        const totalPages = Math.ceil(totalPedidos / perPage);
        nextBtn.disabled = currentPage >= totalPages;
    }
};

const goToPage = (page) => {
    currentPage = page;
    carregarPedidos();
};

// ========== EVENT HANDLERS ==========
const initEventos = () => {
    const filtroStatus = $('#filtroStatus');
    const filtroPeriodo = $('#filtroPeriodoPedidos');
    const buscaCliente = $('#buscaCliente');
    const retry = $('#pedidosRetry');
    const prevBtn = $('#pagePrev');
    const nextBtn = $('#pageNext');
    const mobileMenuBtn = $('#mobileMenuBtn');
    const sidebar = $('#sidebar');

    if (filtroStatus) filtroStatus.addEventListener('change', () => {
        currentPage = 1;
        carregarPedidos();
    });

    if (filtroPeriodo) filtroPeriodo.addEventListener('change', () => {
        currentPage = 1;
        carregarPedidos();
    });

    if (buscaCliente) {
        let searchTimeout;
        buscaCliente.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                carregarPedidos();
            }, 500);
        });
    }

    if (retry) retry.addEventListener('click', carregarPedidos);

    if (prevBtn) prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    });

    if (nextBtn) nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(totalPedidos / perPage);
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    });

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
