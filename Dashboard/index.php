<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PeçaAQ — Catálogo de Peças</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="stylesComprar.css">
</head>
<body>

  <!-- ══════════ HEADER ══════════ -->
  <header>
    <a href="../LandingPage/indexLandingPage.php" class="logo-area">
      <img src="../LandingPage/imgLandingPage/LogoPecaAq5.png" alt="Logo PeçaAQ">
      <span>PEÇA<em>AQ</em></span>
    </a>

    <nav>
      <ul>
        <li><a href="../LandingPage/indexLandingPage.php">Home</a></li>
        <li><a href="../Comprar/indexComprar.php" class="nav-active">Comprar</a></li>
        <li><a href="../Sobre/index.php">Sobre</a></li>
        <li><a href="#contato">Contato</a></li>
      </ul>
    </nav>

    <div class="header-actions">
      <button class="cart-btn" id="cartBtn" aria-label="Carrinho">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
      </button>
      <button class="btn-header" id="headerLoginBtn">Faça seu login</button>
    </div>

    <button class="nav__toggle" aria-label="Menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </header>

  <!-- ══════════ PAGE HERO ══════════ -->
  <section class="page-hero">
    <div class="page-hero__content">
      <p class="section-tag">Catálogo completo</p>
      <h1 class="page-hero__title">Encontre a <span>peça certa</span></h1>
      <p class="page-hero__sub">Mais de 5.000 peças automotivas com garantia, entrega rápida e preços competitivos.</p>
    </div>
    <div class="page-hero__search-wrap">
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Buscar peça, marca ou modelo..." autocomplete="off">
        <button class="search-clear" id="searchClear" aria-label="Limpar busca"><i class="fas fa-times"></i></button>
      </div>
    </div>
  </section>

  <!-- ══════════ MAIN CONTENT ══════════ -->
  <main class="catalog-main">

    <!-- ── SIDEBAR FILTROS ── -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar__header">
        <span><i class="fas fa-sliders-h"></i> Filtros</span>
        <button class="sidebar__clear" id="clearFilters">Limpar tudo</button>
      </div>

      <!-- Categoria -->
      <div class="filter-group">
        <button class="filter-group__toggle" aria-expanded="true">
          Categoria <i class="fas fa-chevron-down"></i>
        </button>
        <div class="filter-group__body">
          <label class="filter-check"><input type="checkbox" name="categoria" value="Motor"> <span>Motor</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Suspensão"> <span>Suspensão</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Freios"> <span>Freios</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Elétrica"> <span>Elétrica</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Transmissão"> <span>Transmissão</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Filtros"> <span>Filtros</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Ignição"> <span>Ignição</span></label>
          <label class="filter-check"><input type="checkbox" name="categoria" value="Arrefecimento"> <span>Arrefecimento</span></label>
        </div>
      </div>

      <!-- Marca -->
      <div class="filter-group">
        <button class="filter-group__toggle" aria-expanded="true">
          Marca <i class="fas fa-chevron-down"></i>
        </button>
        <div class="filter-group__body">
          <label class="filter-check"><input type="checkbox" name="marca" value="Monroe"> <span>Monroe</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="Moura"> <span>Moura</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="Gates"> <span>Gates</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="NGK"> <span>NGK</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="Bosch"> <span>Bosch</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="Cofap"> <span>Cofap</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="Fras-le"> <span>Fras-le</span></label>
          <label class="filter-check"><input type="checkbox" name="marca" value="Mahle"> <span>Mahle</span></label>
        </div>
      </div>

      <!-- Preço -->
      <div class="filter-group">
        <button class="filter-group__toggle" aria-expanded="true">
          Faixa de Preço <i class="fas fa-chevron-down"></i>
        </button>
        <div class="filter-group__body">
          <div class="price-range">
            <div class="price-inputs">
              <div class="price-input-wrap">
                <span>R$</span>
                <input type="number" id="priceMin" placeholder="0" min="0">
              </div>
              <span class="price-sep">—</span>
              <div class="price-input-wrap">
                <span>R$</span>
                <input type="number" id="priceMax" placeholder="2000" min="0">
              </div>
            </div>
            <input type="range" id="priceSlider" min="0" max="2000" value="2000" class="range-slider">
            <div class="price-labels">
              <span>R$ 0</span><span>R$ 2.000</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Disponibilidade -->
      <div class="filter-group">
        <button class="filter-group__toggle" aria-expanded="true">
          Disponibilidade <i class="fas fa-chevron-down"></i>
        </button>
        <div class="filter-group__body">
          <label class="filter-check"><input type="checkbox" name="disponibilidade" value="em_estoque" checked> <span>Em estoque</span></label>
          <label class="filter-check"><input type="checkbox" name="disponibilidade" value="pronta_entrega"> <span>Pronta entrega</span></label>
          <label class="filter-check"><input type="checkbox" name="disponibilidade" value="encomenda"> <span>Sob encomenda</span></label>
        </div>
      </div>

      <button class="btn-primary sidebar__apply" id="applyFilters">
        <i class="fas fa-filter"></i> Aplicar Filtros
      </button>
    </aside>

    <!-- ── PRODUTOS ── -->
    <div class="catalog-content">

      <!-- toolbar -->
      <div class="catalog-toolbar">
        <div class="catalog-toolbar__left">
          <span class="results-count" id="resultsCount">Carregando...</span>
          <button class="filter-toggle-mobile" id="filterToggleMobile">
            <i class="fas fa-sliders-h"></i> Filtros
          </button>
        </div>
        <div class="catalog-toolbar__right">
          <select id="sortSelect" class="sort-select">
            <option value="relevancia">Relevância</option>
            <option value="menor_preco">Menor preço</option>
            <option value="maior_preco">Maior preço</option>
            <option value="nome_az">Nome A-Z</option>
            <option value="nome_za">Nome Z-A</option>
          </select>
          <div class="view-toggle">
            <button class="view-btn active" id="gridViewBtn" aria-label="Grade"><i class="fas fa-th"></i></button>
            <button class="view-btn" id="listViewBtn" aria-label="Lista"><i class="fas fa-list"></i></button>
          </div>
        </div>
      </div>

      <!-- Active filters pills -->
      <div class="active-filters" id="activeFilters"></div>

      <!-- Grid de produtos -->
      <div class="products-grid" id="productsGrid">
        <!-- gerado pelo JS -->
      </div>

      <!-- Empty state -->
      <div class="empty-state" id="emptyState" style="display:none">
        <i class="fas fa-search"></i>
        <h3>Nenhuma peça encontrada</h3>
        <p>Tente ajustar os filtros ou a busca.</p>
        <button class="btn-primary" id="emptyReset">Limpar filtros</button>
      </div>

      <!-- Paginação -->
      <div class="pagination" id="pagination"></div>
    </div>

  </main>

  <!-- ══════════ CART DRAWER ══════════ -->
  <div class="cart-overlay" id="cartOverlay"></div>
  <aside class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer__header">
      <h2><i class="fas fa-shopping-cart"></i> Carrinho</h2>
      <button class="cart-drawer__close" id="cartClose"><i class="fas fa-times"></i></button>
    </div>
    <div class="cart-drawer__body" id="cartBody">
      <div class="cart-empty" id="cartEmpty">
        <i class="fas fa-box-open"></i>
        <p>Seu carrinho está vazio.</p>
      </div>
      <ul class="cart-items" id="cartItems"></ul>
    </div>
    <div class="cart-drawer__footer" id="cartFooter" style="display:none">
      <div class="cart-subtotal">
        <span>Subtotal</span>
        <strong id="cartSubtotal">R$ 0,00</strong>
      </div>
      <a href="../Comprar/checkout.php" class="btn-primary cart-checkout-btn">
        Finalizar Compra <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </aside>

  <!-- ══════════ MODAL PRODUTO ══════════ -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="product-modal" id="productModal">
      <button class="modal-close" id="modalClose"><i class="fas fa-times"></i></button>
      <div class="modal-img-wrap">
        <img id="modalImg" src="" alt="">
        <span class="modal-badge" id="modalBadge"></span>
      </div>
      <div class="modal-info">
        <p class="modal-category" id="modalCategory"></p>
        <h2 class="modal-title" id="modalTitle"></h2>
        <div class="modal-rating" id="modalRating"></div>
        <p class="modal-desc" id="modalDesc"></p>
        <div class="modal-meta">
          <span><i class="fas fa-tag"></i> <span id="modalMarca"></span></span>
          <span><i class="fas fa-truck"></i> Entrega rápida</span>
          <span><i class="fas fa-shield-halved"></i> Garantia 12 meses</span>
        </div>
        <div class="modal-price-row">
          <span class="modal-price" id="modalPrice"></span>
          <span class="modal-installment" id="modalInstallment"></span>
        </div>
        <div class="modal-qty">
          <button class="qty-btn" id="qtyMinus"><i class="fas fa-minus"></i></button>
          <span id="qtyValue">1</span>
          <button class="qty-btn" id="qtyPlus"><i class="fas fa-plus"></i></button>
        </div>
        <button class="btn-primary modal-add-btn" id="modalAddCart">
          <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
        </button>
      </div>
    </div>
  </div>

  <!-- ══════════ TOAST ══════════ -->
  <div class="toast" id="toast"></div>

  <!-- ══════════ FOOTER ══════════ -->
  <footer id="contato">
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="../LandingPage/imgLandingPage/LogoPecaAq5.png" alt="Logo">
        <p>PeçaAQ conecta compradores e fornecedores de peças automotivas com agilidade e segurança.</p>
        <div class="footer-socials">
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Navegação</h4>
        <ul>
          <li><a href="../LandingPage/indexLandingPage.php">Home</a></li>
          <li><a href="../Comprar/indexComprar.php">Comprar</a></li>
          <li><a href="../Sobre/index.php">Sobre</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Conta</h4>
        <ul>
          <li><a href="../login/indexLogin.php">Login</a></li>
          <li><a href="../Cadastrar/indexCadastro.php">Cadastro</a></li>
          <li><a href="../dashboard_cliente.php?tab=perfil">Meu Perfil</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contato</h4>
        <ul>
          <li><a href="#"><i class="fas fa-phone" style="color:var(--red);margin-right:6px"></i>(51) 9 9999-9999</a></li>
          <li><a href="#"><i class="fas fa-envelope" style="color:var(--red);margin-right:6px"></i>contato@pecaaq.com</a></li>
          <li><a href="#"><i class="fas fa-map-marker-alt" style="color:var(--red);margin-right:6px"></i>Porto Alegre, RS</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2025 PeçaAQ. Todos os direitos reservados.</span>
      <span>Feito com <span style="color:var(--red)">♥</span> por estudantes de TI</span>
    </div>
  </footer>

  <script src="appComprar.js"></script>
</body>
</html>