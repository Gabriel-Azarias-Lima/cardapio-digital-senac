# 🍕 Pizzaria - Cardápio Digital

Sistema de cardápio digital responsivo com backend PHP e banco PostgreSQL (Supabase).

## 👥 Integrantes
- Gabriel Azarias de Lima — Backend e frontend
- Nome 2 — Papel
- Nome 3 — Papel

## 🚀 Funcionalidades

### 🛒 Loja Virtual
- **Interface responsiva** com Bootstrap 5
- **Catálogo dinâmico** carregado do banco de dados (produtos, categorias e tipos)
- **Carrinho de compras** com persistência em `localStorage` (não usa tabela de carrinho)
- **Checkout completo** com dados do cliente, tipo de pedido, endereço detalhado e forma de pagamento
- **API REST** para produtos (`/api/produtos.php`) e tipos (`/api/produto_tipos.php`)
- **API de finalização de pedido** (`/api/finalizar_pedido.php`) salvando clientes, endereços, pedidos e itens

### 🎨 Painel Administrativo (NOVO!)
- **Dashboard moderno** com KPIs e gráficos interativos
- **Gestão de pedidos** com filtros e busca avançada
- **Design responsivo** com sidebar colapsável
- **Gradientes e animações** suaves
- **Cards informativos** com status coloridos
- **Gráficos Chart.js** (pedidos, faturamento, sabores)
- **Interface intuitiva** com ícones Bootstrap Icons
- **Tipografia moderna** com fonte Inter

## 🛠️ Tecnologias
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend:** PHP 8+ com PDO
- **Banco:** PostgreSQL (Supabase)
- **Arquitetura:** MVC com classes PHP

## 📁 Estrutura
```
senac-pizzaria/
├── index.php           # Página principal do cardápio
├── README.md           # Instruções do projeto
├── CSS/
│   └── style.css       # Estilos customizados
├── js/
│   ├── pizzas.js       # Carregamento de produtos e dados
│   └── sript.js        # Lógica do frontend (modal, carrinho, filtros)
├── images/             # Imagens do cardápio
├── config/
│   └── database.php    # Configurações do banco (PDO)
├── classes/
│   ├── Database.php    # Conexão PDO
│   └── Produto.php     # Modelo de produtos
├── api/
│   ├── produtos.php        # Endpoint JSON de produtos
│   ├── categorias.php      # Endpoint JSON de categorias
│   ├── produto_tipos.php   # Endpoint JSON de tipos/sabores
│   ├── pedidos.php         # Endpoint JSON de pedidos
│   └── finalizar_pedido.php # Endpoint para gravar pedidos completos
└── admin/              # 🎨 PAINEL ADMINISTRATIVO MODERNO
    ├── index.php       # Dashboard com KPIs e gráficos
    ├── pedidos.php     # Gestão de pedidos
    ├── README.md       # Documentação do admin
    ├── css/
    │   ├── admin.css   # Estilos modernos do painel
    │   └── pedidos.css # Estilos da página de pedidos
    └── js/
        ├── dashboard.js # Lógica do dashboard
        └── pedidos.js   # Lógica de pedidos
```

## ⚙️ Configuração

### 1. Banco de Dados
Execute o script SQL em `tabelas para cardapio.txt` no PostgreSQL.

As principais tabelas usadas pelo sistema são:
- `categorias`, `produtos`, `produto_tipos`
- `clientes`, `enderecos`
- `pedidos`, `pedido_itens`

### 2. Configuração PHP
Edite `config/database.php` com suas credenciais:
```php
define('DB_HOST', 'seu-host');
define('DB_USER', 'seu-usuario');
define('DB_PASS', 'sua-senha');
```

### 3. Extensões PHP
- `pdo_pgsql` (execute `diagnostico.php` para verificar)

### XAMPP (Windows): habilitar PostgreSQL
1. Abra o arquivo `C:\xampp\php\php.ini`.
2. Encontre e descomente as linhas (remova o `;` do início):
```ini
;extension=pdo_pgsql
;extension=pgsql
```
Fique assim:
```ini
extension=pdo_pgsql
extension=pgsql
```
3. Salve o arquivo e reinicie o Apache no XAMPP Control Panel (Stop e Start).
4. Teste no navegador: `http://localhost/cardapio/diagnostico.php`.

Resultado esperado:
```
✅ pdo_pgsql: Instalada
✅ Conexão estabelecida com sucesso!
```

## Uso

### Loja Virtual
1. Configure o banco e credenciais
2. Acesse `index.php` no navegador
3. Produtos são carregados automaticamente do banco
4. Monte o pedido pelo cardápio, finalize no modal de checkout e acompanhe os registros em `clientes`, `enderecos`, `pedidos` e `pedido_itens`
5. Use as APIs em `/api/produtos.php`, `/api/produto_tipos.php` e `/api/finalizar_pedido.php` para integrações externas

**Acesso rápido:**
- Loja / Cardápio: `http://localhost/cardapio-digital-senac-main/`

### Painel Administrativo
1. Acesse `admin/index.php` para o dashboard
2. Visualize KPIs e gráficos em tempo real
3. Acesse `admin/pedidos.php` para gerenciar pedidos
4. Use filtros para refinar dados (período, status, busca)
5. Navegue pela sidebar para acessar diferentes seções

**Acesso rápido:**
- Dashboard Admin: `http://localhost/cardapio-digital-senac-main/admin/`

## Notas
- Sistema tolerante a falhas (funciona mesmo sem banco)
- Validação de ambiente automática
- Logs de erro detalhados
