<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PeçaAQ — Sua Peça Aqui</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root {
      --red:     #e8192c;
      --red-dk:  #b01020;
      --dark:    #0a0a0a;
      --dark2:   #111111;
      --dark3:   #1a1a1a;
      --border:  rgba(255,255,255,0.07);
      --text:    #ffffff;
      --muted:   #999999;
      --header-h: 72px;
    }

    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior: smooth; }

    body {
      background: var(--dark);
      color: var(--text);
      font-family: 'Barlow', sans-serif;
      overflow-x: hidden;
    }

    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: var(--dark); }
    ::-webkit-scrollbar-thumb { background: var(--red); border-radius: 2px; }

    /* ── HEADER ── */
    header {
      position: fixed;
      inset: 0 0 auto 0;
      height: var(--header-h);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 60px;
      background: rgba(10,10,10,0.94);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border);
      z-index: 999;
      animation: fadeDown 0.7s ease;
    }

    .logo-area {
      display: flex; align-items: center; gap: 12px; text-decoration: none;
    }
    .logo-area img { width: 44px; height: 44px; object-fit: contain; }
    .logo-area span {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.5rem; font-weight: 800; letter-spacing: 3px; color: var(--text);
    }
    .logo-area span em { color: var(--red); font-style: normal; }

    nav ul { display: flex; list-style: none; gap: 38px; }
    nav a {
      color: #ccc; text-decoration: none; font-size: 0.88rem;
      font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase;
      position: relative; transition: color 0.25s;
    }
    nav a::after {
      content: ''; position: absolute; left: 0; bottom: -4px;
      width: 0; height: 2px; background: var(--red); transition: width 0.3s;
    }
    nav a:hover { color: #fff; }
    nav a:hover::after { width: 100%; }

    .btn-header {
      background: var(--red); color: #fff; border: none;
      padding: 10px 22px; font-family: 'Barlow', sans-serif;
      font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;
      text-transform: uppercase; border-radius: 4px; cursor: pointer;
      transition: background 0.25s, transform 0.2s, box-shadow 0.25s;
    }
    .btn-header:hover {
      background: var(--red-dk); transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(232,25,44,0.4);
    }

    /* ── HERO ── */
    .hero {
      position: relative; min-height: 100vh;
      display: flex; align-items: center; padding: 0 60px; overflow: hidden;
    }
    .hero__bg {
      position: absolute; inset: 0;
      background: url("imgLandingPage/Landingpage.jpg") center/cover no-repeat;
      animation: zoomBg 22s ease-in-out infinite alternate;
    }
    .hero__bg::after {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(105deg, rgba(0,0,0,0.9) 38%, rgba(0,0,0,0.2) 100%);
    }
    .hero__content {
      position: relative; z-index: 2;
      max-width: 600px; animation: fadeUp 1s ease 0.3s both;
    }
    .hero__tag {
      display: inline-block; font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.85rem; font-weight: 600; letter-spacing: 4px;
      text-transform: uppercase; color: var(--red); margin-bottom: 18px;
    }
    .hero__title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(3rem, 6vw, 5.5rem); font-weight: 800;
      line-height: 1; text-transform: uppercase; margin-bottom: 24px; letter-spacing: 2px;
    }
    .hero__title span { color: var(--red); }
    .hero__sub {
      font-size: 1.05rem; color: #bbb; line-height: 1.7;
      margin-bottom: 14px; max-width: 480px;
    }
    .hero__phone {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.25rem; font-weight: 600; color: #ddd;
      margin-bottom: 36px; letter-spacing: 1px;
    }
    .hero__phone i { color: var(--red); margin-right: 8px; }
    .hero__btns { display: flex; gap: 16px; flex-wrap: wrap; }

    .btn-primary {
      background: var(--red); color: #fff; padding: 14px 34px;
      border: none; border-radius: 4px; font-family: 'Barlow', sans-serif;
      font-size: 0.9rem; font-weight: 600; text-transform: uppercase;
      letter-spacing: 1px; cursor: pointer;
      transition: background 0.25s, transform 0.2s, box-shadow 0.3s;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary:hover {
      background: var(--red-dk); transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(232,25,44,0.35);
    }
    .btn-outline {
      background: transparent; color: #fff; padding: 14px 34px;
      border: 1px solid rgba(255,255,255,0.3); border-radius: 4px;
      font-family: 'Barlow', sans-serif; font-size: 0.9rem; font-weight: 600;
      text-transform: uppercase; letter-spacing: 1px; cursor: pointer;
      transition: all 0.25s; text-decoration: none;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-outline:hover { border-color: var(--red); color: var(--red); transform: translateY(-3px); }

    .hero__scroll {
      position: absolute; bottom: 36px; left: 50%; transform: translateX(-50%);
      display: flex; flex-direction: column; align-items: center; gap: 8px;
      color: #555; font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase;
      animation: bounce 2s infinite;
    }

    /* ── STATS BAR ── */
    .stats-bar {
      background: var(--red);
      display: grid; grid-template-columns: repeat(4, 1fr);
    }
    .stat {
      padding: 28px 20px; text-align: center;
      border-right: 1px solid rgba(255,255,255,0.15);
    }
    .stat:last-child { border-right: none; }
    .stat__num {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.2rem; font-weight: 800; display: block; line-height: 1;
    }
    .stat__label {
      font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px;
      opacity: 0.85; margin-top: 4px;
    }

    /* ── SECTION BASE ── */
    section { padding: 90px 60px; }
    .section-tag {
      font-family: 'Barlow Condensed', sans-serif; font-size: 0.8rem;
      font-weight: 600; letter-spacing: 4px; text-transform: uppercase;
      color: var(--red); margin-bottom: 10px;
    }
    .section-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800;
      text-transform: uppercase; line-height: 1.05;
      margin-bottom: 50px; letter-spacing: 1px;
    }

    /* ── SERVICES ── */
    .services { background: var(--dark2); }
    .services-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2px;
    }
    .service-card {
      background: var(--dark3); padding: 40px 32px;
      position: relative; overflow: hidden; transition: background 0.3s;
    }
    .service-card::before {
      content: ''; position: absolute; left: 0; top: 0;
      width: 3px; height: 0; background: var(--red); transition: height 0.4s ease;
    }
    .service-card:hover { background: #222; }
    .service-card:hover::before { height: 100%; }
    .service-card i { font-size: 2rem; color: var(--red); margin-bottom: 20px; display: block; }
    .service-card h3 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1.3rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;
    }
    .service-card p { font-size: 0.88rem; color: var(--muted); line-height: 1.7; }

    /* ── ABOUT ── */
    .about {
      background: var(--dark);
      display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;
    }
    .about__img { position: relative; overflow: hidden; }
    .about__img img { width: 100%; height: 480px; object-fit: cover; display: block; }
    .about__img::after {
      content: ''; position: absolute; bottom: 0; left: 0;
      width: 100%; height: 40%; background: linear-gradient(to top, var(--dark), transparent);
    }
    .about__badge {
      position: absolute; bottom: 30px; right: -10px;
      background: var(--red); padding: 18px 28px; text-align: center; z-index: 2;
    }
    .about__badge strong {
      font-family: 'Barlow Condensed', sans-serif; font-size: 2.4rem;
      font-weight: 800; display: block; line-height: 1;
    }
    .about__badge span { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; }
    .about__text p { font-size: 0.95rem; color: #aaa; line-height: 1.9; margin-bottom: 20px; }
    .about__checks {
      list-style: none; display: grid; grid-template-columns: 1fr 1fr;
      gap: 10px 20px; margin: 28px 0;
    }
    .about__checks li {
      display: flex; align-items: center; gap: 10px;
      font-size: 0.88rem; color: #ccc;
    }
    .about__checks li::before {
      content: ''; width: 6px; height: 6px;
      background: var(--red); border-radius: 50%; flex-shrink: 0;
    }

    /* ── PRODUCTS PREVIEW ── */
    .products-prev { background: var(--dark2); }
    .products-prev .hrow {
      display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 50px;
    }
    .products-prev .hrow .section-title { margin-bottom: 0; }
    .products-grid-prev {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 2px;
    }
    .prod-card { background: var(--dark3); overflow: hidden; }
    .prod-card__img { height: 200px; overflow: hidden; }
    .prod-card__img img {
      width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;
    }
    .prod-card:hover .prod-card__img img { transform: scale(1.08); }
    .prod-card__body { padding: 22px 24px; }
    .prod-card__body h4 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1.1rem;
      font-weight: 700; text-transform: uppercase; margin-bottom: 6px;
    }
    .prod-card__body .price { color: var(--red); font-size: 1.15rem; font-weight: 600; }

    /* ── WHY ── */
    .why { background: var(--dark); position: relative; overflow: hidden; }
    .why::before {
      content: ''; position: absolute; top: -200px; right: -200px;
      width: 600px; height: 600px; border-radius: 50%;
      background: radial-gradient(circle, rgba(232,25,44,0.06), transparent 70%);
      pointer-events: none;
    }
    .why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; }
    .why-card {
      padding: 38px 30px; background: var(--dark3);
      text-align: center; transition: background 0.3s;
    }
    .why-card:hover { background: #1e1e1e; }
    .why-card i { font-size: 2.2rem; color: var(--red); margin-bottom: 18px; }
    .why-card h3 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1.15rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;
    }
    .why-card p { font-size: 0.85rem; color: var(--muted); line-height: 1.7; }

    /* ── CTA BAND ── */
    .cta-band {
      background: var(--red); padding: 60px;
      display: flex; align-items: center; justify-content: space-between; gap: 30px;
    }
    .cta-band h2 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(1.8rem, 3vw, 2.8rem); font-weight: 800;
      text-transform: uppercase; max-width: 600px; line-height: 1.1;
    }
    .btn-white {
      background: #fff; color: var(--red); padding: 14px 36px;
      border: none; border-radius: 4px; font-family: 'Barlow', sans-serif;
      font-size: 0.9rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1px; cursor: pointer; white-space: nowrap;
      text-decoration: none; display: inline-block; transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-white:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }

    /* ── FOOTER ── */
    footer {
      background: #070707; padding: 60px 60px 30px;
      border-top: 1px solid var(--border);
    }
    .footer-grid {
      display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 50px; margin-bottom: 50px;
    }
    .footer-brand img { width: 48px; margin-bottom: 14px; }
    .footer-brand p { font-size: 0.85rem; color: var(--muted); line-height: 1.8; max-width: 280px; }
    .footer-col h4 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 2px;
      margin-bottom: 18px; color: #fff;
    }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: 10px; }
    .footer-col ul a {
      color: var(--muted); text-decoration: none; font-size: 0.85rem; transition: color 0.2s;
    }
    .footer-col ul a:hover { color: var(--red); }
    .footer-socials { display: flex; gap: 12px; margin-top: 20px; }
    .footer-socials a {
      width: 36px; height: 36px; border: 1px solid var(--border);
      border-radius: 4px; display: grid; place-items: center;
      color: var(--muted); font-size: 0.85rem; transition: all 0.25s;
    }
    .footer-socials a:hover { background: var(--red); border-color: var(--red); color: #fff; }
    .footer-bottom {
      border-top: 1px solid var(--border); padding-top: 24px;
      display: flex; align-items: center; justify-content: space-between;
      font-size: 0.8rem; color: #555;
    }

    /* ── KEYFRAMES ── */
    @keyframes fadeDown {
      from { opacity:0; transform:translateY(-20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(40px); }
      to   { opacity:1; transform:translateY(0); }
    }
    @keyframes zoomBg {
      from { transform: scale(1); }
      to   { transform: scale(1.07); }
    }
    @keyframes bounce {
      0%,100% { transform: translateX(-50%) translateY(0); }
      50%      { transform: translateX(-50%) translateY(8px); }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
      header, .hero, .cta-band { padding-left: 30px; padding-right: 30px; }
      section { padding: 70px 30px; }
      footer  { padding: 50px 30px 24px; }
      .about { grid-template-columns: 1fr; gap: 40px; }
      .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
    }
    @media (max-width: 768px) {
      nav { display: none; }
      .stats-bar { grid-template-columns: repeat(2,1fr); }
      .why-grid  { grid-template-columns: 1fr; }
      .cta-band  { flex-direction: column; text-align: center; }
      .footer-grid { grid-template-columns: 1fr; gap: 28px; }
      .footer-bottom { flex-direction: column; gap: 10px; }
      .products-prev .hrow { flex-direction: column; align-items: flex-start; gap: 20px; }
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <header>
    <a class="logo-area" href="#">
      <img src="../includes/logo1.png" alt="Logo PeçaAQ">
      <span>PEÇA<em>AQ</em></span>
    </a>
    <nav>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#servicos">Serviços</a></li>
        <li><a href="#produtos">Produtos</a></li>
        <li><a href="../Sobre/index.php">Sobre</a></li>
        <li><a href="../lojas/lojas.php">Lojas</a></li>
        <li><a href="../Comprar/indexComprar.php">Comprar</a></li>
      </ul>
    </nav>
    <button class="btn-header" id="headerLoginBtn">Faça seu login</button>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="hero__bg"></div>
    <div class="hero__content">
      <span class="hero__tag">Peças Automotivas de Qualidade</span>
      <h1 class="hero__title">Serviço &amp;<br><span>Excelência</span><br>Automotiva</h1>
      <p class="hero__sub">Encontre a peça perfeita para o seu veículo com segurança, rapidez e qualidade — tudo em um só lugar.</p>
      <p class="hero__phone"><i class="fas fa-phone-alt"></i> (51) 9 9999-9999</p>
      <div class="hero__btns">
        <a href="../Comprar/indexComprar.php" class="btn-primary">
          <i class="fas fa-shopping-cart"></i> Comprar Peças
        </a>
        <a href="../Sobre/index.php" class="btn-outline">
          <i class="fas fa-info-circle"></i> Sobre Nós
        </a>
      </div>
    </div>
    <div class="hero__scroll"><i class="fas fa-chevron-down"></i><span>Scroll</span></div>
  </section>

  <!-- STATS -->
  <div class="stats-bar">
    <div class="stat"><span class="stat__num">5.000+</span><span class="stat__label">Peças Disponíveis</span></div>
    <div class="stat"><span class="stat__num">1.200+</span><span class="stat__label">Clientes Atendidos</span></div>
    <div class="stat"><span class="stat__num">300+</span><span class="stat__label">Fornecedores Parceiros</span></div>
    <div class="stat"><span class="stat__num">98%</span><span class="stat__label">Satisfação</span></div>
  </div>

  <!-- SERVIÇOS -->
  <section class="services" id="servicos">
    <p class="section-tag">O que oferecemos</p>
    <h2 class="section-title">Nossos Serviços</h2>
    <div class="services-grid">
      <div class="service-card">
        <i class="fas fa-cogs"></i>
        <h3>Peças Originais</h3>
        <p>Peças originais e compatíveis das melhores marcas do mercado automotivo, com garantia de qualidade e procedência.</p>
      </div>
      <div class="service-card">
        <i class="fas fa-search"></i>
        <h3>Busca Inteligente</h3>
        <p>Encontre a peça certa pelo modelo, marca ou número de referência. Sistema rápido e preciso para facilitar sua busca.</p>
      </div>
      <div class="service-card">
        <i class="fas fa-truck-fast"></i>
        <h3>Entrega Rápida</h3>
        <p>Entregamos para todo o Brasil com agilidade e segurança. Seu pedido rastreado do armazém até a sua porta.</p>
      </div>
      <div class="service-card">
        <i class="fas fa-store"></i>
        <h3>Fornecedores Locais</h3>
        <p>Conectamos você às melhores lojas e fornecedores da sua região. Compre de quem está perto de você.</p>
      </div>
    </div>
  </section>

  <!-- SOBRE -->
  <section class="about">
    <div class="about__img">
      <img src="../Landingpage/imgLandingPage/b2fd00c33e.webp" alt="PeçaAQ">
      <div class="about__badge">
        <strong>5+</strong>
        <span>Anos de<br>Inovação</span>
      </div>
    </div>
    <div class="about__text">
      <p class="section-tag">Quem somos</p>
      <h2 class="section-title">A plataforma que<br>conecta você à<br><span style="color:var(--red)">peça certa</span></h2>
      <p>Fundado com o propósito de transformar o mercado automotivo, o <strong>PeçaAQ</strong> nasceu para simplificar a negociação de peças automotivas.</p>
      <p>Nossa plataforma conecta fornecedores e compradores com transparência, agilidade e tecnologia, promovendo negócios mais eficientes e modernos.</p>
      <ul class="about__checks">
        <li>Peças com garantia</li>
        <li>Preços competitivos</li>
        <li>Entrega rastreada</li>
        <li>Suporte especializado</li>
        <li>Fornecedores verificados</li>
        <li>Plataforma segura</li>
      </ul>
      <a href="../Sobre/index.php" class="btn-primary">Conheça nossa história <i class="fas fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- PRODUTOS -->
  <section class="products-prev" id="produtos">
    <div class="hrow">
      <div>
        <p class="section-tag">Catálogo</p>
        <h2 class="section-title">Peças em Destaque</h2>
      </div>
      <a href="../Comprar/indexComprar.php" class="btn-primary">Ver Catálogo <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid-prev">
      <div class="prod-card">
        <div class="prod-card__img"><img src="../Comprar/imgComprar/AmortecedorMonroe.jpg" alt="Amortecedor Monroe"></div>
        <div class="prod-card__body"><h4>Amortecedor Monroe</h4><span class="price">R$ 189,90</span></div>
      </div>
      <div class="prod-card">
        <div class="prod-card__img"><img src="../Comprar/imgComprar/BateriaMoura60Ah.jpg" alt="Bateria Moura 60Ah"></div>
        <div class="prod-card__body"><h4>Bateria Moura 60Ah</h4><span class="price">R$ 459,90</span></div>
      </div>
      <div class="prod-card">
        <div class="prod-card__img"><img src="../Comprar/imgComprar/CorreiaDentadaGates.jpg" alt="Correia Dentada Gates"></div>
        <div class="prod-card__body"><h4>Correia Dentada Gates</h4><span class="price">R$ 89,90</span></div>
      </div>
      <div class="prod-card">
        <div class="prod-card__img"><img src="../Comprar/imgComprar/VelasNGKPlatinum.jpg" alt="Velas NGK Platinum"></div>
        <div class="prod-card__body"><h4>Velas NGK Platinum</h4><span class="price">R$ 64,90</span></div>
      </div>
    </div>
  </section>

  <!-- POR QUÊ NÓS -->
  <section class="why">
    <p class="section-tag">Nossas vantagens</p>
    <h2 class="section-title">Por que escolher<br>o PeçaAQ?</h2>
    <div class="why-grid">
      <div class="why-card">
        <i class="fas fa-shield-halved"></i>
        <h3>Compra Segura</h3>
        <p>Transações protegidas com criptografia e intermediação confiável entre comprador e vendedor.</p>
      </div>
      <div class="why-card">
        <i class="fas fa-bolt"></i>
        <h3>Resposta Imediata</h3>
        <p>Plataforma ágil e intuitiva que conecta você ao produto certo em poucos segundos.</p>
      </div>
      <div class="why-card">
        <i class="fas fa-medal"></i>
        <h3>Qualidade Garantida</h3>
        <p>Todos os fornecedores são verificados. Só trabalhamos com peças de procedência comprovada.</p>
      </div>
    </div>
  </section>

  <!-- CTA BAND -->
  <div class="cta-band">
    <h2>Pronto para encontrar<br>a peça que você precisa?</h2>
    <a href="../Comprar/indexComprar.php" class="btn-white">Explorar catálogo</a>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="../includes/logo1.png" alt="Logo">
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
          <li><a href="#">Home</a></li>
          <li><a href="#servicos">Serviços</a></li>
          <li><a href="#produtos">Produtos</a></li>
          <li><a href="../Sobre/index.php">Sobre</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Conta</h4>
        <ul>
          <li><a href="../login/indexLogin.php">Login</a></li>
          <li><a href="../Cadastrar/indexCadastro.php">Cadastro</a></li>
          <li><a href="../dashboard_cliente.php">Meu Perfil</a></li>
          <li><a href="../dashboard_empresa.php">Dashboard</a></li>
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

  <script src="appLandingPage.js"></script>
  <script>
    (function() {
      const btn = document.getElementById('headerLoginBtn');
      if (!btn) return;
      const userData = localStorage.getItem('usuarioLogado');
      if (!userData) {
        btn.textContent = 'Faça seu login';
        btn.onclick = () => window.location.href = '../login/indexLogin.php';
      } else {
        try {
          const u = JSON.parse(userData);
          btn.textContent = 'Meu Perfil';
          btn.onclick = () => {
            window.location.href = (u.tipo && u.tipo.toLowerCase() !== 'cliente')
              ? '../dashboard_empresa.php'
              : '../dashboard_cliente.php';
          };
        } catch(e) {}
      }
    })();
  </script>
</body>
</html>
