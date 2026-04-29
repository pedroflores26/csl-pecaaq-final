<?php
$conn = new mysqli('localhost', 'root', '', 'pecaaq');

if ($conn->connect_error) {
    die('Erro na conexão: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

$sql = "SELECT id, nome_fantasia, avaliacao_media, total_vendas 
        FROM empresas 
        WHERE status = 'aprovada'";

$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lojas Parceiras — PeçaAQ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../includes/layout.css">

<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --red: #e8192c;
    --red-dark: #b01020;
    --red-glow: rgba(232, 25, 44, 0.18);
    --bg: #080808;
    --bg-card: #111111;
    --bg-card-hover: #161616;
    --border: rgba(255,255,255,0.06);
    --border-hover: rgba(232,25,44,0.5);
    --text: #f0f0f0;
    --text-muted: #666;
    --text-dim: #999;
    --metal: #2a2a2a;
    --header-h: 72px;
  }

  html { scroll-behavior: smooth; }

  ::-webkit-scrollbar { width: 4px; }
  ::-webkit-scrollbar-track { background: var(--bg); }
  ::-webkit-scrollbar-thumb { background: var(--red); border-radius: 2px; }

  body {
    font-family: 'Barlow', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    overflow-x: hidden;
  }

  /* ─── NOISE OVERLAY ─── */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 0;
    opacity: .4;
  }

  /* ─── HEADER ─── */
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

  @keyframes fadeDown {
    from { opacity:0; transform:translateY(-20px); }
    to   { opacity:1; transform:translateY(0); }
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

  header nav ul { display: flex; list-style: none; gap: 38px; }
  header nav a {
    color: #ccc; text-decoration: none; font-size: 0.88rem;
    font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase;
    position: relative; transition: color 0.25s;
  }
  header nav a::after {
    content: ''; position: absolute; left: 0; bottom: -4px;
    width: 0; height: 2px; background: var(--red); transition: width 0.3s;
  }
  header nav a:hover { color: #fff; }
  header nav a:hover::after,
  header nav a.active::after { width: 100%; }
  header nav a.active { color: #fff; }

  .btn-header {
    background: var(--red); color: #fff; border: none;
    padding: 10px 22px; font-family: 'Barlow', sans-serif;
    font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;
    text-transform: uppercase; border-radius: 4px; cursor: pointer;
    transition: background 0.25s, transform 0.2s, box-shadow 0.25s;
  }
  .btn-header:hover {
    background: var(--red-dark); transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(232,25,44,0.4);
  }

  /* ─── HERO ─── */
  .hero {
    position: relative;
    padding: 100px 48px 80px;
    padding-top: calc(var(--header-h) + 60px);
    overflow: hidden;
  }

  .hero-bg-line {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background:
      repeating-linear-gradient(
        90deg,
        transparent,
        transparent 119px,
        rgba(255,255,255,0.015) 120px
      );
    pointer-events: none;
  }

  .hero-accent {
    position: absolute;
    top: -120px;
    right: -80px;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(232,25,44,0.12) 0%, transparent 70%);
    pointer-events: none;
  }

  .hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--red);
    margin-bottom: 20px;
  }

  .hero-eyebrow::before {
    content: '';
    display: block;
    width: 28px;
    height: 2px;
    background: var(--red);
  }

  .hero h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(56px, 8vw, 108px);
    line-height: .92;
    letter-spacing: 2px;
    color: var(--text);
    position: relative;
    z-index: 1;
  }

  .hero h1 em {
    font-style: normal;
    color: var(--red);
    -webkit-text-fill-color: transparent;
    -webkit-text-stroke: 2px var(--red);
  }

  .hero-sub {
    margin-top: 24px;
    font-size: 16px;
    font-weight: 300;
    color: var(--text-dim);
    max-width: 460px;
    line-height: 1.7;
    position: relative;
    z-index: 1;
  }

  /* ─── STATS BAR ─── */
  .stats-bar {
    display: flex;
    gap: 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    margin: 0 48px 64px;
  }

  .stat-item {
    flex: 1;
    padding: 24px 32px;
    border-right: 1px solid var(--border);
  }

  .stat-item:last-child { border-right: none; }

  .stat-number {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 42px;
    color: var(--text);
    line-height: 1;
  }

  .stat-number span { color: var(--red); }

  .stat-label {
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-top: 4px;
  }

  /* ─── SECTION ─── */
  .section {
    padding: 0 48px 100px;
    position: relative;
    z-index: 1;
  }

  .section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 40px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border);
  }

  .section-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 36px;
    letter-spacing: 2px;
    color: var(--text);
  }

  .section-count {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--text-muted);
  }

  /* ─── GRID ─── */
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2px;
  }

  /* ─── CARD ─── */
  .card {
    position: relative;
    background: var(--bg-card);
    padding: 32px 28px;
    border: 1px solid var(--border);
    overflow: hidden;
    cursor: pointer;
    transition: background .25s, border-color .25s, transform .25s;
    animation: fadeUp .5s ease both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .card:nth-child(1) { animation-delay: .05s; }
  .card:nth-child(2) { animation-delay: .10s; }
  .card:nth-child(3) { animation-delay: .15s; }
  .card:nth-child(4) { animation-delay: .20s; }
  .card:nth-child(5) { animation-delay: .25s; }
  .card:nth-child(6) { animation-delay: .30s; }
  .card:nth-child(n+7) { animation-delay: .35s; }

  .card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px; height: 100%;
    background: var(--red);
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform .3s ease;
  }

  .card:hover {
    background: var(--bg-card-hover);
    border-color: var(--border-hover);
    transform: translateY(-2px);
  }

  .card:hover::before { transform: scaleY(1); }

  .card-index {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 11px;
    letter-spacing: 2px;
    color: var(--text-muted);
    margin-bottom: 16px;
  }

  .card-avatar {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: var(--metal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    color: var(--text-dim);
    margin-bottom: 20px;
    border: 1px solid var(--border);
    transition: background .25s, border-color .25s, color .25s;
  }

  .card:hover .card-avatar {
    background: var(--red-glow);
    border-color: rgba(232,25,44,.3);
    color: var(--red);
  }

  .card-name {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--text);
    line-height: 1.2;
    margin-bottom: 20px;
  }

  .card-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 28px;
  }

  .meta-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text-dim);
  }

  .meta-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: var(--metal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
  }

  .meta-value {
    font-weight: 500;
    color: var(--text);
  }

  .stars {
    display: flex;
    gap: 2px;
    margin-top: 2px;
  }

  .star {
    width: 7px;
    height: 7px;
    background: var(--metal);
    clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
  }

  .star.filled { background: var(--red); }
  .star.half   { background: linear-gradient(90deg, var(--red) 50%, var(--metal) 50%); }

  .card-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 16px;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    cursor: pointer;
    transition: background .2s, border-color .2s, color .2s;
    text-decoration: none;
  }

  .card-btn:hover,
  .card:hover .card-btn {
    background: var(--red);
    border-color: var(--red);
    color: #fff;
  }

  .card-btn svg {
    width: 14px; height: 14px;
    transition: transform .2s;
  }

  .card-btn:hover svg,
  .card:hover .card-btn svg { transform: translateX(3px); }

  /* ─── EMPTY STATE ─── */
  .empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 40px;
    border: 1px dashed var(--border);
    border-radius: 4px;
  }

  .empty-icon {
    font-size: 40px;
    margin-bottom: 16px;
    opacity: .4;
  }

  .empty p {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px;
    letter-spacing: 1px;
    color: var(--text-muted);
  }
.main-footer {
  background: #111;
  border-top: 1px solid var(--border);
  padding: 60px 48px 28px;
  position: relative;
  z-index: 2;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 40px;
  margin-bottom: 38px;
}

.footer-brand img {
  width: 120px;
  max-width: 100%;
  height: auto;
  display: block;
  margin-bottom: 16px;
  object-fit: contain;
}

.footer-brand p {
  color: var(--text-dim);
  font-size: 14px;
  line-height: 1.7;
  max-width: 320px;
  margin-bottom: 18px;
}

.footer-socials {
  display: flex;
  gap: 10px;
}

.footer-socials a {
  width: 34px;
  height: 34px;
  border: 1px solid var(--border);
  border-radius: 6px;
  color: var(--text-dim);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.footer-socials a:hover {
  color: var(--red);
  border-color: var(--red);
}

.footer-col h4 {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 15px;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 14px;
}

.footer-col ul {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 9px;
}

.footer-col a {
  color: var(--text-dim);
  text-decoration: none;
  font-size: 14px;
}

.footer-col a:hover {
  color: #fff;
}

.footer-col i {
  color: var(--red);
  margin-right: 8px;
}

.footer-bottom {
  border-top: 1px solid var(--border);
  padding-top: 20px;
  display: flex;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  color: var(--text-muted);
  font-size: 13px;
}

@media (max-width: 768px) {
  .main-footer {
    padding: 40px 20px 24px;
  }

  .footer-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }

  .footer-bottom {
    text-align: center;
    justify-content: center;
  }
}

  @media (max-width: 1024px) {
    header { padding: 0 30px; }
  }
  @media (max-width: 768px) {
    header nav { display: none; }
    .hero { padding: calc(var(--header-h) + 30px) 20px 50px; }
    .stats-bar { margin: 0 20px 40px; flex-wrap: wrap; }
    .stat-item { min-width: 50%; border-bottom: 1px solid var(--border); }
    .section { padding: 0 20px 60px; }
    footer { padding: 24px 20px; flex-direction: column; gap: 12px; text-align: center; }
  }
</style>
</head>
<body>


  <!-- HEADER (compartilhado) -->
  <?php $base = '../'; $activePage = 'sobre'; require __DIR__ . '/../includes/header.php'; ?>
  <div class="pq-header-spacer"></div>
<!-- HEADER -->
<header>
  <a class="logo-area" href="../LandingPage/indexLandingPage.php">
    <img src="../includes/logo1.png" alt="Logo PeçaAQ">
    <span>PEÇA<em>AQ</em></span>
  </a>
  <nav>
    <ul>
      <li><a href="../LandingPage/indexLandingPage.php">Home</a></li>
      <li><a href="../Comprar/indexComprar.php">Produtos</a></li>
      <li><a href="../Sobre/index.php">Sobre</a></li>
      <li><a href="lojas.php" class="active">Lojas</a></li>
      <li><a href="../Comprar/indexComprar.php">Comprar</a></li>
    </ul>
  </nav>
  <button class="btn-header" id="headerLoginBtn">Faça seu login</button>
</header>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-line"></div>
  <div class="hero-accent"></div>

  <p class="hero-eyebrow">Lojas Parceiras</p>
  <h1>ENCONTRE AS<br>MELHORES<br><em>LOJAS</em></h1>
  <p class="hero-sub">Fornecedores verificados, avaliados pela comunidade PeçaAQ. Qualidade e confiança em cada peça.</p>
</section>

<!-- STATS BAR -->
<?php
$total_lojas = $res->num_rows;
$total_vendas_geral = 0;
$soma_avaliacao = 0;
$temp_rows = [];

while ($r = $res->fetch_assoc()) {
    $temp_rows[] = $r;
    $total_vendas_geral += (int)$r['total_vendas'];
    $soma_avaliacao += (float)$r['avaliacao_media'];
}
$media_geral = $total_lojas > 0 ? $soma_avaliacao / $total_lojas : 0;
?>
<div class="stats-bar">
  <div class="stat-item">
    <div class="stat-number"><?php echo $total_lojas; ?><span>+</span></div>
    <div class="stat-label">Lojas ativas</div>
  </div>
  <div class="stat-item">
    <div class="stat-number"><?php echo number_format($total_vendas_geral, 0, ',', '.'); ?></div>
    <div class="stat-label">Vendas realizadas</div>
  </div>
  <div class="stat-item">
    <div class="stat-number"><?php echo number_format($media_geral, 1, ',', '.'); ?><span>/5</span></div>
    <div class="stat-label">Avaliação média</div>
  </div>
  <div class="stat-item">
    <div class="stat-number">100<span>%</span></div>
    <div class="stat-label">Verificadas</div>
  </div>
</div>

<!-- GRID -->
<section class="section">
  <div class="section-header">
    <h2 class="section-title">Lojas Disponíveis</h2>
    <span class="section-count"><?php echo $total_lojas; ?> resultado<?php echo $total_lojas !== 1 ? 's' : ''; ?></span>
  </div>

  <div class="grid">
    <?php if (count($temp_rows) > 0): ?>

      <?php foreach ($temp_rows as $i => $row):
        $nome = htmlspecialchars($row['nome_fantasia']);
        $iniciais = mb_strtoupper(mb_substr($nome, 0, 2));
        $avaliacao = (float)$row['avaliacao_media'];
        $vendas = (int)$row['total_vendas'];
        $estrelas_cheias = floor($avaliacao);
        $meia_estrela = ($avaliacao - $estrelas_cheias) >= 0.3;
      ?>

        <div class="card">
          <div class="card-index"><?php printf('%02d', $i + 1); ?></div>

          <div class="card-avatar"><?php echo $iniciais; ?></div>

          <h3 class="card-name"><?php echo $nome; ?></h3>

          <div class="card-meta">
            <div class="meta-row">
              <div class="meta-icon">★</div>
              <div>
                <div class="meta-value"><?php echo number_format($avaliacao, 1, ',', '.'); ?> / 5,0</div>
                <div class="stars">
                  <?php for ($s = 1; $s <= 5; $s++): ?>
                    <?php if ($s <= $estrelas_cheias): ?>
                      <div class="star filled"></div>
                    <?php elseif ($s == $estrelas_cheias + 1 && $meia_estrela): ?>
                      <div class="star half"></div>
                    <?php else: ?>
                      <div class="star"></div>
                    <?php endif; ?>
                  <?php endfor; ?>
                </div>
              </div>
            </div>

            <div class="meta-row">
              <div class="meta-icon">🛒</div>
              <div>
                <span class="meta-value"><?php echo number_format($vendas, 0, ',', '.'); ?></span>
                <span style="margin-left:4px; font-size:12px;">vendas</span>
              </div>
            </div>
          </div>

          <a href="ver_loja.php?id=<?php echo (int)$row['id']; ?>" class="card-btn">
            Ver loja
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M3 8h10M9 4l4 4-4 4"/>
            </svg>
          </a>
        </div>

      <?php endforeach; ?>

    <?php else: ?>

      <div class="empty">
        <div class="empty-icon">⚙</div>
        <p>Nenhuma loja encontrada.</p>
      </div>

    <?php endif; ?>
  </div>
</section>

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


<script>
document.addEventListener("DOMContentLoaded", function () {
  const botoes = document.querySelectorAll("#headerLoginBtn, .btn-header, .pq-btn-header");

  botoes.forEach(function (btn) {
    btn.style.pointerEvents = "auto";
    btn.style.cursor = "pointer";

    btn.onclick = function (e) {
      e.preventDefault();
      e.stopPropagation();

      const BASE = "../";
      const userData = localStorage.getItem("usuarioLogado");

      if (!userData) {
        window.location.href = BASE + "login/indexLogin.php";
        return;
      }

      try {
        const usuario = JSON.parse(userData);
        const tipo = (usuario.tipo || "").toLowerCase();

        if (tipo === "empresa") {
          window.location.href = BASE + "dashboard_empresa.php";
        } else if (tipo === "admin") {
          window.location.href = BASE + "Dashboard/indexDashboard.php";
        } else {
          window.location.href = BASE + "dashboard_cliente.php?tab=perfil";
        }
      } catch (erro) {
        localStorage.removeItem("usuarioLogado");
        window.location.href = BASE + "login/indexLogin.php";
      }
    };
  });
});
</script>
</body>
</html>
