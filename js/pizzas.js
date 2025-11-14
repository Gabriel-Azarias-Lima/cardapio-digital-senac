// Array de pizzas - será preenchido pela API
let pizzaJson = [];

/**
 * Busca tipos de bebidas do banco de dados
 */
async function buscarTiposBebidas(produtoId) {
    try {
        const response = await fetch(`api/produto_tipos.php?produto_id=${produtoId}`);
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            return result.data.map(tipo => tipo.nome);
        }
        return null;
    } catch (error) {
        console.error('Erro ao buscar tipos de bebidas:', error);
        return null;
    }
}

/**
 * Carrega produtos do banco de dados via API
 */
async function carregarProdutos() {
    try {
        const response = await fetch('api/produtos.php');
        const result = await response.json();
        
        if (result.success && result.data) {
            // Converter dados da API para o formato esperado pelo código
            const produtosPromises = result.data.map(async produto => {
                const isBebida = produto.categoria_slug === 'bebida';
                
                // Para bebidas, tentar buscar tipos do banco
                let sizes = ['6 fatias', '8 fatias', '12 fatias'];
                if (isBebida) {
                    const tiposDoBanco = await buscarTiposBebidas(produto.id);
                    
                    if (tiposDoBanco) {
                        sizes = tiposDoBanco;
                    } else {
                        // Tipos padrão baseados no nome do produto
                        if (produto.nome.toLowerCase().includes('suco')) {
                            sizes = ['Laranja', 'Limão', 'Melancia'];
                        } else {
                            sizes = ['Coca-Cola', 'Fanta', 'Sprite'];
                        }
                    }
                }
                
                return {
                    id: produto.id,
                    name: produto.nome,
                    img: produto.imagem_url,
                    // Para bebidas: mesmo preço em todos os tamanhos
                    // Para pizzas: pequena (80%), média (90%), grande (100%)
                    price: isBebida ? 
                        [parseFloat(produto.preco), parseFloat(produto.preco), parseFloat(produto.preco)] :
                        [
                            parseFloat((produto.preco * 0.8).toFixed(2)),
                            parseFloat((produto.preco * 0.9).toFixed(2)),
                            parseFloat(produto.preco)
                        ],
                    sizes: sizes,
                    description: produto.descricao,
                    categoria: produto.categoria_slug
                };
            });
            
            pizzaJson = await Promise.all(produtosPromises);
            
            console.log(`${pizzaJson.length} produtos carregados do banco de dados`);
            return true;
        } else {
            console.error('Erro ao carregar produtos:', result.error);
            // Carregar dados de fallback
            carregarDadosFallback();
            return false;
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        // Carregar dados de fallback
        carregarDadosFallback();
        return false;
    }
}

/**
 * Dados de fallback caso a API não esteja disponível
 */
function carregarDadosFallback() {
    pizzaJson = [
        {
            id: 1,
            name: 'Mussarela',
            img: 'images/pizza-mussarela.png',
            price: [20.00, 23.00, 25.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Molho de tomate, camada dupla de mussarela e orégano',
            categoria: 'tradicional'
        },
        {
            id: 2,
            name: 'Calabresa',
            img: 'images/pizza-calabresa.png',
            price: [21.00, 24.00, 26.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Molho de tomate, mussarela, calabresa fatiada, cebola e orégano',
            categoria: 'tradicional'
        },
        {
            id: 3,
            name: 'Quatro Queijos',
            img: 'images/pizza-quatro-queijos.png',
            price: [23.00, 26.00, 28.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Molho de tomate, camadas de mussarela, provolone, parmessão, gorgonzola e orégano',
            categoria: 'especial'
        },
        {
            id: 4,
            name: 'Brasileira',
            img: 'images/pizza-brasileira.png',
            price: [25.00, 28.00, 30.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Molho de tomate, mussarela, calabresa picada, palmito, champignon, azeitonas e orégano',
            categoria: 'especial'
        },
        {
            id: 5,
            name: 'Portuguesa',
            img: 'images/pizza-portuguesa.png',
            price: [24.00, 27.00, 29.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Molho de tomate, mussarela, presunto, ovos, cebolas, pimentão, azeitona  e orégano',
            categoria: 'tradicional'
        },
        {
            id: 6,
            name: 'Moda da Casa',
            img: 'images/pizza-moda-da-casa.png',
            price: [30.00, 33.00, 35.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Molho de tomate, mussarela, carne de sol, tomates em cubos, coentro, cebola, azeitona, catupiry e orégano',
            categoria: 'especial'
        },
        {
            id: 7,
            name: 'Banana com canela',
            img: 'images/pizza-banana-com-canela.png',
            price: [27.00, 30.00, 32.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Mussarela, banana, canela e açúcar',
            categoria: 'doce'
        },
        {
            id: 8,
            name: 'Chocolate com morango',
            img: 'images/pizza-chocolate-com-morango.png',
            price: [30.00, 32.00, 35.00],
            sizes: ['6 fatias', '8 fatias', '12 fatias'],
            description: 'Creme de leite, lascas de chocolate e morangos',
            categoria: 'doce'
        },
        {
            id: 9,
            name: 'Refrigerantes',
            img: 'images/refrigerante.png',
            price: [7.00, 7.00, 7.00],
            sizes: ['Coca-Cola', 'Fanta', 'Sprite'],
            description: 'Refrigerantes diversos em lata ou garrafa.',
            categoria: 'bebida'
        },
        {
            id: 10,
            name: 'Sucos Naturais',
            img: 'images/sucos.png',
            price: [8.00, 8.00, 8.00],
            sizes: ['Laranja', 'Limão', 'Morango'],
            description: 'Sucos naturais variados, preparados na hora.',
            categoria: 'bebida'
        }
    ];
    console.log('Usando dados de fallback');
}