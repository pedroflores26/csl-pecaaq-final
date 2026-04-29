# 🚗 PeçaAQ — Marketplace de Peças Automotivas

## 📌 Sobre o Projeto

O **PeçaAQ** é uma plataforma web desenvolvida para conectar compradores e fornecedores de peças automotivas de forma rápida, segura e organizada.

O sistema permite que clientes encontrem peças com facilidade, comparem fornecedores, visualizem lojas parceiras, realizem compras e acompanhem seus pedidos, enquanto empresas podem cadastrar seus produtos, gerenciar estoque e acompanhar vendas através de um painel administrativo.

O projeto foi desenvolvido com foco acadêmico e profissional, buscando aplicar conceitos reais de desenvolvimento web full stack com integração entre front-end, back-end e banco de dados.

---

## 🎯 Objetivo

Criar um marketplace automotivo moderno que facilite a busca e comercialização de peças automtivas, oferecendo:

* Catálogo completo de produtos
* Sistema de lojas parceiras
* Dashboard para empresas
* Área do cliente
* Carrinho de compras
* Sistema de avaliações
* Controle administrativo
* Gestão de pedidos
* Login e cadastro com múltiplos perfis

---

## 👥 Tipos de Usuários

### 👤 Cliente

O cliente pode:

* Criar conta
* Fazer login
* Buscar produtos
* Filtrar por categoria, marca e preço
* Adicionar itens ao carrinho
* Finalizar compras
* Avaliar produtos
* Visualizar lojas parceiras
* Acompanhar pedidos
* Editar perfil

---

### 🏢 Empresa

A empresa pode:

* Criar conta empresarial
* Cadastrar CNPJ
* Fazer login
* Acessar dashboard exclusivo
* Cadastrar produtos
* Editar produtos
* Gerenciar estoque
* Visualizar vendas
* Acompanhar pedidos
* Monitorar avaliações

---

### 👑 Administrador

O administrador pode:

* Aprovar empresas
* Gerenciar usuários
* Gerenciar produtos
* Controlar pedidos
* Acompanhar estatísticas
* Acessar o GodMode/Admin Panel

---

## 🛠 Tecnologias Utilizadas

### Front-End

* HTML5
* CSS3
* JavaScript (Vanilla JS)
* Font Awesome
* Google Fonts
* Layout Responsivo
* Dark Theme UI

### Back-End

* PHP
* PHP Sessions
* LocalStorage
* API interna com JSON

### Banco de Dados

* MySQL
* phpMyAdmin
* XAMPP

### Ambiente

* Visual Studio Code
* XAMPP Control Panel
* Apache
* MySQL Server

---

## 🗂 Estrutura do Projeto

```bash
PeçaAQ/
│
├── LandingPage/
├── Comprar/
├── lojas/
├── Sobre/
├── login/
├── Cadastrar/
├── Dashboard/
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── layout.css
│   └── logo.png
│
├── dashboard_cliente.php
├── dashboard_empresa.php
├── index.php
├── conexao.php
└── README.md
```

---

## 🛒 Funcionalidades Principais

## Catálogo de Produtos

Sistema completo de exibição de peças com:

* Cards de produtos
* Imagem dinâmica vinda do banco
* Preço promocional
* Filtros inteligentes
* Modal de detalhes
* Paginação
* Ordenação por preço e relevância
* Busca por nome/categoria/marca

---

## 🏪 Lojas Parceiras

Página exclusiva com:

* Lista de empresas aprovadas
* Avaliação média
* Total de vendas
* Estatísticas gerais
* Página individual de cada loja
* Botão “Ver Loja”

---

## ⭐ Sistema de Avaliações

Cada produto possui:

* Nota de 1 a 5 estrelas
* Título da avaliação
* Comentário
* Compra verificada
* Likes úteis/não úteis
* Histórico por usuário

---

## 🛍 Carrinho de Compras

Funcionalidades:

* Adicionar ao carrinho
* Controle de quantidade
* Subtotal automático
* Persistência com LocalStorage
* Finalização de compra

---

## 🔐 Sistema de Login

Login inteligente por perfil:

### Cliente

* Login por e-mail

### Empresa

* Login por CNPJ

### Admin

* Painel administrativo exclusivo

Com:

* Session PHP
* LocalStorage
* Redirecionamento automático
* Dashboard correspondente por tipo

---

## 🗃 Banco de Dados

Principais tabelas:

* usuarios
* empresas
* produtos
* pedidos
* pedido_itens
* avaliacoes
* categorias
* marcas
* enderecos

Relacionamentos completos com Foreign Keys e controle de integridade.

---

## 🚀 Como Rodar o Projeto

## 1. Instalar o XAMPP

Baixe e instale:

* Apache
* MySQL
* phpMyAdmin

---

## 2. Clonar ou copiar o projeto

Coloque a pasta dentro de:

```bash
C:/xampp/htdocs/
```

Exemplo:

```bash
C:/xampp/htdocs/PecaAQ/
```

---

## 3. Importar o banco

Abra:

```bash
http://localhost/phpmyadmin
```

Importe o arquivo SQL do projeto.

Banco:

```sql
pecaaq
```

---

## 4. Iniciar serviços

No XAMPP:

* Start Apache
* Start MySQL

---

## 5. Executar

Abra no navegador:

```bash
http://localhost/PecaAQ/
```

---

## 🎨 Design do Projeto

O visual foi desenvolvido com foco em:

* Estética automotiva premium
* Visual moderno e profissional
* Interface escura (dark mode)
* Destaques em vermelho
* Layout limpo
* Responsividade
* Experiência intuitiva

Inspirado em marketplaces premium e plataformas automotivas modernas.

---

## 📈 Melhorias Futuras

Possíveis evoluções:

* Sistema de pagamento online
* Integração com Mercado Pago / Stripe
* Chat entre cliente e loja
* Favoritos
* Cupom de desconto
* Rastreamento de pedidos
* Sistema de frete automático
* Dashboard analítico avançado
* Notificações em tempo real
* Aplicativo mobile

---

## 👨‍💻 Desenvolvido por

Projeto desenvolvido por estudantes de Tecnologia da Informação com foco em desenvolvimento Full Stack, UX/UI e boas práticas de engenharia de software.

Feito com dedicação, café e muito debug ☕🔥
integrantes:
Pedro Flores
Joaquim Barbosa
Gabriel Bandasz
Gabriel Sandes
Lucas Matheus
---

# © PeçaAQ

Todos os direitos reservados.
