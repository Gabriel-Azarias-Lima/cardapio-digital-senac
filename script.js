// ===== Dados dos Produtos (carregados do banco) =====
let produtos = {};
let todosProdutos = [];

// Função para carregar produtos do banco de dados
async function carregarProdutosDoBanco() {
    try {
        const response = await fetch('./api/produtos.php');
        const data = await response.json();
        
        if (data.success) {
            todosProdutos = data.data;
            organizarProdutosPorCategoria();
            return true;
        } else {
            console.error('Erro ao carregar produtos:', data.error);
            return false;
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        return false;
    }
}

// Função para organizar produtos por categoria
function organizarProdutosPorCategoria() {
    produtos = {};
    
    todosProdutos.forEach(produto => {
        const categoriaSlug = produto.categoria_slug;
        
        if (!produtos[categoriaSlug]) {
            produtos[categoriaSlug] = [];
        }
        
        // Adicionar badge baseado na categoria
        if (categoriaSlug.includes('combo')) {
            produto.badge = 'combo';
        } else if (categoriaSlug.includes('promocao')) {
            produto.badge = 'promo';
        }
        
        produtos[categoriaSlug].push(produto);
    });
}

// ===== Variável Global do Carrinho =====
let carrinho = [];

// ===== Inicialização =====
document.addEventListener('DOMContentLoaded', async () => {
    carregarCarrinhoLocalStorage();
    
    // Carregar produtos do banco se não foram carregados via PHP
    if (todosProdutos.length === 0) {
        const sucesso = await carregarProdutosDoBanco();
        if (sucesso) {
            renderizarProdutos();
        }
    }
    
    inicializarEventos();
    atualizarCarrinho();
});

// ===== Renderizar Produtos =====
function renderizarProdutos() {
    // Renderizar todas as categorias dinamicamente
    Object.keys(produtos).forEach(categoriaSlug => {
        renderizarCategoria(categoriaSlug, produtos[categoriaSlug]);
    });
}

function renderizarCategoria(containerId, items) {
    const container = document.getElementById(containerId);
    container.innerHTML = items.map(item => criarCardProduto(item)).join('');
}

function criarCardProduto(produto) {
    const badge = produto.badge === 'promo' 
        ? '<span class="promo-badge">🔥 Promoção!</span>' 
        : produto.badge === 'combo' 
        ? '<span class="combo-badge">💥 Combo</span>' 
        : '';

    return `
        <div class="col-md-6 col-lg-3">
            <div class="product-card">
                ${badge}
                <img src="${produto.imagem}" alt="${produto.nome}" class="product-img">
                <div class="product-body">
                    <h5 class="product-title">${produto.nome}</h5>
                    <p class="product-description">${produto.descricao}</p>
                    <p class="product-price">R$ ${produto.preco.toFixed(2)}</p>
                    <button class="btn btn-add-cart" onclick="adicionarAoCarrinho(${produto.id})">
                        <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ===== Gerenciamento do Carrinho =====
function adicionarAoCarrinho(produtoId) {
    const produto = encontrarProdutoPorId(produtoId);
    
    if (!produto) return;

    const itemExistente = carrinho.find(item => item.id === produtoId);

    if (itemExistente) {
        itemExistente.quantidade++;
    } else {
        carrinho.push({
            id: produto.id,
            nome: produto.nome,
            preco: produto.preco,
            quantidade: 1
        });
    }

    salvarCarrinhoLocalStorage();
    atualizarCarrinho();
    mostrarNotificacao('Item adicionado ao carrinho!');
}

function removerDoCarrinho(produtoId) {
    carrinho = carrinho.filter(item => item.id !== produtoId);
    salvarCarrinhoLocalStorage();
    atualizarCarrinho();
}

function limparCarrinho() {
    if (carrinho.length === 0) return;
    
    if (confirm('Deseja realmente limpar o carrinho?')) {
        carrinho = [];
        salvarCarrinhoLocalStorage();
        atualizarCarrinho();
        mostrarNotificacao('Carrinho limpo!');
    }
}

function atualizarCarrinho() {
    const cartCount = document.getElementById('cartCount');
    const carrinhoVazio = document.getElementById('carrinhoVazio');
    const carrinhoItens = document.getElementById('carrinhoItens');
    const carrinhoLista = document.getElementById('carrinhoLista');
    const carrinhoTotal = document.getElementById('carrinhoTotal');
    const finalizarBtn = document.getElementById('finalizarPedidoBtn');

    const totalItens = carrinho.reduce((acc, item) => acc + item.quantidade, 0);
    cartCount.textContent = totalItens;

    if (carrinho.length === 0) {
        carrinhoVazio.style.display = 'block';
        carrinhoItens.style.display = 'none';
        finalizarBtn.disabled = true;
    } else {
        carrinhoVazio.style.display = 'none';
        carrinhoItens.style.display = 'block';
        finalizarBtn.disabled = false;

        carrinhoLista.innerHTML = carrinho.map(item => `
            <tr>
                <td>${item.nome}</td>
                <td>${item.quantidade}</td>
                <td>R$ ${item.preco.toFixed(2)}</td>
                <td>R$ ${(item.preco * item.quantidade).toFixed(2)}</td>
                <td>
                    <button class="btn btn-remove-item btn-sm" onclick="removerDoCarrinho(${item.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        const total = calcularTotal();
        carrinhoTotal.textContent = `R$ ${total.toFixed(2)}`;
    }
}

function calcularTotal() {
    return carrinho.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);
}

function encontrarProdutoPorId(id) {
    return todosProdutos.find(p => p.id === id);
}

// ===== LocalStorage =====
function salvarCarrinhoLocalStorage() {
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
}

function carregarCarrinhoLocalStorage() {
    const carrinhoSalvo = localStorage.getItem('carrinho');
    if (carrinhoSalvo) {
        carrinho = JSON.parse(carrinhoSalvo);
    }
}

// ===== Finalização do Pedido =====
function inicializarEventos() {
    // Limpar carrinho
    document.getElementById('limparCarrinho').addEventListener('click', limparCarrinho);

    // Tipo de pedido (Retirar ou Entrega)
    const tipoRetirar = document.getElementById('tipoRetirar');
    const tipoEntrega = document.getElementById('tipoEntrega');

    tipoRetirar.addEventListener('change', () => {
        document.getElementById('enderecoSection').style.display = 'none';
        limparCamposEndereco();
    });

    tipoEntrega.addEventListener('change', () => {
        document.getElementById('enderecoSection').style.display = 'block';
    });

    // Método de pagamento
    const pagDinheiro = document.getElementById('pagDinheiro');
    const pagCartao = document.getElementById('pagCartao');
    const pagPix = document.getElementById('pagPix');

    pagDinheiro.addEventListener('change', () => {
        document.getElementById('trocoSection').style.display = 'block';
    });

    pagCartao.addEventListener('change', () => {
        document.getElementById('trocoSection').style.display = 'none';
    });

    pagPix.addEventListener('change', () => {
        document.getElementById('trocoSection').style.display = 'none';
    });

    // Atualizar resumo ao abrir modal de finalização
    const finalizarModal = document.getElementById('finalizarModal');
    finalizarModal.addEventListener('show.bs.modal', atualizarResumo);

    // Enviar pedido
    document.getElementById('enviarPedidoBtn').addEventListener('click', enviarPedido);

    // Fechar confirmação
    document.getElementById('fecharConfirmacao').addEventListener('click', () => {
        document.getElementById('confirmacaoOverlay').style.display = 'none';
        location.reload(); // Recarrega a página para limpar tudo
    });
}

function limparCamposEndereco() {
    document.getElementById('rua').value = '';
    document.getElementById('numero').value = '';
    document.getElementById('bairro').value = '';
    document.getElementById('cidade').value = '';
    document.getElementById('referencia').value = '';
    document.getElementById('observacoes').value = '';
}

function atualizarResumo() {
    const resumoDiv = document.getElementById('resumoPedido');
    const totalFinal = document.getElementById('totalFinal');

    resumoDiv.innerHTML = carrinho.map(item => `
        <div class="d-flex justify-content-between mb-1">
            <span>${item.quantidade}x ${item.nome}</span>
            <span>R$ ${(item.preco * item.quantidade).toFixed(2)}</span>
        </div>
    `).join('');

    totalFinal.textContent = `R$ ${calcularTotal().toFixed(2)}`;
}

function enviarPedido() {
    const erroDiv = document.getElementById('erroValidacao');
    erroDiv.style.display = 'none';

    // Validar campos obrigatórios
    const nome = document.getElementById('nomeCliente').value.trim();
    const telefone = document.getElementById('telefoneCliente').value.trim();
    const tipoPedido = document.querySelector('input[name="tipoPedido"]:checked').value;
    const metodoPagamento = document.querySelector('input[name="pagamento"]:checked').value;

    // Validação básica
    if (!nome || !telefone) {
        mostrarErro('Por favor, preencha seu nome e telefone.');
        return;
    }

    // Validação de endereço se for entrega
    if (tipoPedido === 'entrega') {
        const rua = document.getElementById('rua').value.trim();
        const numero = document.getElementById('numero').value.trim();
        const bairro = document.getElementById('bairro').value.trim();
        const cidade = document.getElementById('cidade').value.trim();

        if (!rua || !numero || !bairro || !cidade) {
            mostrarErro('Por favor, preencha todos os campos de endereço obrigatórios.');
            return;
        }
    }

    // Tudo validado, fechar modais e mostrar confirmação
    const carrinhoModal = bootstrap.Modal.getInstance(document.getElementById('carrinhoModal'));
    const finalizarModal = bootstrap.Modal.getInstance(document.getElementById('finalizarModal'));

    if (carrinhoModal) carrinhoModal.hide();
    if (finalizarModal) finalizarModal.hide();

    mostrarConfirmacao();
}

function mostrarErro(mensagem) {
    const erroDiv = document.getElementById('erroValidacao');
    erroDiv.textContent = mensagem;
    erroDiv.style.display = 'block';

    // Scroll até o erro
    erroDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function mostrarConfirmacao() {
    // Gerar tempo aleatório entre 20 e 50 minutos
    const tempoMin = 20;
    const tempoMax = 50;
    const tempo = Math.floor(Math.random() * (tempoMax - tempoMin + 1)) + tempoMin;

    document.getElementById('tempoEstimado').textContent = 
        `Tempo estimado: ${tempo} minutos`;

    document.getElementById('confirmacaoOverlay').style.display = 'flex';

    // Limpar carrinho após confirmação
    carrinho = [];
    salvarCarrinhoLocalStorage();
    atualizarCarrinho();

    // Limpar formulário
    document.getElementById('formPedido').reset();
}

function mostrarNotificacao(mensagem) {
    // Toast simples (pode ser melhorado com biblioteca de toast)
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: #28a745;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    toast.textContent = mensagem;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// ===== Animações de Notificação =====
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
