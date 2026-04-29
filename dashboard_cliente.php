<?php
// ═══════════════════════════════════════════════════════
//  PeçaAQ — DASHBOARD DO CLIENTE
//  Painel pessoal do comprador. Conecta ao banco pecaaq.
// ═══════════════════════════════════════════════════════

session_start();

// Proteção de sessão — redireciona se não logado ou não for cliente
if (empty($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'cliente') {
    header('Location: Login/indexLogin.php');
    exit;
}

// ── Conexão ─────────────────────────────────────────────
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pecaaq';

$conn = @new mysqli($host, $user, $pass, $db);
$dbOk = !$conn->connect_error;
if ($dbOk) $conn->set_charset('utf8mb4');

$uid = (int)$_SESSION['id_usuario'];

// ── Helpers ─────────────────────────────────────────────
function q($conn, $sql, $types = '', ...$vals) {
    $st = $conn->prepare($sql);
    if ($types) $st->bind_param($types, ...$vals);
    $st->execute();
    return $st->get_result();
}
function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function rows($conn, $sql, $types = '', ...$vals) {
    $r = q($conn, $sql, $types, ...$vals);
    return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}
function scalar($conn, $sql, $types = '', ...$vals) {
    $r = q($conn, $sql, $types, ...$vals);
    return $r ? ($r->fetch_row()[0] ?? null) : null;
}

// ── Ações POST ───────────────────────────────────────────
$msg = '';
$msgType = 'ok';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dbOk) {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'update_perfil') {
        $nome      = $conn->real_escape_string($_POST['nome'] ?? '');
        $sobrenome = $conn->real_escape_string($_POST['sobrenome'] ?? '');
        $telefone  = $conn->real_escape_string($_POST['telefone'] ?? '');
        $conn->query("UPDATE usuarios SET nome='$nome', sobrenome='$sobrenome', telefone='$telefone', atualizado_em=NOW() WHERE id=$uid");
        $msg = "Perfil atualizado com sucesso.";
    }
    elseif ($acao === 'cancelar_pedido') {
        $pedido_id = (int)($_POST['pedido_id'] ?? 0);
        // Só pode cancelar pedidos do próprio usuário e que ainda não foram enviados
        $ok = scalar($conn, "SELECT COUNT(*) FROM pedidos WHERE id=? AND usuario_id=? AND status IN ('aguardando_pagamento','pagamento_aprovado')", 'ii', $pedido_id, $uid);
        if ($ok) {
            $conn->query("UPDATE pedidos SET status='cancelado', atualizado_em=NOW() WHERE id=$pedido_id");
            $conn->query("INSERT INTO pedido_historico (pedido_id, status, descricao) VALUES ($pedido_id,'cancelado','Cancelado pelo cliente.')");
            $msg = "Pedido cancelado.";
            $msgType = 'warn';
        } else {
            $msg = "Não foi possível cancelar este pedido.";
            $msgType = 'err';
        }
    }
    elseif ($acao === 'abrir_ticket') {
        $assunto   = $conn->real_escape_string($_POST['assunto'] ?? '');
        $categoria = $conn->real_escape_string($_POST['categoria'] ?? 'duvida');
        $mensagem  = $conn->real_escape_string($_POST['mensagem'] ?? '');
        $numero    = 'TK-' . strtoupper(substr(uniqid(), -8));
        $conn->query("INSERT INTO tickets (numero, usuario_id, assunto, categoria, mensagem, status, prioridade) VALUES ('$numero', $uid, '$assunto', '$categoria', '$mensagem', 'aberto', 'media')");
        $msg = "Ticket $numero aberto com sucesso.";
    }

    if ($msg) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $msgType];
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=' . ($_POST['tab'] ?? 'dashboard'));
        exit;
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── Aba ativa ───────────────────────────────────────────
$tab = $_GET['tab'] ?? 'dashboard';

// ── Dados ───────────────────────────────────────────────
$data = [];
$usuario = [];

if ($dbOk) {
    $usuario = rows($conn, "SELECT * FROM usuarios WHERE id=?", 'i', $uid)[0] ?? [];

    if ($tab === 'dashboard') {
        $data['total_pedidos']    = scalar($conn, "SELECT COUNT(*) FROM pedidos WHERE usuario_id=?", 'i', $uid) ?? 0;
        $data['pedidos_andamento']= scalar($conn, "SELECT COUNT(*) FROM pedidos WHERE usuario_id=? AND status NOT IN ('entregue','cancelado','devolvido')", 'i', $uid) ?? 0;
        $data['total_gasto']      = scalar($conn, "SELECT SUM(total) FROM pedidos WHERE usuario_id=? AND status NOT IN ('cancelado','devolvido','reembolsado')", 'i', $uid) ?? 0;
        $data['tickets_abertos']  = scalar($conn, "SELECT COUNT(*) FROM tickets WHERE usuario_id=? AND status='aberto'", 'i', $uid) ?? 0;
        $data['pedidos_recentes'] = rows($conn, "SELECT numero, total, status, criado_em FROM pedidos WHERE usuario_id=? ORDER BY criado_em DESC LIMIT 5", 'i', $uid);
    }
    elseif ($tab === 'pedidos') {
        $data['pedidos'] = rows($conn, "SELECT p.id, p.numero, p.total, p.subtotal, p.frete, p.desconto, p.status, p.rastreamento, p.metodo_pagamento, p.parcelas, p.criado_em FROM pedidos p WHERE p.usuario_id=? ORDER BY p.criado_em DESC", 'i', $uid);
    }
    elseif ($tab === 'tickets') {
        $data['tickets'] = rows($conn, "SELECT id, numero, assunto, categoria, prioridade, status, criado_em FROM tickets WHERE usuario_id=? ORDER BY criado_em DESC", 'i', $uid);
    }
    elseif ($tab === 'perfil') {
        // já temos $usuario
    }
    elseif ($tab === 'enderecos') {
        $data['enderecos'] = rows($conn, "SELECT * FROM enderecos WHERE usuario_id=? ORDER BY principal DESC, criado_em DESC", 'i', $uid);
    }
    elseif ($tab === 'favoritos') {
        $data['favoritos'] = rows($conn, "SELECT p.id, p.nome, p.preco, p.estoque, p.avaliacao_media, c.nome cat, m.nome marca FROM favoritos f JOIN produtos p ON p.id=f.produto_id LEFT JOIN categorias c ON c.id=p.categoria_id LEFT JOIN marcas m ON m.id=p.marca_id WHERE f.usuario_id=?", 'i', $uid);
    }
}

$statusColors = [
    'aguardando_pagamento'=>'#f59e0b','pagamento_aprovado'=>'#3b82f6',
    'em_separacao'=>'#8b5cf6','enviado'=>'#06b6d4','entregue'=>'#22c55e',
    'cancelado'=>'#ef4444','devolvido'=>'#f97316','reembolsado'=>'#ec4899',
    'aberto'=>'#ef4444','em_atendimento'=>'#f59e0b','resolvido'=>'#22c55e','fechado'=>'#6b7280',
    'urgente'=>'#ef4444','alta'=>'#f97316','media'=>'#f59e0b','baixa'=>'#6b7280',
];

$nomeCompleto = trim(($usuario['nome'] ?? '') . ' ' . ($usuario['sobrenome'] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Painel — PeçaAQ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg:      #080c10;
  --surface: #0c1219;
  --panel:   #101820;
  --border:  #1e3040;
  --accent:  #e8192c;
  --accent2: #ff6b35;
  --blue:    #3b82f6;
  --green:   #22c55e;
  --yellow:  #f59e0b;
  --text:    #e2eaf2;
  --muted:   #4a6a82;
  --mono:    'JetBrains Mono', monospace;
  --sans:    'Space Grotesk', sans-serif;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--sans);
  font-size: 15px;
  min-height: 100vh;
  background-image:
    radial-gradient(ellipse 70% 50% at 100% 0%, rgba(232,25,44,.06) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 0% 100%, rgba(59,130,246,.04) 0%, transparent 50%);
}

/* ── Layout ── */
.layout { display: flex; min-height: 100vh; }

/* ── Sidebar ── */
.sidebar {
  width: 240px; flex-shrink: 0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  position: sticky; top: 0; height: 100vh;
}
.logo {
  padding: 24px 20px 20px;
  border-bottom: 1px solid var(--border);
}
.logo-brand {
  font-size: 22px; font-weight: 700; letter-spacing: 2px;
  color: var(--text);
}
.logo-brand em { color: var(--accent); font-style: normal; }
.logo-sub { font-size: 11px; color: var(--muted); margin-top: 4px; letter-spacing: 1px; text-transform: uppercase; }

.user-card {
  margin: 16px 16px 0;
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 14px;
  display: flex; align-items: center; gap: 10px;
}
.user-avatar {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), #8b1220);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 700; color: #fff;
  flex-shrink: 0;
}
.user-name { font-size: 13px; font-weight: 600; line-height: 1.2; }
.user-tipo { font-size: 10px; color: var(--accent); letter-spacing: 1px; text-transform: uppercase; font-family: var(--mono); }

.nav { flex: 1; padding: 16px 0; overflow-y: auto; }
.nav a {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 20px;
  color: var(--muted);
  text-decoration: none;
  font-size: 14px; font-weight: 500;
  border-left: 3px solid transparent;
  transition: all .15s;
}
.nav a:hover { color: var(--text); background: rgba(232,25,44,.04); }
.nav a.active { color: var(--accent); border-left-color: var(--accent); background: rgba(232,25,44,.06); }
.nav-icon { font-size: 16px; width: 20px; text-align: center; }

.sidebar-footer {
  padding: 14px 20px;
  border-top: 1px solid var(--border);
}
.logout-link {
  display: flex; align-items: center; gap: 8px;
  color: var(--muted); text-decoration: none;
  font-size: 13px; font-weight: 500;
  transition: color .15s;
}
.logout-link:hover { color: var(--accent); }

/* ── Main ── */
.main { flex: 1; overflow-x: hidden; }

.topbar {
  padding: 16px 28px;
  border-bottom: 1px solid var(--border);
  background: rgba(8,12,16,.9);
  backdrop-filter: blur(12px);
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 100;
}
.page-title { font-size: 18px; font-weight: 700; }
.page-title span { color: var(--accent); }
.topbar-right { font-family: var(--mono); font-size: 11px; color: var(--muted); }

.content { padding: 24px 28px; }

/* ── Flash ── */
.flash {
  padding: 12px 18px; margin-bottom: 20px; border-radius: 6px;
  font-size: 14px; display: flex; align-items: center; gap: 10px;
  border: 1px solid;
}
.flash-ok   { background: rgba(34,197,94,.08);  border-color: var(--green);  color: var(--green); }
.flash-warn { background: rgba(245,158,11,.08);  border-color: var(--yellow); color: var(--yellow); }
.flash-err  { background: rgba(232,25,44,.08);   border-color: var(--accent); color: var(--accent); }

/* ── Stats ── */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card {
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 20px;
  position: relative; overflow: hidden;
  transition: border-color .2s, transform .2s;
}
.stat-card:hover { border-color: var(--accent); transform: translateY(-2px); }
.stat-card::after {
  content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent, var(--accent), transparent);
  opacity: 0; transition: opacity .2s;
}
.stat-card:hover::after { opacity: 1; }
.stat-label { font-size: 11px; color: var(--muted); letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 10px; }
.stat-val { font-family: var(--mono); font-size: 28px; font-weight: 700; color: var(--accent); }
.stat-sub { font-size: 12px; color: var(--muted); margin-top: 4px; }

/* ── Section ── */
.section { margin-bottom: 28px; }
.section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.section-title { font-size: 14px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 8px; }
.section-title::before { content: ''; display: inline-block; width: 3px; height: 16px; background: var(--accent); border-radius: 2px; }

/* ── Table ── */
.table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; }
thead tr { background: var(--surface); }
th {
  padding: 11px 14px; text-align: left;
  font-family: var(--mono); font-size: 10px; color: var(--muted);
  letter-spacing: 2px; text-transform: uppercase;
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
}
td {
  padding: 12px 14px; border-bottom: 1px solid rgba(30,48,64,.5);
  font-size: 13px; vertical-align: middle;
}
tbody tr:hover { background: rgba(232,25,44,.025); }
tbody tr:last-child td { border-bottom: none; }
.mono { font-family: var(--mono); font-size: 12px; }

/* ── Badges ── */
.badge {
  display: inline-block; padding: 3px 10px; border-radius: 4px;
  font-family: var(--mono); font-size: 10px; font-weight: 600;
  letter-spacing: 0.5px; text-transform: uppercase; border: 1px solid;
}

/* ── Buttons ── */
.btn {
  padding: 8px 18px; border-radius: 6px; border: 1px solid;
  cursor: pointer; font-family: var(--sans); font-weight: 600;
  font-size: 13px; transition: all .15s; text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
}
.btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
.btn-primary:hover { background: #c8101f; }
.btn-outline  { background: transparent; border-color: var(--border); color: var(--muted); }
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }
.btn-danger   { background: rgba(232,25,44,.1); border-color: var(--accent); color: var(--accent); }
.btn-danger:hover { background: rgba(232,25,44,.2); }
.btn-sm { padding: 5px 12px; font-size: 12px; }

/* ── Forms ── */
.form-card {
  background: var(--panel); border: 1px solid var(--border);
  border-radius: 10px; padding: 24px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1/-1; }
label { font-size: 11px; color: var(--muted); letter-spacing: 1.5px; text-transform: uppercase; font-family: var(--mono); }
input[type=text], input[type=email], input[type=tel], input[type=date], select, textarea {
  background: var(--surface); border: 1px solid var(--border);
  color: var(--text); padding: 10px 14px; border-radius: 6px;
  font-family: var(--sans); font-size: 14px; outline: none;
  transition: border-color .2s;
}
input:focus, select:focus, textarea:focus { border-color: var(--accent); }
textarea { resize: vertical; min-height: 100px; }

/* ── Ticket form ── */
.ticket-form { margin-bottom: 24px; }

/* ── Endereço card ── */
.address-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap: 16px; }
.address-card {
  background: var(--panel); border: 1px solid var(--border);
  border-radius: 10px; padding: 18px; position: relative;
  transition: border-color .2s;
}
.address-card.principal { border-color: var(--accent); }
.address-card:hover { border-color: var(--muted); }
.address-tag { font-size: 10px; font-family: var(--mono); color: var(--accent); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }
.address-line { font-size: 13px; line-height: 1.7; color: var(--text); }
.address-principal-badge {
  position: absolute; top: 12px; right: 12px;
  background: rgba(232,25,44,.15); border: 1px solid var(--accent);
  color: var(--accent); font-size: 9px; font-family: var(--mono);
  padding: 2px 8px; border-radius: 3px; letter-spacing: 1px;
}

/* ── Produto favorito ── */
.favs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap: 16px; }
.fav-card {
  background: var(--panel); border: 1px solid var(--border);
  border-radius: 10px; padding: 18px;
  transition: border-color .2s, transform .2s;
}
.fav-card:hover { border-color: var(--accent); transform: translateY(-2px); }
.fav-cat { font-size: 10px; color: var(--muted); letter-spacing: 1px; text-transform: uppercase; font-family: var(--mono); margin-bottom: 6px; }
.fav-name { font-size: 14px; font-weight: 600; margin-bottom: 8px; }
.fav-price { font-family: var(--mono); font-size: 18px; color: var(--accent); font-weight: 700; }
.fav-stock { font-size: 11px; color: var(--muted); margin-top: 4px; }
.fav-actions { display: flex; gap: 8px; margin-top: 14px; }

/* ── Rastreio ── */
.rastreio-pill {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(6,182,212,.08); border: 1px solid rgba(6,182,212,.25);
  color: #06b6d4; font-family: var(--mono); font-size: 11px;
  padding: 3px 10px; border-radius: 4px;
}

/* ── Empty ── */
.empty-state {
  text-align: center; padding: 56px 20px;
  color: var(--muted);
}
.empty-state .icon { font-size: 48px; margin-bottom: 12px; }
.empty-state h3 { font-size: 16px; color: var(--text); margin-bottom: 6px; }
.empty-state p { font-size: 13px; }

/* ── Welcome banner ── */
.welcome-banner {
  background: linear-gradient(135deg, rgba(232,25,44,.12) 0%, rgba(139,18,32,.06) 100%);
  border: 1px solid rgba(232,25,44,.2);
  border-radius: 12px; padding: 24px 28px; margin-bottom: 28px;
  display: flex; align-items: center; justify-content: space-between;
}
.welcome-text h2 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.welcome-text p { color: var(--muted); font-size: 13px; }
.welcome-actions { display: flex; gap: 10px; }
</style>
</head>
<body>
<div class="layout">

<!-- ── Sidebar ── -->
<aside class="sidebar">
  <div class="logo">
    <div class="logo-brand">PEÇA<em>AQ</em></div>
    <div class="logo-sub">Meu Painel</div>
  </div>

  <div class="user-card">
    <div class="user-avatar"><?= strtoupper(substr($usuario['nome'] ?? 'C', 0, 1)) ?></div>
    <div>
      <div class="user-name"><?= esc(substr($nomeCompleto, 0, 20)) ?></div>
      <div class="user-tipo">Cliente</div>
    </div>
  </div>

  <nav class="nav">
    <?php
    $tabs = [
      'dashboard' => ['🏠', 'Início'],
      'pedidos'   => ['📦', 'Meus Pedidos'],
      'tickets'   => ['💬', 'Suporte'],
      'perfil'    => ['👤', 'Meu Perfil'],
    ];
    foreach ($tabs as $k => [$icon, $label]) {
      $cls = $tab === $k ? 'active' : '';
      echo "<a href='?tab=$k' class='$cls'><span class='nav-icon'>$icon</span> $label</a>";
    }
    ?>
  </nav>
  <div class="sidebar-footer">
    <a href="Comprar/indexComprar.php" class="logout-link" style="margin-bottom:10px;display:flex;">🛒 Ir às compras</a>
    <a href="login/logout.php" class="logout-link">↩ Sair da conta</a>
  </div>
</aside>

<!-- ── Main ── -->
<main class="main">
  <div class="topbar">
    <div class="page-title"><?= esc($tabs[$tab][0]) ?> <span><?= esc($tabs[$tab][1]) ?></span></div>
    <div class="topbar-right"><?= date('d/m/Y H:i') ?> &nbsp;|&nbsp; <?= esc($usuario['email'] ?? '') ?></div>
  </div>

  <div class="content">

    <?php if ($flash): ?>
    <div class="flash flash-<?= esc($flash['type']) ?>">
      <?= $flash['type'] === 'ok' ? '✔' : ($flash['type'] === 'warn' ? '⚠' : '✖') ?>
      <?= esc($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <?php if ($tab === 'dashboard'): ?>
    <!-- ══════════ DASHBOARD ══════════ -->
    <div class="welcome-banner">
      <div class="welcome-text">
        <h2>Olá, <?= esc($usuario['nome'] ?? 'Cliente') ?>! 👋</h2>
        <p>Bem-vindo ao seu painel. Acompanhe seus pedidos e gerencie sua conta.</p>
      </div>
      <div class="welcome-actions">
        <a href="Comprar/indexComprar.php" class="btn btn-primary">🛒 Comprar Peças</a>
        <a href="?tab=pedidos" class="btn btn-outline">📦 Ver Pedidos</a>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total de Pedidos</div>
        <div class="stat-val"><?= $data['total_pedidos'] ?></div>
        <div class="stat-sub">desde o cadastro</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Em Andamento</div>
        <div class="stat-val" style="color: var(--yellow)"><?= $data['pedidos_andamento'] ?></div>
        <div class="stat-sub">pedidos ativos</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Investido</div>
        <div class="stat-val" style="font-size:20px; color: var(--green)">R$ <?= number_format($data['total_gasto'], 2, ',', '.') ?></div>
        <div class="stat-sub">em peças automotivas</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Tickets Abertos</div>
        <div class="stat-val" style="color: <?= $data['tickets_abertos'] > 0 ? 'var(--accent)' : 'var(--muted)' ?>"><?= $data['tickets_abertos'] ?></div>
        <div class="stat-sub">suporte ativo</div>
      </div>
    </div>

    <div class="section">
      <div class="section-head">
        <div class="section-title">Últimos Pedidos</div>
        <a href="?tab=pedidos" class="btn btn-outline btn-sm">Ver todos →</a>
      </div>
      <?php if (!empty($data['pedidos_recentes'])): ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Número</th><th>Total</th><th>Status</th><th>Data</th><th>Ação</th></tr></thead>
          <tbody>
          <?php foreach ($data['pedidos_recentes'] as $p): $sc = $statusColors[$p['status']] ?? '#6b7280'; ?>
          <tr>
            <td class="mono" style="color:var(--blue)"><?= esc($p['numero']) ?></td>
            <td class="mono" style="color:var(--green)">R$ <?= number_format($p['total'],2,',','.') ?></td>
            <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15"><?= str_replace('_',' ',esc($p['status'])) ?></span></td>
            <td class="mono" style="color:var(--muted)"><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
            <td><a href="?tab=pedidos" class="btn btn-outline btn-sm">Detalhes</a></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <div class="icon">📦</div>
        <h3>Nenhum pedido ainda</h3>
        <p>Que tal começar comprando sua primeira peça?</p>
        <a href="Comprar/indexComprar.php" class="btn btn-primary" style="margin-top:16px">🛒 Ver Catálogo</a>
      </div>
      <?php endif; ?>
    </div>

    <?php elseif ($tab === 'pedidos'): ?>
    <!-- ══════════ PEDIDOS ══════════ -->
    <?php if (!empty($data['pedidos'])): ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Número</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Rastreamento</th><th>Data</th><th>Ação</th></tr></thead>
        <tbody>
        <?php foreach ($data['pedidos'] as $p): $sc = $statusColors[$p['status']] ?? '#6b7280'; ?>
        <tr>
          <td class="mono" style="color:var(--blue)"><?= esc($p['numero']) ?></td>
          <td>
            <div class="mono" style="color:var(--green)">R$ <?= number_format($p['total'],2,',','.') ?></div>
            <?php if ($p['frete'] > 0): ?>
            <div style="font-size:11px;color:var(--muted)">Frete: R$ <?= number_format($p['frete'],2,',','.') ?></div>
            <?php endif; ?>
          </td>
          <td style="font-size:12px"><?= esc($p['metodo_pagamento'] ?? '—') ?><?= $p['parcelas'] > 1 ? " ({$p['parcelas']}x)" : '' ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15;font-size:9px"><?= str_replace('_',' ',esc($p['status'])) ?></span></td>
          <td>
            <?php if ($p['rastreamento']): ?>
            <span class="rastreio-pill">📦 <?= esc($p['rastreamento']) ?></span>
            <?php else: ?>
            <span style="color:var(--muted);font-size:12px">—</span>
            <?php endif; ?>
          </td>
          <td class="mono" style="color:var(--muted);font-size:11px"><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
          <td>
            <?php if (in_array($p['status'], ['aguardando_pagamento','pagamento_aprovado'])): ?>
            <form method="post" onsubmit="return confirm('Cancelar este pedido?')">
              <input type="hidden" name="acao" value="cancelar_pedido">
              <input type="hidden" name="tab" value="pedidos">
              <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
              <button class="btn btn-danger btn-sm">✕ Cancelar</button>
            </form>
            <?php else: ?>
            <span style="color:var(--muted);font-size:12px">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div class="icon">📦</div>
      <h3>Nenhum pedido encontrado</h3>
      <p>Você ainda não fez nenhum pedido.</p>
      <a href="Comprar/indexComprar.php" class="btn btn-primary" style="margin-top:16px">🛒 Comprar Agora</a>
    </div>
    <?php endif; ?>

    <?php elseif ($tab === 'tickets'): ?>
    <!-- ══════════ SUPORTE ══════════ -->
    <div class="form-card ticket-form">
      <div class="section-title" style="margin-bottom:18px">💬 Abrir Novo Ticket</div>
      <form method="post">
        <input type="hidden" name="acao" value="abrir_ticket">
        <input type="hidden" name="tab" value="tickets">
        <div class="form-grid">
          <div class="form-group">
            <label>Assunto</label>
            <input type="text" name="assunto" placeholder="Descreva brevemente o problema" required>
          </div>
          <div class="form-group">
            <label>Categoria</label>
            <select name="categoria">
              <option value="duvida">Dúvida</option>
              <option value="pedido">Problema com Pedido</option>
              <option value="produto">Produto com Defeito</option>
              <option value="pagamento">Pagamento</option>
              <option value="devolucao">Devolução / Reembolso</option>
              <option value="outro">Outro</option>
            </select>
          </div>
          <div class="form-group full">
            <label>Mensagem</label>
            <textarea name="mensagem" placeholder="Descreva com detalhes..." required></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:12px">📨 Enviar Ticket</button>
      </form>
    </div>

    <?php if (!empty($data['tickets'])): ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Número</th><th>Assunto</th><th>Categoria</th><th>Prioridade</th><th>Status</th><th>Data</th></tr></thead>
        <tbody>
        <?php foreach ($data['tickets'] as $t):
          $pc = $statusColors[$t['prioridade']] ?? '#6b7280';
          $sc = $statusColors[$t['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--accent)"><?= esc($t['numero']) ?></td>
          <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= esc($t['assunto']) ?></td>
          <td style="font-size:12px;color:var(--muted)"><?= esc($t['categoria']) ?></td>
          <td><span class="badge" style="color:<?= $pc ?>;border-color:<?= $pc ?>30;background:<?= $pc ?>15"><?= esc($t['prioridade']) ?></span></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15"><?= esc($t['status']) ?></span></td>
          <td class="mono" style="color:var(--muted);font-size:11px"><?= date('d/m/Y H:i', strtotime($t['criado_em'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="padding:32px">
      <div class="icon">💬</div>
      <h3>Nenhum ticket aberto</h3>
      <p>Abra um ticket acima se precisar de ajuda.</p>
    </div>
    <?php endif; ?>

    <?php elseif ($tab === 'perfil'): ?>
    <!-- ══════════ PERFIL ══════════ -->
    <div class="form-card">
      <div class="section-title" style="margin-bottom:20px">Informações Pessoais</div>
      <form method="post">
        <input type="hidden" name="acao" value="update_perfil">
        <input type="hidden" name="tab" value="perfil">
        <div class="form-grid">
          <div class="form-group">
            <label>Nome</label>
            <input type="text" name="nome" value="<?= esc($usuario['nome'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Sobrenome</label>
            <input type="text" name="sobrenome" value="<?= esc($usuario['sobrenome'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>E-mail</label>
            <input type="email" value="<?= esc($usuario['email'] ?? '') ?>" disabled style="opacity:.5">
          </div>
          <div class="form-group">
            <label>Telefone</label>
            <input type="tel" name="telefone" value="<?= esc($usuario['telefone'] ?? '') ?>" placeholder="(51) 9 9999-9999">
          </div>
          <div class="form-group">
            <label>Membro desde</label>
            <input type="text" value="<?= date('d/m/Y', strtotime($usuario['criado_em'] ?? 'now')) ?>" disabled style="opacity:.5">
          </div>
          <div class="form-group">
            <label>Status da conta</label>
            <input type="text" value="<?= esc($usuario['status'] ?? '') ?>" disabled style="opacity:.5;text-transform:capitalize">
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:16px">💾 Salvar Alterações</button>
      </form>
    </div>

    <?php elseif ($tab === 'enderecos'): ?>
    

    <?php endif; ?>

  </div>
</main>
</div>
<script>
const flash = document.querySelector('.flash');
if (flash) setTimeout(() => { flash.style.transition='opacity .5s'; flash.style.opacity='0'; }, 4000);
</script>
</body>
</html>