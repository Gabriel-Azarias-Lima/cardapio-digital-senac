# 🍕 Pizzaria - Cardápio Digital

Sistema de cardápio digital responsivo com backend PHP e banco PostgreSQL (Supabase).

## 👥 Integrantes
- Gabriel Azarias de Lima — Backend e frontend
- Nome 2 — Papel
- Nome 3 — Papel

## 🚀 Funcionalidades
- **Interface responsiva** com Bootstrap 5
- **Catálogo dinâmico** carregado do banco de dados
- **Carrinho de compras** com persistência local
- **Formulário de pedidos** com validação
- **API REST** para produtos (`/api/produtos.php`)

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
└── api/
    ├── produtos.php    # Endpoint JSON de produtos
    ├── categorias.php  # Endpoint JSON de categorias
    └── produto_tipos.php # Endpoint de tipos/sabores
```

## ⚙️ Configuração

### 1. Banco de Dados
Execute o script SQL em `tabelas para cardapio.txt` no PostgreSQL.

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

## 🔧 Uso
1. Configure o banco e credenciais
2. Acesse `index.php` no navegador
3. Produtos são carregados automaticamente do banco
4. Use a API em `/api/produtos.php` para integração externa

## 📝 Notas
- Sistema tolerante a falhas (funciona mesmo sem banco)
- Validação de ambiente automática
- Logs de erro detalhados
