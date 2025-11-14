let modalKey = 0

// variavel para controlar a quantidade inicial de pizzas na modal
let quantPizzas = 1

let cart = [] // carrinho

// funcoes auxiliares ou uteis
const seleciona = (elemento) => document.querySelector(elemento)
const selecionaTodos = (elemento) => document.querySelectorAll(elemento)

const formatoReal = (valor) => {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

const formatoMonetario = (valor) => {
    if(valor) {
        return valor.toFixed(2)
    }
}

const abrirModal = () => {
    seleciona('.pizzaWindowArea').style.opacity = 0 // transparente
    seleciona('.pizzaWindowArea').style.display = 'flex'
    setTimeout(() => seleciona('.pizzaWindowArea').style.opacity = 1, 150)
}

const fecharModal = () => {
    seleciona('.pizzaWindowArea').style.opacity = 0 // transparente
    setTimeout(() => seleciona('.pizzaWindowArea').style.display = 'none', 500)
}

const botoesFechar = () => {
    // BOTOES FECHAR MODAL
    selecionaTodos('.pizzaInfo--cancelButton, .pizzaInfo--cancelMobileButton').forEach( (item) => item.addEventListener('click', fecharModal) )
}

const preencheDadosDasPizzas = (pizzaItem, item, index) => {
    // setar um atributo para identificar qual elemento foi clicado
	pizzaItem.setAttribute('data-key', index)
    pizzaItem.querySelector('.pizza-item--img img').src = item.img
    pizzaItem.querySelector('.pizza-item--content .pizza-item--price').innerHTML = formatoReal(item.price[2])
    pizzaItem.querySelector('.pizza-item--content .pizza-item--name').innerHTML = item.name
    pizzaItem.querySelector('.pizza-item--content .pizza-item--desc').innerHTML = item.description
    
    // Adicionar badge "Popular" para algumas pizzas
    if(['Mussarela', 'Calabresa', 'Quatro Queijos'].includes(item.name)) {
        const badge = document.createElement('div')
        badge.className = 'pizza-item--badge'
        badge.innerHTML = 'Popular'
        pizzaItem.querySelector('.pizza-item--img').appendChild(badge)
    }
}

const preencheDadosModal = (item) => {
    seleciona('.pizzaBig img').src = item.img
    seleciona('.pizzaInfo h1').innerHTML = item.name
    seleciona('.pizzaInfo--desc').innerHTML = item.description
    seleciona('.pizzaInfo--actualPrice').innerHTML = formatoReal(item.price[2])
    
    // Ajustar label do setor de tamanho/tipo
    const isBebida = item.categoria === 'bebida'
    seleciona('.pizzaInfo--sector').innerHTML = isBebida ? 'Tipo' : 'Tamanho'
}

const pegarKey = (e) => {
    // .closest retorna o elemento mais proximo que tem a class que passamos
    // do .pizza-item ele vai pegar o valor do atributo data-key
    let key = e.target.closest('.pizza-item').getAttribute('data-key')
    console.log('Pizza clicada ' + key)
    console.log(pizzaJson[key])

    // garantir que a quantidade inicial de pizzas é 1
    quantPizzas = 1

    // Para manter a informação de qual pizza foi clicada
    modalKey = key

    return key
}

const preencherTamanhos = (key) => {
    // tirar a selecao de tamanho atual e selecionar o tamanho grande
    seleciona('.pizzaInfo--size.selected').classList.remove('selected')
    
    const item = pizzaJson[key]
    const isBebida = item.categoria === 'bebida'

    // selecionar todos os tamanhos
    selecionaTodos('.pizzaInfo--size').forEach((size, sizeIndex) => {
        // selecionar o primeiro item (índice 0) por padrão
        (sizeIndex === 0) ? size.classList.add('selected') : size.classList.remove('selected')
        
        // Para bebidas, mostrar apenas o nome do tipo (sem span)
        // Para pizzas, mostrar tamanho + fatias
        if (isBebida) {
            size.innerHTML = item.sizes[sizeIndex]
        } else {
            const tamanhoLabel = sizeIndex === 0 ? 'PEQUENA' : (sizeIndex === 1 ? 'MÉDIA' : 'GRANDE')
            size.innerHTML = `${tamanhoLabel} <span>${item.sizes[sizeIndex]}</span>`
        }
    })
}

// Retorna o índice do tamanho selecionado (0=P, 1=M, 2=G)
const getSelectedSizeIndex = () => {
    const sizes = selecionaTodos('.pizzaInfo--size')
    let idx = 2
    sizes.forEach((el, i) => {
        if(el.classList.contains('selected')) idx = i
    })
    return idx
}

// Atualiza o preço exibido multiplicando pelo total selecionado
const atualizarPrecoTotal = () => {
    const idx = getSelectedSizeIndex()
    const base = pizzaJson[modalKey].price[idx]
    seleciona('.pizzaInfo--actualPrice').innerHTML = formatoReal(base * quantPizzas)
}

const escolherTamanhoPreco = (key) => {
    // Ações nos botões de tamanho
    // selecionar todos os tamanhos
    selecionaTodos('.pizzaInfo--size').forEach((size, sizeIndex) => {
        size.addEventListener('click', (e) => {
            // clicou em um item, tirar a selecao dos outros e marca o q vc clicou
            // tirar a selecao de tamanho atual e selecionar o tamanho grande
            seleciona('.pizzaInfo--size.selected').classList.remove('selected')
            // marcar o que vc clicou, ao inves de usar e.target use size, pois ele é nosso item dentro do loop
            size.classList.add('selected')

            // mudar o preço de acordo com o tamanho e quantidade
            atualizarPrecoTotal()
        })
    })
}

const mudarQuantidade = () => {
    // Ações nos botões + e - da janela modal
    seleciona('.pizzaInfo--qtmais').addEventListener('click', () => {
        quantPizzas++
        seleciona('.pizzaInfo--qt').innerHTML = quantPizzas
        atualizarPrecoTotal()
    })

    seleciona('.pizzaInfo--qtmenos').addEventListener('click', () => {
        if(quantPizzas > 1) {
            quantPizzas--
            seleciona('.pizzaInfo--qt').innerHTML = quantPizzas 	
            atualizarPrecoTotal()
        }
    })
}

const adicionarNoCarrinho = () => {
    seleciona('.pizzaInfo--addButton').addEventListener('click', () => {
        console.log('Adicionar no carrinho')

        // pegar dados da janela modal atual
    	// qual pizza? pegue o modalKey para usar pizzaJson[modalKey]
    	console.log("Pizza " + modalKey)
    // Obter o índice do tamanho selecionado
    const sizeIndex = getSelectedSizeIndex()
    // Obter o nome do sabor/tamanho
    const sizeName = pizzaJson[modalKey].sizes[sizeIndex]
    
    console.log("Tamanho: ", sizeName)
    console.log("Quantidade: ", quantPizzas)
    
    // Preço unitário
    let price = pizzaJson[modalKey].price[sizeIndex]
    
    // Criar identificador único para o item no carrinho
    let identificador = `${pizzaJson[modalKey].id}_${sizeName}`.toLowerCase().replace(/\s+/g, '_')

        // antes de adicionar verifique se ja tem aquele codigo e tamanho
        // para adicionarmos a quantidade
        let key = cart.findIndex( (item) => item.identificador == identificador )
        console.log(key)

        if(key > -1) {
            // se encontrar aumente a quantidade
            cart[key].qt += quantPizzas
        } else {
            // adicionar objeto pizza no carrinho
            let pizza = {
                identificador: identificador,
                id: pizzaJson[modalKey].id,
                size: sizeName,
                qt: quantPizzas,
                price: parseFloat(price)
            }
            cart.push(pizza)
            console.log(pizza)
            console.log('Sub total R$ ' + (pizza.qt * pizza.price).toFixed(2))
        }

        fecharModal()
        abrirCarrinho()
        atualizarCarrinho()
    })
}

const abrirCarrinho = () => {
    console.log('Qtd de itens no carrinho ' + cart.length)
    if(cart.length > 0) {
        // mostrar o carrinho
	    seleciona('aside').classList.add('show')
    }

    // exibir aside do carrinho no modo mobile
    seleciona('.menu-openner').addEventListener('click', () => {
        if(cart.length > 0) {
            seleciona('aside').classList.add('show')
        }
    })
}

const fecharCarrinho = () => {
    // fechar o carrinho com o botão X no modo mobile
    seleciona('.btn-close-custom').addEventListener('click', () => {
        seleciona('aside').classList.remove('show')
    })
}

const atualizarCarrinho = () => {
    // exibir número de itens no carrinho
	seleciona('.cart-badge').innerHTML = cart.length
	
	// mostrar ou nao o carrinho
	if(cart.length > 0) {

		// mostrar o carrinho
		seleciona('aside').classList.add('show')

		// zerar meu .cart para nao fazer insercoes duplicadas
		seleciona('.cart').innerHTML = ''

        // crie as variaveis antes do for
		let subtotal = 0
		let desconto = 0
		let total    = 0

        // para preencher os itens do carrinho, calcular subtotal
		for(let i in cart) {
			// use o find para pegar o item por id
			let pizzaItem = pizzaJson.find( (item) => item.id == cart[i].id )
			console.log(pizzaItem)

        			    // em cada item pegar o subtotal
         	subtotal += cart[i].price * cart[i].qt

			// fazer o clone, exibir na tela e depois preencher as informações
			let cartItem = seleciona('.models .cart--item').cloneNode(true)
			seleciona('.cart').append(cartItem)

			// Obter o nome do tamanho/sabor
			let sizeDisplay = cart[i].size
			
			// Se for uma bebida, mostrar apenas o nome do sabor
			// Se for pizza, mostrar o tamanho (P, M, G)
			let pizzaName = pizzaItem.name
			if (pizzaItem.categoria === 'bebida') {
			    // Para bebidas, o tamanho já é o nome do sabor (ex: 'Coca-Cola')
			    pizzaName = `${pizzaItem.name} - ${sizeDisplay}`
			} else {
			    // Para pizzas, mostrar o tamanho (P, M, G)
			    const tamanhoLetra = sizeDisplay === '0' ? 'P' : (sizeDisplay === '1' ? 'M' : 'G')
			    pizzaName = `${pizzaItem.name} (${tamanhoLetra})`
			}

			// preencher as informacoes
			cartItem.querySelector('img').src = pizzaItem.img
			cartItem.querySelector('.cart--item-nome').innerHTML = pizzaName
			cartItem.querySelector('.cart--item--qt').innerHTML = cart[i].qt

			// selecionar botoes + e -
			cartItem.querySelector('.cart--item-qtmais').addEventListener('click', () => {
				console.log('Clicou no botão mais')
				// adicionar apenas a quantidade que esta neste contexto
				cart[i].qt++
				// atualizar a quantidade
				atualizarCarrinho()
			})

			cartItem.querySelector('.cart--item-qtmenos').addEventListener('click', () => {
				console.log('Clicou no botão menos')
				if(cart[i].qt > 1) {
					// subtrair apenas a quantidade que esta neste contexto
					cart[i].qt--
				} else {
					// remover se for zero
					cart.splice(i, 1)
				}

                (cart.length < 1) ? seleciona('header').style.display = 'flex' : ''

				// atualizar a quantidade
				atualizarCarrinho()
			})

			seleciona('.cart').append(cartItem)

		} // fim do for

		// fora do for
		// calcule desconto 10% e total
		//desconto = subtotal * 0.1
		desconto = subtotal * 0
		total = subtotal - desconto

		// exibir na tela os resultados
		// selecionar o ultimo span do elemento
		seleciona('.subtotal span:last-child').innerHTML = formatoReal(subtotal)
		seleciona('.desconto span:last-child').innerHTML = formatoReal(desconto)
		seleciona('.total span:last-child').innerHTML    = formatoReal(total)

	} else {
		// ocultar o carrinho
		seleciona('aside').classList.remove('show')
	}
}

const finalizarCompra = () => {
    seleciona('.cart--finalizar').addEventListener('click', () => {
        console.log('Finalizar compra')
        
        // Verificar se há itens no carrinho
        if(cart.length === 0) {
            alert('Seu carrinho está vazio!')
            return
        }
        
        // Fechar carrinho
        seleciona('aside').classList.remove('show')
        
        // Abrir modal de checkout
        abrirModalCheckout()
    })
}

// ========== FUNCIONALIDADES DO CHECKOUT ==========

const abrirModalCheckout = () => {
    // Preencher resumo do pedido
    preencherResumoCheckout()
    
    // Abrir modal usando Bootstrap
    const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'))
    checkoutModal.show()
}

const preencherResumoCheckout = () => {
    const resumoDiv = seleciona('#resumoPedido')
    resumoDiv.innerHTML = ''
    
    let total = 0
    
    // Listar todos os itens do carrinho
    cart.forEach(item => {
        const pizzaItem = pizzaJson.find(p => p.id === item.id)
        const subtotal = item.price * item.qt
        total += subtotal
        
        resumoDiv.innerHTML += `
            <div class="d-flex justify-content-between mb-2">
                <span>${item.qt}x ${pizzaItem.name} (${item.size})</span>
                <span class="fw-bold">${formatoReal(subtotal)}</span>
            </div>
        `
    })
    
    // Atualizar total
    seleciona('#totalCheckout').innerHTML = formatoReal(total)
}

// Controlar exibição do campo de endereço
document.querySelectorAll('input[name="tipoPedido"]').forEach(radio => {
    radio.addEventListener('change', (e) => {
        const enderecoArea = seleciona('#enderecoArea')
        if(e.target.value === 'entrega') {
            enderecoArea.style.display = 'block'
            seleciona('#endereco').required = true
        } else {
            enderecoArea.style.display = 'none'
            seleciona('#endereco').required = false
        }
    })
})

// Controlar exibição do campo de troco
seleciona('#metodoPagamento').addEventListener('change', (e) => {
    const trocoArea = seleciona('#trocoArea')
    if(e.target.value === 'dinheiro') {
        trocoArea.style.display = 'block'
        seleciona('#troco').required = true
    } else {
        trocoArea.style.display = 'none'
        seleciona('#troco').required = false
    }
})

// Confirmar pedido
seleciona('#confirmarPedido').addEventListener('click', () => {
    // Validar campos obrigatórios
    const nome = seleciona('#nomeCompleto').value.trim()
    const telefone = seleciona('#telefone').value.trim()
    const tipoPedido = document.querySelector('input[name="tipoPedido"]:checked').value
    const metodoPagamento = seleciona('#metodoPagamento').value
    
    if(!nome) {
        alert('Por favor, preencha seu nome completo!')
        return
    }
    
    if(!telefone) {
        alert('Por favor, preencha seu telefone!')
        return
    }
    
    if(!metodoPagamento) {
        alert('Por favor, selecione um método de pagamento!')
        return
    }
    
    // Validar endereço se for entrega
    if(tipoPedido === 'entrega') {
        const endereco = seleciona('#endereco').value.trim()
        if(!endereco) {
            alert('Por favor, preencha o endereço de entrega!')
            return
        }
    }
    
    // Validar troco se for dinheiro
    if(metodoPagamento === 'dinheiro') {
        const troco = parseFloat(seleciona('#troco').value)
        const total = calcularTotal()
        if(!troco || troco < total) {
            alert('O valor do troco deve ser maior ou igual ao total do pedido!')
            return
        }
    }
    
    // Calcular tempo estimado baseado na quantidade de pizzas
    const totalPizzas = cart.reduce((sum, item) => sum + item.qt, 0)
    const tempoBase = 30 // minutos base
    const tempoPorPizza = 5 // minutos adicionais por pizza
    const tempoEstimado = tempoBase + (totalPizzas * tempoPorPizza)
    
    // Atualizar tempo estimado no modal de confirmação
    seleciona('#tempoEstimado').innerHTML = `${tempoEstimado} minutos`
    
    // Fechar modal de checkout
    const checkoutModal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'))
    checkoutModal.hide()
    
    // Abrir modal de confirmação
    setTimeout(() => {
        const confirmacaoModal = new bootstrap.Modal(document.getElementById('confirmacaoModal'))
        confirmacaoModal.show()
        
        // Limpar carrinho e formulário
        cart = []
        atualizarCarrinho()
        limparFormularioCheckout()
    }, 300)
})

const calcularTotal = () => {
    return cart.reduce((total, item) => total + (item.price * item.qt), 0)
}

const limparFormularioCheckout = () => {
    seleciona('#checkoutForm').reset()
    seleciona('#enderecoArea').style.display = 'none'
    seleciona('#trocoArea').style.display = 'none'
}

// ========== FUNCIONALIDADES DE PESQUISA E CATEGORIAS ==========

const initializeSearch = () => {
    const searchInput = seleciona('#searchInput')
    if(searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase()
            filterPizzas(searchTerm)
        })
    }
}

const initializeCategories = () => {
    const categoryBtns = selecionaTodos('.category-btn')
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remover classe active de todos os botões
            categoryBtns.forEach(b => b.classList.remove('active'))
            // Adicionar classe active ao botão clicado
            btn.classList.add('active')

            const category = btn.getAttribute('data-category')
            filterByCategory(category)
        })
    })
}

const filterPizzas = (searchTerm) => {
    const pizzaItems = selecionaTodos('.pizza-item')
    pizzaItems.forEach(item => {
        const pizzaName = item.querySelector('.pizza-item--content .pizza-item--name').textContent.toLowerCase()
        const pizzaDesc = item.querySelector('.pizza-item--content .pizza-item--desc').textContent.toLowerCase()
        
        if(pizzaName.includes(searchTerm) || pizzaDesc.includes(searchTerm)) {
            item.style.display = 'block'
        } else {
            item.style.display = 'none'
        }
    })
}

const filterByCategory = (category) => {
    const pizzaItems = selecionaTodos('.pizza-item')
    
    if(category === 'all') {
        pizzaItems.forEach(item => item.style.display = 'block')
        return
    }
    
    pizzaItems.forEach(item => {
        const key = item.getAttribute('data-key')

        if (key === null) {
            item.style.display = 'none'
            return
        }

        const pizza = pizzaJson[key]

        if (!pizza || !pizza.categoria) {
            item.style.display = 'none'
            return
        }

        item.style.display = (pizza.categoria === category) ? 'block' : 'none'
    })
}

// Define a ordem de exibição das pizzas
const getPizzaOrderGroup = (pizza) => {
    const populares = ['Mussarela', 'Calabresa', 'Quatro Queijos']

    if (populares.includes(pizza.name)) return 0 // Populares

    if (pizza.categoria === 'tradicional' || pizza.categoria === 'especial') {
        return 1 // Pizzas salgadas "normais"
    }

    if (pizza.categoria === 'doce') return 2 // Doces

    if (pizza.categoria === 'bebida') return 3 // Bebidas

    return 4 // Qualquer outra categoria
}

/**
 * Renderiza as pizzas na tela
 */
const renderizarPizzas = () => {
    // Limpar área de pizzas
    seleciona('.pizza-area').innerHTML = ''
    
    // Ordenar índices de pizzas conforme regras de exibição
    const pizzasOrdenadas = pizzaJson
        .map((item, index) => ({ item, index }))
        .sort((a, b) => {
            const grupoA = getPizzaOrderGroup(a.item)
            const grupoB = getPizzaOrderGroup(b.item)

            if (grupoA !== grupoB) {
                return grupoA - grupoB
            }

            // Dentro do mesmo grupo, ordenar alfabeticamente pelo nome
            return a.item.name.localeCompare(b.item.name, 'pt-BR')
        })

    // MAPEAR pizzas ordenadas para gerar lista de pizzas
    pizzasOrdenadas.forEach(({ item, index }) => {
        //console.log(item)
        let pizzaItem = document.querySelector('.models .pizza-item').cloneNode(true)
        //console.log(pizzaItem)
        //document.querySelector('.pizza-area').append(pizzaItem)
        seleciona('.pizza-area').append(pizzaItem)

        // preencher os dados de cada pizza
        preencheDadosDasPizzas(pizzaItem, item, index)
        
        // pizza clicada
        pizzaItem.querySelector('.pizza-item a').addEventListener('click', (e) => {
            e.preventDefault()
            console.log('Clicou na pizza')

            let chave = pegarKey(e)

            // abrir janela modal
            abrirModal()

            // preenchimento dos dados
            preencheDadosModal(item)

            // pegar tamanho selecionado
            preencherTamanhos(chave)

            // definir quantidade inicial como 1
            seleciona('.pizzaInfo--qt').innerHTML = quantPizzas

            // selecionar o tamanho e preco com o clique no botao
            escolherTamanhoPreco(chave)

        })

        botoesFechar()

    }) // fim do MAPEAR pizzaJson para gerar lista de pizzas
}

/**
 * Inicializa a aplicação
 */
async function inicializarApp() {
    // Mostrar loading
    seleciona('.pizza-area').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-3 text-muted">Carregando produtos...</p></div>'
    
    // Carregar produtos do banco de dados
    await carregarProdutos()
    
    // Renderizar pizzas
    renderizarPizzas()
    
    // Inicializar funcionalidades
    mudarQuantidade()
    adicionarNoCarrinho()
    atualizarCarrinho()
    fecharCarrinho()
    finalizarCompra()
    initializeSearch()
    initializeCategories()
    
    console.log('Aplicação inicializada com sucesso!')
}

// Inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarApp)
} else {
    inicializarApp()
}