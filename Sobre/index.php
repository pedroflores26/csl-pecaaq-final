<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PeçaAq - Sobre</title>

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
      --header-h: 130px;
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
      position: fixed; inset: 0 0 auto 0;
      height: var(--header-h);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 60px;
      background: rgba(10,10,10,0.94);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border);
      z-index: 999;
      animation: fadeDown 0.7s ease;
    }
    .logo-area { display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .logo-area img { width: 130px; height: 130px; object-fit: contain; }
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
    nav a:hover, nav a.active { color: #fff; }
    nav a:hover::after, nav a.active::after { width: 100%; }

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

    /* ── PAGE ── */
    .page-content { padding-top: var(--header-h); }

    /* ── PORTAL SECTION ── */
    .portal-section {
      padding: 80px 60px;
      background: var(--dark);
    }
    .portal-inner {
      max-width: 1200px; margin: 0 auto;
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 80px; align-items: center;
    }
    .portal-text .section-tag {
      font-family: 'Barlow Condensed', sans-serif; font-size: 0.8rem;
      font-weight: 600; letter-spacing: 4px; text-transform: uppercase;
      color: var(--red); margin-bottom: 10px; display: block;
    }
    .portal-text h2 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 3.5vw, 3rem); font-weight: 800;
      text-transform: uppercase; letter-spacing: 1px;
      margin-bottom: 24px; line-height: 1.05;
    }
    .portal-text h2 span { color: var(--red); }
    .portal-text p {
      font-size: 0.95rem; color: #aaa; line-height: 1.9; margin-bottom: 16px;
    }
    .portal-img { position: relative; }
    .portal-img img {
      width: 100%; height: 420px; object-fit: cover;
      object-position: center top;
      display: block;
    }
    .portal-img-caption {
      background: var(--dark3); border-left: 3px solid var(--red);
      padding: 14px 18px; margin-top: 2px;
      font-size: 0.8rem; color: var(--muted); letter-spacing: 0.5px;
    }

    /* ── QUEM CRIOU ── */
    .criou-section {
      padding: 80px 60px;
      background: var(--dark2);
    }
    .criou-section .section-tag {
      font-family: 'Barlow Condensed', sans-serif; font-size: 0.8rem;
      font-weight: 600; letter-spacing: 4px; text-transform: uppercase;
      color: var(--red); margin-bottom: 10px;
    }
    .criou-section .section-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem); font-weight: 800;
      text-transform: uppercase; margin-bottom: 14px; letter-spacing: 1px;
    }
    .criou-section .section-desc {
      font-size: 0.95rem; color: var(--muted); max-width: 560px;
      margin-bottom: 50px; line-height: 1.8;
    }
    .criou-grid {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 40px; align-items: center;
      max-width: 1200px;
    }
    .criou-foto img {
      width: 100%; max-height: 420px; object-fit: cover;
      object-position: center top; display: block;
    }
    .criou-info p {
      font-size: 0.95rem; color: #aaa; line-height: 1.9; margin-bottom: 18px;
    }
    .criou-tags {
      display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;
    }
    .criou-tag {
      background: var(--dark3); border: 1px solid var(--border);
      padding: 6px 16px; font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.78rem; font-weight: 600; letter-spacing: 2px;
      text-transform: uppercase; color: var(--muted);
    }
    .criou-tag.red { border-color: var(--red); color: var(--red); }

    /* ── PITCH SECTION ── */
    .pitch-section {
      padding: 80px 60px;
      background: var(--dark);
    }
    .pitch-section .section-tag {
      font-family: 'Barlow Condensed', sans-serif; font-size: 0.8rem;
      font-weight: 600; letter-spacing: 4px; text-transform: uppercase;
      color: var(--red); margin-bottom: 10px;
    }
    .pitch-section .section-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem); font-weight: 800;
      text-transform: uppercase; margin-bottom: 50px; letter-spacing: 1px;
    }
    .pitch-grid {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px;
    }
    .pitch-card {
      padding: 38px 30px; background: var(--dark3);
      transition: background 0.3s; position: relative; overflow: hidden;
    }
    .pitch-card::before {
      content: ''; position: absolute; left: 0; top: 0;
      width: 3px; height: 0; background: var(--red); transition: height 0.4s ease;
    }
    .pitch-card:hover { background: #222; }
    .pitch-card:hover::before { height: 100%; }
    .pitch-card i { font-size: 1.8rem; color: var(--red); margin-bottom: 18px; display: block; }
    .pitch-card h3 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1.15rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;
    }
    .pitch-card p { font-size: 0.88rem; color: var(--muted); line-height: 1.7; }

    /* ── DIFERENCIAIS ── */
    .diferenciais {
      padding: 80px 60px;
      background: var(--dark2);
    }
    .diferenciais .section-tag {
      font-family: 'Barlow Condensed', sans-serif; font-size: 0.8rem;
      font-weight: 600; letter-spacing: 4px; text-transform: uppercase;
      color: var(--red); margin-bottom: 10px;
    }
    .diferenciais .section-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem); font-weight: 800;
      text-transform: uppercase; margin-bottom: 50px; letter-spacing: 1px;
    }
    .dif-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2px;
    }
    .dif-item {
      background: var(--dark3); padding: 32px 28px;
      display: flex; gap: 18px; align-items: flex-start;
    }
    .dif-item i { font-size: 1.4rem; color: var(--red); margin-top: 3px; flex-shrink: 0; }
    .dif-item h4 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;
    }
    .dif-item p { font-size: 0.85rem; color: var(--muted); line-height: 1.7; }

    /* ── EQUIPE ── */
    .equipe {
      padding: 90px 60px; background: var(--dark); text-align: center;
    }
    .equipe h2 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800;
      text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;
    }
    .equipe .descricao {
      font-size: 0.95rem; color: var(--muted);
      max-width: 560px; margin: 0 auto 50px; line-height: 1.8;
    }
    .membros {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 24px; max-width: 1200px; margin: 0 auto;
    }
    .membro { perspective: 1000px; height: 320px; }
    .card-inner {
      position: relative; width: 100%; height: 100%;
      transform-style: preserve-3d; transition: transform 0.6s ease;
    }
    .membro:hover .card-inner { transform: rotateY(180deg); }
    .card-front, .card-back {
      position: absolute; inset: 0; backface-visibility: hidden;
      background: var(--dark3); border: 1px solid var(--border);
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; padding: 28px 20px;
    }
    .card-back { transform: rotateY(180deg); background: var(--red); }
    .card-front img {
      width: 130px; height: 130px; border-radius: 50%; object-fit: cover;
      margin-bottom: 16px; border: 3px solid var(--red);
    }
    .card-front h3, .card-back h3 {
      font-family: 'Barlow Condensed', sans-serif; font-size: 1.1rem;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;
    }
    .card-front p { font-size: 0.8rem; color: var(--red); text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }
    .card-back p { font-size: 0.85rem; line-height: 1.7; text-align: center; margin-top: 8px; opacity: 0.95; }

    /* ── FOOTER ── */
    footer { background: #070707; padding: 60px 60px 30px; border-top: 1px solid var(--border); }
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
    .footer-col ul a { color: var(--muted); text-decoration: none; font-size: 0.85rem; transition: color 0.2s; }
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

    @keyframes fadeDown {
      from { opacity:0; transform:translateY(-20px); }
      to   { opacity:1; transform:translateY(0); }
    }

    @media (max-width: 1024px) {
      header { padding-left: 30px; padding-right: 30px; }
      .portal-section, .criou-section, .pitch-section, .diferenciais, .equipe { padding: 60px 30px; }
      .portal-inner, .criou-grid { grid-template-columns: 1fr; gap: 40px; }
      .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
      footer { padding: 50px 30px 24px; }
      .pitch-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 768px) {
      nav { display: none; }
      .pitch-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr; gap: 28px; }
      .footer-bottom { flex-direction: column; gap: 10px; }
    }
  </style>
</head>
<body>

  <!-- HEADER (compartilhado) -->
  <?php $base = '../'; $activePage = 'sobre'; require __DIR__ . '/../includes/header.php'; ?>
  <div class="pq-header-spacer"></div>

  <div class="page-content">

    <!-- ── PEÇAAQ — PORTAL DE PEÇAS AUTOMOTIVAS ── -->
    <section class="portal-section">
      <div class="portal-inner">
        <div class="portal-text">
          <span class="section-tag">O projeto</span>
          <h2>PeçaAQ — Portal de<br>Peças <span>Automotivas</span></h2>
          <p>
            O <strong style="color:#fff;">PeçaAQ</strong> é uma plataforma digital voltada para a busca e divulgação de peças automotivas. O projeto surgiu com o objetivo de facilitar a conexão entre empresas que vendem peças e usuários que precisam comprá-las.
          </p>
          <p>
            Nossa proposta é trazer mais praticidade, organização e agilidade para esse processo — utilizando a tecnologia como solução para um mercado que ainda enfrenta dificuldades no meio digital.
          </p>
          <p>
            O sistema atua como um intermediário digital, conectando diretamente empresas e clientes em um único ambiente, facilitando todo o processo de busca e divulgação.
          </p>
        </div>
        <div class="portal-img">
          <img src="../includes/sao lucas.jpg" alt="PeçaAQ Portal">

        </div>
      </div>
    </section>

    <!-- ── QUEM CRIOU ── -->
    <section class="criou-section">
      <p class="section-tag">Por trás do projeto</p>
      <h2 class="section-title">Quem Criou</h2>
      <p class="section-desc">
        Seis estudantes de Tecnologia da Informação do Colégio ULBRA São Lucas que transformaram uma ideia acadêmica em um projeto real e inovador no mercado automotivo.
      </p>
      <div class="criou-grid">
        <div class="criou-foto">
          <img src="../sobre/img/porshe.jpg" alt="Equipe PeçaAQ no pitch">
        </div>
        <div class="criou-info">
          <p>
            O projeto nasceu como um desafio escolar: criar uma solução que pudesse fazer a diferença no dia a dia das pessoas. Identificamos dois problemas principais — empresas com dificuldade de divulgar seus produtos online, e usuários com dificuldade de encontrar peças específicas com rapidez.
          </p>
          <p>
            Com isso, o time desenvolveu o portal do zero, focando em uma experiência simples, acessível e eficiente. O PeçaAQ foi apresentado no evento <strong style="color:#fff;">CSL Conecta</strong> do Colégio ULBRA São Lucas, onde o projeto ganhou o palco.
          </p>
          <div class="criou-tags">
            <span class="criou-tag red">Colégio ULBRA São Lucas</span>
            <span class="criou-tag">Tecnologia da Informação</span>
            <span class="criou-tag">CSL Conecta</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ── O PROJETO EM 3 PASSOS ── -->
    <section class="pitch-section">
      <p class="section-tag">Como funciona</p>
      <h2 class="section-title">O Projeto em 3 Passos</h2>
      <div class="pitch-grid">
        <div class="pitch-card">
          <i class="fas fa-store"></i>
          <h3>Empresas Cadastram</h3>
          <p>Fornecedores e lojas acessam o sistema, cadastram seus produtos com nome, descrição, preço e detalhes. Os produtos ficam organizados e visíveis na plataforma.</p>
        </div>
        <div class="pitch-card">
          <i class="fas fa-search"></i>
          <h3>Usuários Buscam</h3>
          <p>Proprietários de veículos, mecânicos e oficinas acessam o portal e utilizam a busca para encontrar as peças que precisam de forma rápida e organizada.</p>
        </div>
        <div class="pitch-card">
          <i class="fas fa-handshake"></i>
          <h3>Conexão Direta</h3>
          <p>O sistema atua como intermediário digital, conectando compradores e vendedores em um único ambiente — sem complicação, sem perda de tempo.</p>
        </div>
      </div>
    </section>

    <!-- ── DIFERENCIAIS ── -->
    <section class="diferenciais">
      <p class="section-tag">Por que o PeçaAQ?</p>
      <h2 class="section-title">Nossos Diferenciais</h2>
      <div class="dif-grid">
        <div class="dif-item">
          <i class="fas fa-bolt"></i>
          <div>
            <h4>Conexão Direta</h4>
            <p>Empresas e clientes conectados sem intermediários complexos — simples e direto.</p>
          </div>
        </div>
        <div class="dif-item">
          <i class="fas fa-tags"></i>
          <div>
            <h4>Cadastro de Produtos</h4>
            <p>As próprias empresas cadastram e divulgam seus produtos na plataforma.</p>
          </div>
        </div>
        <div class="dif-item">
          <i class="fas fa-search"></i>
          <div>
            <h4>Busca Rápida</h4>
            <p>Sistema de busca ágil e organizado, que facilita a navegação e encontra a peça certa.</p>
          </div>
        </div>
        <div class="dif-item">
          <i class="fas fa-desktop"></i>
          <div>
            <h4>Interface Intuitiva</h4>
            <p>Design pensado para qualquer tipo de usuário — fácil de usar desde o primeiro acesso.</p>
          </div>
        </div>
        <div class="dif-item">
          <i class="fas fa-layer-group"></i>
          <div>
            <h4>Centralizado</h4>
            <p>Todas as informações em um único portal — sem precisar visitar vários sites.</p>
          </div>
        </div>
        <div class="dif-item">
          <i class="fas fa-clock"></i>
          <div>
            <h4>Economia de Tempo</h4>
            <p>Encontre a peça necessária em poucos passos, economizando tempo e esforço.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ── QUEM SOMOS ── -->
    <section class="equipe">
      <h2>Quem somos</h2>
      <p class="descricao">
        Seis estudantes de Tecnologia da Informação que transformaram uma ideia acadêmica em um projeto real e inovador no mercado automotivo.
      </p>

      <div class="membros">
        <div class="membro">
          <div class="card-inner">
            <div class="card-front">
              <img src="img/joaquim.jpeg" alt="Joaquim Barbosa">
              <h3>Joaquim Barbosa</h3>
              <p>Dev</p>
            </div>
            <div class="card-back">
              <h3>Joaquim Barbosa</h3>
              <p>Responsável pelo design e identidade visual do PeçaAQ. Criou o README, o Branch e participou do desenvolvimento das telas.</p>
            </div>
          </div>
        </div>

        <div class="membro">
          <div class="card-inner">
            <div class="card-front">
              <img src="img/gabriel-sandes.jpeg" alt="Gabriel Sandes">
              <h3>Gabriel Sandes</h3>
              <p>POO</p>
            </div>
            <div class="card-back">
              <h3>Gabriel Sandes</h3>
              <p>Líder técnico e arquiteto do sistema. Atua no backend e garante o desempenho e organização do código.</p>
            </div>
          </div>
        </div>

        <div class="membro">
          <div class="card-inner">
            <div class="card-front">
              <img src="img/gabriel-bandasz.jpeg" alt="Gabriel Bandasz">
              <h3>Gabriel Bandasz</h3>
              <p>Scrum Master</p>
            </div>
            <div class="card-back">
              <h3>Gabriel Bandasz</h3>
              <p>Scrum Master, responsável pela organização, ritmo e colaboração do time durante o desenvolvimento.</p>
            </div>
          </div>
        </div>

        <div class="membro">
          <div class="card-inner">
            <div class="card-front">
              <img src="img/pedro flores.jpeg" alt="Pedro Flores">
              <h3>Pedro Flores</h3>
              <p>Dev</p>
            </div>
            <div class="card-back">
              <h3>Pedro Flores</h3>
              <p>Especialista em APIs e banco de dados, responsável pela lógica do sistema e comunicação entre o front e o back.</p>
            </div>
          </div>
        </div>

        <div class="membro">
          <div class="card-inner">
            <div class="card-front">
              <img src="img/lucas-matheus.jpg" alt="Lucas Matheus">
              <h3>Lucas Matheus</h3>
              <p>Dev</p>
            </div>
            <div class="card-back">
              <h3>Lucas Matheus</h3>
              <p>Cuida da experiência visual e testes de estabilidade, garantindo performance e fluidez ao site.</p>
            </div>
          </div>
        </div>

      </div>
    </section>

  </div><!-- /page-content -->

  <!-- FOOTER (compartilhado) -->
  <?php require __DIR__ . '/../includes/footer.php'; ?>

  <script src="script.js"></script>
</body>
</html>
