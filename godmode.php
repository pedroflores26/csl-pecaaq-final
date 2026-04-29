<?php
// ═══════════════════════════════════════════════════════
//  PeçaAQ — GOD MODE PANEL
//  Painel de administração total. Conecta ao banco pecaaq.
//  ⚠ USE APENAS EM AMBIENTE LOCAL / DESENVOLVIMENTO ⚠
// ═══════════════════════════════════════════════════════

session_start();

// ── Conexão ─────────────────────────────────────────────
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pecaaq';

$conn = @new mysqli($host, $user, $pass, $db);
$dbOk = !$conn->connect_error;
if ($dbOk) $conn->set_charset('utf8mb4');

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

    if ($acao === 'update_usuario') {
        $id    = (int)($_POST['id'] ?? 0);
        $tipo  = $_POST['tipo']   ?? '';
        $status= $_POST['status'] ?? '';
        $email_ver = isset($_POST['email_verificado']) ? 1 : 0;
        $conn->query("UPDATE usuarios SET tipo='$tipo', status='$status', email_verificado=$email_ver, atualizado_em=NOW() WHERE id=$id");
        $msg = "Usuário #$id atualizado.";
    }
    elseif ($acao === 'delete_usuario') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM usuarios WHERE id=$id AND tipo!='admin'");
        $msg = "Usuário #$id removido."; $msgType = 'warn';
    }
    elseif ($acao === 'update_produto') {
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'ativo';
        $preco  = floatval($_POST['preco'] ?? 0);
        $estoque= (int)($_POST['estoque'] ?? 0);
        $dest   = isset($_POST['destaque']) ? 1 : 0;
        $conn->query("UPDATE produtos SET status='$status', preco=$preco, estoque=$estoque, destaque=$dest, atualizado_em=NOW() WHERE id=$id");
        $msg = "Produto #$id atualizado.";
    }
    elseif ($acao === 'delete_produto') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM produtos WHERE id=$id");
        $msg = "Produto #$id removido."; $msgType = 'warn';
    }
    elseif ($acao === 'update_pedido') {
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $rastr  = $conn->real_escape_string($_POST['rastreamento'] ?? '');
        $conn->query("UPDATE pedidos SET status='$status', rastreamento='$rastr', atualizado_em=NOW() WHERE id=$id");
        // log no histórico
        $desc = "Status alterado para '$status' via God Mode.";
        $conn->query("INSERT INTO pedido_historico (pedido_id, status, descricao) VALUES ($id,'$status','$desc')");
        $msg = "Pedido #$id atualizado.";
    }
    elseif ($acao === 'update_empresa') {
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status']  ?? '';
        $verif  = isset($_POST['verificada']) ? 1 : 0;
        $conn->query("UPDATE empresas SET status='$status', verificada=$verif, atualizado_em=NOW() WHERE id=$id");
        $msg = "Empresa #$id atualizada.";
    }
    elseif ($acao === 'save_config') {
        $chave = $conn->real_escape_string($_POST['chave'] ?? '');
        $valor = $conn->real_escape_string($_POST['valor'] ?? '');
        $conn->query("UPDATE configuracoes SET valor='$valor' WHERE chave='$chave'");
        $msg = "Config '$chave' salva.";
    }
    elseif ($acao === 'create_cupom') {
        $cod   = $conn->real_escape_string(strtoupper($_POST['codigo'] ?? ''));
        $tipo  = $_POST['tipo'] ?? 'percentual';
        $valor = floatval($_POST['valor'] ?? 0);
        $valMin= floatval($_POST['valor_minimo'] ?? 0);
        $vDe   = $_POST['valido_de'] ?? null;
        $vAte  = $_POST['valido_ate'] ?? null;
        $vDe   = $vDe ? "'$vDe'" : 'NULL';
        $vAte  = $vAte ? "'$vAte'" : 'NULL';
        $conn->query("INSERT INTO cupons (codigo,tipo,valor,valor_minimo,valido_de,valido_ate) VALUES ('$cod','$tipo',$valor,$valMin,$vDe,$vAte)");
        $msg = "Cupom '$cod' criado.";
    }
    elseif ($acao === 'delete_cupom') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM cupons WHERE id=$id");
        $msg = "Cupom #$id removido."; $msgType = 'warn';
    }
    elseif ($acao === 'close_ticket') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE tickets SET status='fechado', resolvido_em=NOW(), atualizado_em=NOW() WHERE id=$id");
        $msg = "Ticket #$id fechado.";
    }
    elseif ($acao === 'update_repasse') {
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $conn->query("UPDATE repasses SET status='$status' WHERE id=$id");
        $msg = "Repasse #$id atualizado.";
    }
    elseif ($acao === 'sql_exec') {
        $sql_raw = trim($_POST['sql_raw'] ?? '');
        if ($sql_raw) {
            $res = $conn->query($sql_raw);
            if ($conn->error) { $msg = "Erro SQL: " . esc($conn->error); $msgType = 'err'; }
            elseif (is_object($res)) { $_SESSION['sql_result'] = $res->fetch_all(MYSQLI_ASSOC); $msg = "Consulta executada (" . $res->num_rows . " linhas)."; }
            else { $msg = "Executado. Linhas afetadas: " . $conn->affected_rows; }
        }
    }

    if ($msg) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $msgType]; header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=' . ($_POST['tab'] ?? 'dashboard')); exit; }
}

// flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── Dados para a aba ativa ────────────────────────────────
$tab = $_GET['tab'] ?? 'dashboard';

// ============================================================
//  COLETA DE DADOS POR ABA
// ============================================================
$data = [];

if ($dbOk) {
    if ($tab === 'dashboard') {
        $data['total_usuarios']  = scalar($conn, 'SELECT COUNT(*) FROM usuarios');
        $data['total_produtos']  = scalar($conn, 'SELECT COUNT(*) FROM produtos');
        $data['total_pedidos']   = scalar($conn, 'SELECT COUNT(*) FROM pedidos');
        $data['total_empresas']  = scalar($conn, 'SELECT COUNT(*) FROM empresas');
        $data['receita_total']   = scalar($conn, "SELECT SUM(total) FROM pedidos WHERE status NOT IN ('cancelado','devolvido','reembolsado')") ?? 0;
        $data['pedidos_pend']    = scalar($conn, "SELECT COUNT(*) FROM pedidos WHERE status='aguardando_pagamento'");
        $data['tickets_abertos'] = scalar($conn, "SELECT COUNT(*) FROM tickets WHERE status='aberto'");
        $data['estoque_critico'] = scalar($conn, 'SELECT COUNT(*) FROM vw_estoque_critico');
        $data['pedidos_recentes']= rows($conn, 'SELECT p.numero, p.total, p.status, p.criado_em, u.nome, u.email FROM pedidos p JOIN usuarios u ON u.id=p.usuario_id ORDER BY p.criado_em DESC LIMIT 10');
        $data['prods_destaque']  = rows($conn, "SELECT nome, preco, estoque, status FROM produtos WHERE destaque=1 LIMIT 5");
    }
    elseif ($tab === 'usuarios') {
        $busca = $conn->real_escape_string($_GET['busca'] ?? '');
        $where = $busca ? "WHERE u.nome LIKE '%$busca%' OR u.email LIKE '%$busca%'" : '';
        $data['usuarios'] = rows($conn, "SELECT u.id, u.nome, u.sobrenome, u.email, u.tipo, u.status, u.email_verificado, u.criado_em, u.ultimo_login FROM usuarios u $where ORDER BY u.criado_em DESC LIMIT 200");
    }
    elseif ($tab === 'produtos') {
        $busca = $conn->real_escape_string($_GET['busca'] ?? '');
        $where = $busca ? "WHERE p.nome LIKE '%$busca%' OR p.sku LIKE '%$busca%'" : '';
        $data['produtos'] = rows($conn, "SELECT p.id, p.nome, p.sku, p.preco, p.estoque, p.status, p.destaque, p.avaliacao_media, p.total_vendas, c.nome cat, m.nome marca, e.nome_fantasia empresa FROM produtos p LEFT JOIN categorias c ON c.id=p.categoria_id LEFT JOIN marcas m ON m.id=p.marca_id LEFT JOIN empresas e ON e.id=p.empresa_id $where ORDER BY p.criado_em DESC LIMIT 200");
    }
    elseif ($tab === 'pedidos') {
        $busca = $conn->real_escape_string($_GET['busca'] ?? '');
        $where = $busca ? "WHERE p.numero LIKE '%$busca%' OR u.email LIKE '%$busca%'" : '';
        $data['pedidos'] = rows($conn, "SELECT p.id, p.numero, p.status, p.total, p.subtotal, p.frete, p.desconto, p.metodo_pagamento, p.parcelas, p.rastreamento, p.criado_em, u.nome, u.email FROM pedidos p JOIN usuarios u ON u.id=p.usuario_id $where ORDER BY p.criado_em DESC LIMIT 200");
    }
    elseif ($tab === 'empresas') {
        $data['empresas'] = rows($conn, "SELECT e.id, e.razao_social, e.nome_fantasia, e.cnpj, e.status, e.verificada, e.avaliacao_media, e.total_vendas, e.criado_em, u.email FROM empresas e JOIN usuarios u ON u.id=e.usuario_id ORDER BY e.criado_em DESC LIMIT 200");
    }
    elseif ($tab === 'cupons') {
        $data['cupons'] = rows($conn, "SELECT * FROM cupons ORDER BY criado_em DESC");
    }
    elseif ($tab === 'tickets') {
        $data['tickets'] = rows($conn, "SELECT t.id, t.numero, t.assunto, t.categoria, t.prioridade, t.status, t.criado_em, u.nome, u.email FROM tickets t JOIN usuarios u ON u.id=t.usuario_id ORDER BY FIELD(t.prioridade,'urgente','alta','media','baixa'), t.criado_em DESC LIMIT 200");
    }
    elseif ($tab === 'repasses') {
        $data['repasses'] = rows($conn, "SELECT r.id, r.valor_bruto, r.valor_taxa, r.valor_liquido, r.taxa_plat, r.status, r.criado_em, e.nome_fantasia FROM repasses r JOIN empresas e ON e.id=r.empresa_id ORDER BY r.criado_em DESC LIMIT 200");
    }
    elseif ($tab === 'config') {
        $data['configs'] = rows($conn, "SELECT * FROM configuracoes ORDER BY grupo, chave");
    }
    elseif ($tab === 'audit') {
        $data['logs'] = rows($conn, "SELECT a.id, a.acao, a.tabela, a.registro_id, a.ip, a.criado_em, u.nome, u.email FROM audit_logs a LEFT JOIN usuarios u ON u.id=a.usuario_id ORDER BY a.criado_em DESC LIMIT 300");
    }
    elseif ($tab === 'sql') {
        $data['sql_result'] = $_SESSION['sql_result'] ?? null;
        unset($_SESSION['sql_result']);
    }
}

// Status badges
$statusColors = [
    'ativo'=>'#22c55e','inativo'=>'#6b7280','suspenso'=>'#ef4444','pendente'=>'#f59e0b',
    'aprovada'=>'#22c55e','rejeitada'=>'#ef4444',
    'aguardando_pagamento'=>'#f59e0b','pagamento_aprovado'=>'#3b82f6',
    'em_separacao'=>'#8b5cf6','enviado'=>'#06b6d4','entregue'=>'#22c55e',
    'cancelado'=>'#ef4444','devolvido'=>'#f97316','reembolsado'=>'#ec4899',
    'aberto'=>'#ef4444','em_atendimento'=>'#f59e0b','resolvido'=>'#22c55e','fechado'=>'#6b7280',
    'pago'=>'#22c55e','processando'=>'#3b82f6','estornado'=>'#ef4444',
    'cliente'=>'#06b6d4','empresa'=>'#8b5cf6','admin'=>'#ef4444',
    'verificada'=>'#22c55e','pendente_ver'=>'#f59e0b',
    'urgente'=>'#ef4444','alta'=>'#f97316','media'=>'#f59e0b','baixa'=>'#6b7280',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>⚡ GOD MODE — PeçaAQ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg:      #050a0e;
  --surface: #0a1520;
  --panel:   #0d1e2e;
  --border:  #1a3a52;
  --accent:  #00d4ff;
  --accent2: #ff6b35;
  --green:   #00ff88;
  --red:     #ff3860;
  --yellow:  #ffd600;
  --text:    #c8e6f0;
  --muted:   #4a7a96;
  --mono:    'Share Tech Mono', monospace;
  --sans:    'Rajdhani', sans-serif;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--sans);
  font-size: 15px;
  min-height: 100vh;
  background-image:
    radial-gradient(ellipse 80% 60% at 50% -20%, rgba(0,212,255,.08) 0%, transparent 60%),
    repeating-linear-gradient(0deg, transparent, transparent 39px, rgba(0,212,255,.03) 40px),
    repeating-linear-gradient(90deg, transparent, transparent 39px, rgba(0,212,255,.03) 40px);
}

/* ── Scanline overlay ── */
body::before {
  content:''; position:fixed; inset:0; pointer-events:none; z-index:9999;
  background: repeating-linear-gradient(0deg, rgba(0,0,0,.04) 0px, rgba(0,0,0,.04) 1px, transparent 1px, transparent 3px);
}

/* ── Layout ── */
.layout { display: flex; min-height: 100vh; }

/* ── Sidebar ── */
.sidebar {
  width: 220px; flex-shrink:0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  position: sticky; top: 0; height: 100vh;
}
.logo {
  padding: 24px 20px 16px;
  border-bottom: 1px solid var(--border);
}
.logo-top { font-family: var(--mono); font-size: 11px; color: var(--muted); letter-spacing: 3px; }
.logo-main {
  font-family: var(--mono); font-size: 22px; font-weight: bold;
  color: var(--accent);
  text-shadow: 0 0 20px rgba(0,212,255,.6), 0 0 40px rgba(0,212,255,.3);
  animation: pulse-glow 2s ease-in-out infinite;
}
.logo-sub { font-size: 11px; color: var(--accent2); font-family: var(--mono); letter-spacing: 2px; margin-top: 2px; }
@keyframes pulse-glow {
  0%,100% { text-shadow: 0 0 20px rgba(0,212,255,.6), 0 0 40px rgba(0,212,255,.3); }
  50% { text-shadow: 0 0 30px rgba(0,212,255,.9), 0 0 60px rgba(0,212,255,.5); }
}

.nav { flex:1; padding: 12px 0; overflow-y: auto; }
.nav a {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 20px;
  color: var(--muted);
  text-decoration: none;
  font-family: var(--sans); font-weight: 600; font-size: 13px;
  letter-spacing: 1px; text-transform: uppercase;
  transition: all .15s;
  border-left: 3px solid transparent;
  position: relative;
}
.nav a:hover { color: var(--text); background: rgba(0,212,255,.05); }
.nav a.active {
  color: var(--accent); border-left-color: var(--accent);
  background: rgba(0,212,255,.08);
}
.nav a.active::after {
  content:''; position:absolute; right:0; top:0; bottom:0; width:3px;
  background: linear-gradient(to bottom, transparent, var(--accent), transparent);
}
.nav-icon { font-size: 16px; width: 20px; text-align:center; }

.sidebar-footer {
  padding: 14px 20px;
  border-top: 1px solid var(--border);
  font-family: var(--mono); font-size: 10px; color: var(--muted);
}
.db-status { display: flex; align-items: center; gap: 6px; }
.dot { width:8px; height:8px; border-radius:50%; }
.dot-ok { background: var(--green); box-shadow: 0 0 8px var(--green); animation: blink 2s infinite; }
.dot-err { background: var(--red); }
@keyframes blink { 0%,100%{ opacity:1; } 50%{ opacity:.4; } }

/* ── Main ── */
.main { flex:1; overflow-x: hidden; }

.topbar {
  padding: 16px 28px;
  border-bottom: 1px solid var(--border);
  background: rgba(10,21,32,.8);
  backdrop-filter: blur(10px);
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 100;
}
.page-title { font-size: 20px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
.page-title span { color: var(--accent); }
.topbar-right { font-family: var(--mono); font-size: 11px; color: var(--muted); }

.content { padding: 24px 28px; }

/* ── Flash ── */
.flash {
  padding: 12px 18px; margin-bottom: 20px; border-radius: 4px;
  font-family: var(--mono); font-size: 13px; font-weight: bold;
  display: flex; align-items: center; gap: 10px;
  border: 1px solid;
}
.flash-ok  { background: rgba(0,255,136,.08); border-color: var(--green); color: var(--green); }
.flash-warn{ background: rgba(255,214,0,.08); border-color: var(--yellow); color: var(--yellow); }
.flash-err { background: rgba(255,56,96,.08); border-color: var(--red); color: var(--red); }

/* ── Cards de stats ── */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card {
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 20px;
  position: relative; overflow: hidden;
  transition: border-color .2s;
}
.stat-card:hover { border-color: var(--accent); }
.stat-card::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background: linear-gradient(90deg, transparent, var(--accent), transparent);
}
.stat-label { font-size: 11px; color: var(--muted); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px; }
.stat-val { font-family: var(--mono); font-size: 28px; color: var(--accent); font-weight: bold; }
.stat-sub { font-size: 11px; color: var(--muted); margin-top: 4px; }

/* ── Tables ── */
.section { margin-bottom: 32px; }
.section-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 14px;
}
.section-title {
  font-size: 13px; font-weight: 700; letter-spacing: 3px;
  text-transform: uppercase; color: var(--accent);
  display: flex; align-items: center; gap: 8px;
}
.section-title::before { content: '//'; color: var(--muted); }

.search-box {
  background: var(--surface); border: 1px solid var(--border); color: var(--text);
  padding: 7px 14px; border-radius: 4px; font-family: var(--mono); font-size: 13px;
  width: 260px; outline: none;
  transition: border-color .2s;
}
.search-box:focus { border-color: var(--accent); }

.table-wrap { overflow-x: auto; border-radius: 6px; border: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; }
thead tr { background: var(--surface); }
th {
  padding: 10px 12px; text-align: left;
  font-family: var(--mono); font-size: 11px; color: var(--muted);
  letter-spacing: 2px; text-transform: uppercase;
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
}
td {
  padding: 10px 12px; border-bottom: 1px solid rgba(26,58,82,.4);
  font-size: 13px; vertical-align: middle;
}
tbody tr:hover { background: rgba(0,212,255,.03); }
tbody tr:last-child td { border-bottom: none; }

.badge {
  display: inline-block; padding: 2px 8px; border-radius: 3px;
  font-family: var(--mono); font-size: 10px; font-weight: bold;
  letter-spacing: 1px; text-transform: uppercase;
  border: 1px solid;
}

.mono { font-family: var(--mono); font-size: 12px; }

/* ── Forms / Modals ── */
.btn {
  padding: 6px 14px; border-radius: 3px; border: 1px solid;
  cursor: pointer; font-family: var(--sans); font-weight: 600;
  font-size: 12px; letter-spacing: 1px; text-transform: uppercase;
  transition: all .15s; text-decoration: none; display: inline-block;
}
.btn-primary { background: rgba(0,212,255,.1); border-color: var(--accent); color: var(--accent); }
.btn-primary:hover { background: rgba(0,212,255,.2); }
.btn-danger  { background: rgba(255,56,96,.1); border-color: var(--red); color: var(--red); }
.btn-danger:hover  { background: rgba(255,56,96,.2); }
.btn-warn    { background: rgba(255,214,0,.1); border-color: var(--yellow); color: var(--yellow); }
.btn-warn:hover    { background: rgba(255,214,0,.2); }
.btn-green   { background: rgba(0,255,136,.1); border-color: var(--green); color: var(--green); }
.btn-green:hover   { background: rgba(0,255,136,.2); }
.btn-sm { padding: 4px 10px; font-size: 11px; }

/* inline edit form */
.edit-row { display: none; }
.edit-row.open { display: table-row; }
.edit-row td { background: rgba(0,212,255,.04); padding: 14px 12px; }
.edit-form { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
.field { display: flex; flex-direction: column; gap: 4px; }
.field label { font-family: var(--mono); font-size: 10px; color: var(--muted); letter-spacing: 2px; text-transform: uppercase; }
.field input, .field select {
  background: var(--surface); border: 1px solid var(--border); color: var(--text);
  padding: 6px 10px; border-radius: 3px; font-family: var(--mono); font-size: 12px;
  outline: none; min-width: 120px;
}
.field input:focus, .field select:focus { border-color: var(--accent); }
.field input[type=checkbox] { width: auto; }

/* SQL terminal */
.sql-terminal {
  background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 20px;
}
.sql-input {
  width: 100%; background: var(--bg); border: 1px solid var(--border);
  color: var(--green); font-family: var(--mono); font-size: 14px;
  padding: 16px; border-radius: 4px; resize: vertical; min-height: 120px;
  outline: none; margin-bottom: 12px;
}
.sql-input:focus { border-color: var(--accent); }
.sql-result-table { margin-top: 20px; }

/* Config grid */
.config-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.config-card {
  background: var(--panel); border: 1px solid var(--border); border-radius: 6px; padding: 16px;
}
.config-key { font-family: var(--mono); font-size: 11px; color: var(--muted); margin-bottom: 6px; }
.config-val-input {
  width: 100%; background: var(--surface); border: 1px solid var(--border);
  color: var(--text); padding: 7px 10px; border-radius: 3px; font-family: var(--mono); font-size: 13px;
  outline: none; margin-bottom: 8px;
}
.config-val-input:focus { border-color: var(--accent); }

/* Create form */
.create-form {
  background: var(--panel); border: 1px solid var(--border); border-radius: 6px; padding: 20px; margin-bottom: 20px;
}
.create-form-grid { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; }
.big-field { display: flex; flex-direction: column; gap: 4px; }
.big-field label { font-family: var(--mono); font-size: 10px; color: var(--muted); letter-spacing: 2px; text-transform: uppercase; }
.big-field input, .big-field select {
  background: var(--surface); border: 1px solid var(--border); color: var(--text);
  padding: 8px 12px; border-radius: 3px; font-family: var(--mono); font-size: 13px;
  outline: none; min-width: 130px;
}
.big-field input:focus, .big-field select:focus { border-color: var(--accent); }

/* recent orders */
.recent-table { font-size: 13px; }
.overflow-text { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* no db */
.no-db {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  min-height: 60vh; text-align: center; gap: 16px;
}
.no-db-icon { font-size: 64px; }
.no-db-title { font-family: var(--mono); font-size: 24px; color: var(--red); }
.no-db-msg { color: var(--muted); max-width: 440px; line-height: 1.7; }
.no-db-code { font-family: var(--mono); background: var(--panel); border: 1px solid var(--border); padding: 12px 20px; border-radius: 4px; font-size: 13px; color: var(--green); }

/* Empty state */
.empty { text-align: center; padding: 40px; color: var(--muted); font-family: var(--mono); font-size: 13px; }

/* warn bar */
.warn-bar {
  background: rgba(255,107,53,.08); border: 1px solid rgba(255,107,53,.3);
  padding: 10px 16px; border-radius: 4px; margin-bottom: 20px;
  font-family: var(--mono); font-size: 12px; color: var(--accent2);
  display: flex; align-items: center; gap: 8px;
}
</style>
</head>
<body>
<div class="layout">

<!-- ── Sidebar ── -->
<aside class="sidebar">
  <div class="logo">
    <div class="logo-top">// ACESSO TOTAL</div>
    <div class="logo-main">⚡ GOD MODE</div>
    <div class="logo-sub">PeçaAQ Admin</div>
  </div>
  <nav class="nav">
    <?php
    $tabs = [
      'dashboard' => ['🏠', 'Dashboard'],
      'usuarios'  => ['👥', 'Usuários'],
      'empresas'  => ['🏪', 'Empresas'],
      'produtos'  => ['📦', 'Produtos'],
      'pedidos'   => ['🛒', 'Pedidos'],
      'cupons'    => ['🏷️', 'Cupons'],
      'tickets'   => ['🎫', 'Tickets'],
      'repasses'  => ['💸', 'Repasses'],
      'config'    => ['⚙️', 'Config'],
      'audit'     => ['📋', 'Audit Log'],
      'sql'       => ['💻', 'SQL Livre'],
    ];
    foreach ($tabs as $k => [$icon, $label]) {
      $cls = $tab === $k ? 'active' : '';
      echo "<a href='?tab=$k' class='$cls'><span class='nav-icon'>$icon</span> $label</a>";
    }
    ?>
  </nav>
  <div class="sidebar-footer">
    <div class="db-status">
      <div class="dot <?= $dbOk ? 'dot-ok' : 'dot-err' ?>"></div>
      <?= $dbOk ? "DB: <strong>$db</strong>" : "DB: OFFLINE" ?>
    </div>
    <div style="margin-top:6px; color: #2a5a7a;">pecaaq_db @ <?= $host ?></div>
  </div>
</aside>

<!-- ── Main ── -->
<main class="main">
 <div class="topbar">
    <div class="page-title">
        <?= esc($tabs[$tab][0]) ?> <span><?= esc($tabs[$tab][1]) ?></span>
    </div>

    <div class="topbar-right">
        <a href="login/indexLogin.php" class="btn btn-danger">
            ← Voltar para Login
        </a>

        &nbsp;&nbsp;&nbsp;

        <?= date('d/m/Y H:i:s') ?> &nbsp;|&nbsp; GOD MODE v1.0
    </div>
</div>
  <div class="content">

    <?php if ($flash): ?>
    <div class="flash flash-<?= esc($flash['type']) ?>">
      <?= $flash['type'] === 'ok' ? '✔' : ($flash['type'] === 'warn' ? '⚠' : '✖') ?>
      <?= esc($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <?php if (!$dbOk): ?>
    <div class="no-db">
      <div class="no-db-icon">💀</div>
      <div class="no-db-title">BANCO INACESSÍVEL</div>
      <div class="no-db-msg">Não foi possível conectar ao banco de dados. Verifique se o MySQL/MariaDB está rodando e o banco <strong>pecaaq</strong> existe.</div>
      <div class="no-db-code">Host: <?= $host ?> | DB: <?= $db ?> | User: <?= $user ?></div>
      <div class="no-db-msg" style="font-size:12px;color:#1a3a52;">Edite as variáveis $host/$user/$pass/$db no topo deste arquivo para alterar a conexão.</div>
    </div>

    <?php elseif ($tab === 'dashboard'): ?>
    <!-- ═══════════════ DASHBOARD ═══════════════ -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Usuários</div>
        <div class="stat-val"><?= number_format($data['total_usuarios']) ?></div>
        <div class="stat-sub">cadastrados</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Produtos</div>
        <div class="stat-val"><?= number_format($data['total_produtos']) ?></div>
        <div class="stat-sub">no catálogo</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Pedidos</div>
        <div class="stat-val"><?= number_format($data['total_pedidos']) ?></div>
        <div class="stat-sub"><?= $data['pedidos_pend'] ?> aguardando</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Empresas</div>
        <div class="stat-val"><?= number_format($data['total_empresas']) ?></div>
        <div class="stat-sub">fornecedores</div>
      </div>
      <div class="stat-card" style="--accent: #00ff88;">
        <div class="stat-label">Receita Total</div>
        <div class="stat-val" style="color:var(--green); font-size:22px;">R$ <?= number_format($data['receita_total'], 2, ',', '.') ?></div>
        <div class="stat-sub">pedidos aprovados</div>
      </div>
      <div class="stat-card" style="border-color: <?= $data['tickets_abertos'] > 0 ? '#ef4444' : 'var(--border)' ?>;">
        <div class="stat-label">Tickets Abertos</div>
        <div class="stat-val" style="color:<?= $data['tickets_abertos'] > 0 ? 'var(--red)' : 'var(--muted)' ?>"><?= $data['tickets_abertos'] ?></div>
        <div class="stat-sub">suporte pendente</div>
      </div>
      <div class="stat-card" style="border-color: <?= $data['estoque_critico'] > 0 ? '#f59e0b' : 'var(--border)' ?>;">
        <div class="stat-label">Estoque Crítico</div>
        <div class="stat-val" style="color:<?= $data['estoque_critico'] > 0 ? 'var(--yellow)' : 'var(--muted)' ?>"><?= $data['estoque_critico'] ?></div>
        <div class="stat-sub">produtos abaixo do mínimo</div>
      </div>
    </div>

    <div class="section">
      <div class="section-head"><div class="section-title">Pedidos Recentes</div></div>
      <div class="table-wrap">
        <table class="recent-table">
          <thead><tr><th>Número</th><th>Cliente</th><th>Email</th><th>Total</th><th>Status</th><th>Data</th></tr></thead>
          <tbody>
          <?php foreach ($data['pedidos_recentes'] as $p): $sc = $statusColors[$p['status']] ?? '#6b7280'; ?>
          <tr>
            <td class="mono"><?= esc($p['numero']) ?></td>
            <td><?= esc($p['nome']) ?></td>
            <td class="mono" style="color:var(--muted)"><?= esc($p['email']) ?></td>
            <td class="mono" style="color:var(--green)">R$ <?= number_format($p['total'],2,',','.') ?></td>
            <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15"><?= esc($p['status']) ?></span></td>
            <td class="mono" style="color:var(--muted)"><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php elseif ($tab === 'usuarios'): ?>
    <!-- ═══════════════ USUÁRIOS ═══════════════ -->
    <form method="get" style="margin-bottom:16px;display:flex;gap:10px;align-items:center;">
      <input type="hidden" name="tab" value="usuarios">
      <input class="search-box" type="text" name="busca" placeholder="Buscar por nome ou e-mail..." value="<?= esc($_GET['busca']??'') ?>">
      <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Status</th><th>Verificado</th><th>Cadastro</th><th>Último Login</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['usuarios'] as $u):
          $tc = $statusColors[$u['tipo']] ?? '#6b7280';
          $sc = $statusColors[$u['status']] ?? '#6b7280';
        ?>
        <tr id="row-u-<?= $u['id'] ?>">
          <td class="mono" style="color:var(--muted)">#<?= $u['id'] ?></td>
          <td><strong><?= esc($u['nome']) ?> <?= esc($u['sobrenome']) ?></strong></td>
          <td class="mono" style="color:var(--muted);font-size:12px"><?= esc($u['email']) ?></td>
          <td><span class="badge" style="color:<?= $tc ?>;border-color:<?= $tc ?>20;background:<?= $tc ?>15"><?= esc($u['tipo']) ?></span></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15"><?= esc($u['status']) ?></span></td>
          <td style="text-align:center"><?= $u['email_verificado'] ? '✅' : '❌' ?></td>
          <td class="mono" style="color:var(--muted);font-size:11px"><?= date('d/m/y', strtotime($u['criado_em'])) ?></td>
          <td class="mono" style="color:var(--muted);font-size:11px"><?= $u['ultimo_login'] ? date('d/m/y H:i', strtotime($u['ultimo_login'])) : '—' ?></td>
          <td style="white-space:nowrap">
            <button class="btn btn-primary btn-sm" onclick="toggleEdit('eu-<?= $u['id'] ?>')">✏ Editar</button>
            <?php if ($u['tipo'] !== 'admin'): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Deletar usuário?')">
              <input type="hidden" name="acao" value="delete_usuario">
              <input type="hidden" name="tab" value="usuarios">
              <input type="hidden" name="id" value="<?= $u['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <tr class="edit-row" id="eu-<?= $u['id'] ?>">
          <td colspan="9">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_usuario">
              <input type="hidden" name="tab" value="usuarios">
              <input type="hidden" name="id" value="<?= $u['id'] ?>">
              <div class="field"><label>Tipo</label>
                <select name="tipo">
                  <?php foreach (['cliente','empresa','admin'] as $t): ?>
                  <option value="<?= $t ?>" <?= $u['tipo']===$t?'selected':'' ?>><?= $t ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['ativo','inativo','suspenso','pendente'] as $s): ?>
                  <option value="<?= $s ?>" <?= $u['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field" style="flex-direction:row;align-items:center;gap:6px;margin-top:18px;">
                <input type="checkbox" name="email_verificado" <?= $u['email_verificado']?'checked':'' ?>>
                <label>Email verificado</label>
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-sm" style="margin-top:18px;border-color:var(--muted);color:var(--muted)" onclick="toggleEdit('eu-<?= $u['id'] ?>')">✕ Fechar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['usuarios'])): ?><tr><td colspan="9" class="empty">// nenhum usuário encontrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'empresas'): ?>
    <!-- ═══════════════ EMPRESAS ═══════════════ -->
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Fantasia</th><th>CNPJ</th><th>Email</th><th>Status</th><th>Verificada</th><th>Avaliação</th><th>Vendas</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['empresas'] as $e):
          $sc = $statusColors[$e['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--muted)">#<?= $e['id'] ?></td>
          <td><strong><?= esc($e['nome_fantasia']) ?></strong><br><span style="font-size:11px;color:var(--muted)"><?= esc($e['razao_social']) ?></span></td>
          <td class="mono" style="font-size:12px"><?= esc($e['cnpj']) ?></td>
          <td class="mono" style="color:var(--muted);font-size:12px"><?= esc($e['email']) ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15"><?= esc($e['status']) ?></span></td>
          <td style="text-align:center"><?= $e['verificada'] ? '✅' : '❌' ?></td>
          <td class="mono"><?= number_format($e['avaliacao_media'],1) ?> ⭐</td>
          <td class="mono"><?= $e['total_vendas'] ?></td>
          <td>
            <button class="btn btn-primary btn-sm" onclick="toggleEdit('ee-<?= $e['id'] ?>')">✏ Editar</button>
          </td>
        </tr>
        <tr class="edit-row" id="ee-<?= $e['id'] ?>">
          <td colspan="9">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_empresa">
              <input type="hidden" name="tab" value="empresas">
              <input type="hidden" name="id" value="<?= $e['id'] ?>">
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['pendente','aprovada','suspensa','rejeitada'] as $s): ?>
                  <option value="<?= $s ?>" <?= $e['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field" style="flex-direction:row;align-items:center;gap:6px;margin-top:18px;">
                <input type="checkbox" name="verificada" <?= $e['verificada']?'checked':'' ?>>
                <label>Verificada</label>
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-sm" style="margin-top:18px;border-color:var(--muted);color:var(--muted)" onclick="toggleEdit('ee-<?= $e['id'] ?>')">✕</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'produtos'): ?>
    <!-- ═══════════════ PRODUTOS ═══════════════ -->
    <form method="get" style="margin-bottom:16px;display:flex;gap:10px;">
      <input type="hidden" name="tab" value="produtos">
      <input class="search-box" type="text" name="busca" placeholder="Buscar por nome ou SKU..." value="<?= esc($_GET['busca']??'') ?>">
      <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Nome</th><th>SKU</th><th>Categoria</th><th>Marca</th><th>Empresa</th><th>Preço</th><th>Estoque</th><th>Status</th><th>⭐</th><th>Vendas</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['produtos'] as $p):
          $sc = $statusColors[$p['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--muted)">#<?= $p['id'] ?></td>
          <td class="overflow-text" style="max-width:160px"><?= esc($p['nome']) ?><?= $p['destaque'] ? ' 🌟' : '' ?></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= esc($p['sku']) ?></td>
          <td style="font-size:12px"><?= esc($p['cat']) ?></td>
          <td style="font-size:12px"><?= esc($p['marca']) ?></td>
          <td style="font-size:12px;color:var(--muted)"><?= esc($p['empresa']) ?></td>
          <td class="mono" style="color:var(--green)">R$ <?= number_format($p['preco'],2,',','.') ?></td>
          <td class="mono" style="color:<?= $p['estoque'] <= 5 ? 'var(--red)' : 'var(--text)' ?>"><?= $p['estoque'] ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15"><?= esc($p['status']) ?></span></td>
          <td class="mono" style="font-size:12px"><?= number_format($p['avaliacao_media'],1) ?></td>
          <td class="mono"><?= $p['total_vendas'] ?></td>
          <td style="white-space:nowrap">
            <button class="btn btn-primary btn-sm" onclick="toggleEdit('ep-<?= $p['id'] ?>')">✏</button>
            <form method="post" style="display:inline" onsubmit="return confirm('Deletar produto?')">
              <input type="hidden" name="acao" value="delete_produto">
              <input type="hidden" name="tab" value="produtos">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑</button>
            </form>
          </td>
        </tr>
        <tr class="edit-row" id="ep-<?= $p['id'] ?>">
          <td colspan="12">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_produto">
              <input type="hidden" name="tab" value="produtos">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['ativo','inativo','rascunho','pendente'] as $s): ?>
                  <option value="<?= $s ?>" <?= $p['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field"><label>Preço (R$)</label>
                <input type="number" name="preco" step="0.01" value="<?= $p['preco'] ?>" style="width:100px">
              </div>
              <div class="field"><label>Estoque</label>
                <input type="number" name="estoque" value="<?= $p['estoque'] ?>" style="width:80px">
              </div>
              <div class="field" style="flex-direction:row;align-items:center;gap:6px;margin-top:18px;">
                <input type="checkbox" name="destaque" <?= $p['destaque']?'checked':'' ?>>
                <label>Destaque</label>
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-sm" style="margin-top:18px;border-color:var(--muted);color:var(--muted)" onclick="toggleEdit('ep-<?= $p['id'] ?>')">✕</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['produtos'])): ?><tr><td colspan="12" class="empty">// nenhum produto encontrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'pedidos'): ?>
    <!-- ═══════════════ PEDIDOS ═══════════════ -->
    <form method="get" style="margin-bottom:16px;display:flex;gap:10px;">
      <input type="hidden" name="tab" value="pedidos">
      <input class="search-box" type="text" name="busca" placeholder="Buscar por número ou email..." value="<?= esc($_GET['busca']??'') ?>">
      <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Número</th><th>Cliente</th><th>Subtotal</th><th>Frete</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Rastreio</th><th>Data</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['pedidos'] as $p):
          $sc = $statusColors[$p['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--accent)"><?= esc($p['numero']) ?></td>
          <td><?= esc($p['nome']) ?><br><span style="font-size:11px;color:var(--muted)"><?= esc($p['email']) ?></span></td>
          <td class="mono">R$ <?= number_format($p['subtotal'],2,',','.') ?></td>
          <td class="mono">R$ <?= number_format($p['frete'],2,',','.') ?></td>
          <td class="mono" style="color:var(--green);font-weight:bold">R$ <?= number_format($p['total'],2,',','.') ?></td>
          <td style="font-size:12px"><?= esc($p['metodo_pagamento'] ?? '—') ?><?= $p['parcelas']>1?" ({$p['parcelas']}x)":''; ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15;font-size:9px"><?= str_replace('_',' ', esc($p['status'])) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= esc($p['rastreamento'] ?: '—') ?></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/y H:i', strtotime($p['criado_em'])) ?></td>
          <td>
            <button class="btn btn-primary btn-sm" onclick="toggleEdit('eo-<?= $p['id'] ?>')">✏</button>
          </td>
        </tr>
        <tr class="edit-row" id="eo-<?= $p['id'] ?>">
          <td colspan="10">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_pedido">
              <input type="hidden" name="tab" value="pedidos">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['aguardando_pagamento','pagamento_aprovado','em_separacao','enviado','entregue','cancelado','devolvido','reembolsado'] as $s): ?>
                  <option value="<?= $s ?>" <?= $p['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field"><label>Rastreamento</label>
                <input type="text" name="rastreamento" value="<?= esc($p['rastreamento']) ?>" placeholder="BR123456789">
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-sm" style="margin-top:18px;border-color:var(--muted);color:var(--muted)" onclick="toggleEdit('eo-<?= $p['id'] ?>')">✕</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['pedidos'])): ?><tr><td colspan="10" class="empty">// nenhum pedido encontrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'cupons'): ?>
    <!-- ═══════════════ CUPONS ═══════════════ -->
    <div class="create-form">
      <div class="section-title" style="margin-bottom:14px">➕ Criar Cupom</div>
      <form method="post">
        <input type="hidden" name="acao" value="create_cupom">
        <input type="hidden" name="tab" value="cupons">
        <div class="create-form-grid">
          <div class="big-field"><label>Código</label><input type="text" name="codigo" placeholder="DESCONTO20" required></div>
          <div class="big-field"><label>Tipo</label>
            <select name="tipo">
              <option value="percentual">Percentual (%)</option>
              <option value="fixo">Fixo (R$)</option>
              <option value="frete_gratis">Frete Grátis</option>
            </select>
          </div>
          <div class="big-field"><label>Valor</label><input type="number" name="valor" step="0.01" placeholder="20.00"></div>
          <div class="big-field"><label>Valor Mínimo Pedido</label><input type="number" name="valor_minimo" step="0.01" placeholder="100.00"></div>
          <div class="big-field"><label>Válido De</label><input type="date" name="valido_de"></div>
          <div class="big-field"><label>Válido Até</label><input type="date" name="valido_ate"></div>
          <button type="submit" class="btn btn-green" style="margin-top:20px">⚡ Criar</button>
        </div>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Código</th><th>Tipo</th><th>Valor</th><th>Mínimo</th><th>Uso</th><th>Válido De</th><th>Válido Até</th><th>Ativo</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['cupons'] as $c): ?>
        <tr>
          <td class="mono" style="color:var(--muted)">#<?= $c['id'] ?></td>
          <td class="mono" style="color:var(--accent);font-size:14px;font-weight:bold"><?= esc($c['codigo']) ?></td>
          <td><?= esc($c['tipo']) ?></td>
          <td class="mono"><?= $c['tipo']==='percentual' ? $c['valor'].'%' : ($c['tipo']==='frete_gratis' ? 'Grátis' : 'R$ '.number_format($c['valor'],2,',','.')) ?></td>
          <td class="mono">R$ <?= number_format($c['valor_minimo'],2,',','.') ?></td>
          <td class="mono"><?= $c['uso_atual'] ?><?= $c['uso_maximo'] ? '/'.$c['uso_maximo'] : '/∞' ?></td>
          <td class="mono" style="font-size:11px"><?= $c['valido_de'] ?? '—' ?></td>
          <td class="mono" style="font-size:11px"><?= $c['valido_ate'] ?? '—' ?></td>
          <td style="text-align:center"><?= $c['ativo'] ? '✅' : '❌' ?></td>
          <td>
            <form method="post" style="display:inline" onsubmit="return confirm('Deletar cupom?')">
              <input type="hidden" name="acao" value="delete_cupom">
              <input type="hidden" name="tab" value="cupons">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑 Remover</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['cupons'])): ?><tr><td colspan="10" class="empty">// nenhum cupom cadastrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'tickets'): ?>
    <!-- ═══════════════ TICKETS ═══════════════ -->
    <div class="table-wrap">
      <table>
        <thead><tr><th>Número</th><th>Assunto</th><th>Usuário</th><th>Categoria</th><th>Prioridade</th><th>Status</th><th>Data</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['tickets'] as $t):
          $pc = $statusColors[$t['prioridade']] ?? '#6b7280';
          $sc = $statusColors[$t['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--accent)"><?= esc($t['numero']) ?></td>
          <td class="overflow-text" style="max-width:220px"><?= esc($t['assunto']) ?></td>
          <td><?= esc($t['nome']) ?><br><span style="font-size:11px;color:var(--muted)"><?= esc($t['email']) ?></span></td>
          <td style="font-size:12px"><?= esc($t['categoria']) ?></td>
          <td><span class="badge" style="color:<?= $pc ?>;border-color:<?= $pc ?>20;background:<?= $pc ?>15"><?= esc($t['prioridade']) ?></span></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15"><?= esc($t['status']) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/y H:i', strtotime($t['criado_em'])) ?></td>
          <td>
            <?php if (!in_array($t['status'],['fechado','resolvido'])): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Fechar ticket?')">
              <input type="hidden" name="acao" value="close_ticket">
              <input type="hidden" name="tab" value="tickets">
              <input type="hidden" name="id" value="<?= $t['id'] ?>">
              <button class="btn btn-green btn-sm">✓ Fechar</button>
            </form>
            <?php else: ?>
            <span style="color:var(--muted);font-size:12px">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['tickets'])): ?><tr><td colspan="8" class="empty">// nenhum ticket encontrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'repasses'): ?>
    <!-- ═══════════════ REPASSES ═══════════════ -->
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Empresa</th><th>Bruto</th><th>Taxa (%)</th><th>Taxa (R$)</th><th>Líquido</th><th>Status</th><th>Data</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['repasses'] as $r):
          $sc = $statusColors[$r['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--muted)">#<?= $r['id'] ?></td>
          <td><?= esc($r['nome_fantasia']) ?></td>
          <td class="mono">R$ <?= number_format($r['valor_bruto'],2,',','.') ?></td>
          <td class="mono"><?= $r['taxa_plat'] ?>%</td>
          <td class="mono" style="color:var(--red)">R$ <?= number_format($r['valor_taxa'],2,',','.') ?></td>
          <td class="mono" style="color:var(--green)">R$ <?= number_format($r['valor_liquido'],2,',','.') ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>20;background:<?= $sc ?>15"><?= esc($r['status']) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/y', strtotime($r['criado_em'])) ?></td>
          <td>
            <button class="btn btn-primary btn-sm" onclick="toggleEdit('er-<?= $r['id'] ?>')">✏</button>
          </td>
        </tr>
        <tr class="edit-row" id="er-<?= $r['id'] ?>">
          <td colspan="9">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_repasse">
              <input type="hidden" name="tab" value="repasses">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['pendente','processando','pago','estornado'] as $s): ?>
                  <option value="<?= $s ?>" <?= $r['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-sm" style="margin-top:18px;border-color:var(--muted);color:var(--muted)" onclick="toggleEdit('er-<?= $r['id'] ?>')">✕</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['repasses'])): ?><tr><td colspan="9" class="empty">// nenhum repasse encontrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'config'): ?>
    <!-- ═══════════════ CONFIG ═══════════════ -->
    <?php
    $grupos = [];
    foreach ($data['configs'] as $c) $grupos[$c['grupo']][] = $c;
    foreach ($grupos as $grupo => $cfgs):
    ?>
    <div class="section">
      <div class="section-head"><div class="section-title"><?= esc(strtoupper($grupo)) ?></div></div>
      <div class="config-grid">
        <?php foreach ($cfgs as $c): ?>
        <div class="config-card">
          <div class="config-key"><?= esc($c['chave']) ?></div>
          <?php if ($c['descricao']): ?><div style="font-size:11px;color:var(--muted);margin-bottom:8px"><?= esc($c['descricao']) ?></div><?php endif; ?>
          <form method="post" style="display:flex;gap:8px;align-items:center">
            <input type="hidden" name="acao" value="save_config">
            <input type="hidden" name="tab" value="config">
            <input type="hidden" name="chave" value="<?= esc($c['chave']) ?>">
            <?php if ($c['tipo']==='boolean'): ?>
            <select name="valor" class="config-val-input" style="max-width:120px">
              <option value="1" <?= $c['valor']==='1'?'selected':'' ?>>Ativado</option>
              <option value="0" <?= $c['valor']==='0'?'selected':'' ?>>Desativado</option>
            </select>
            <?php else: ?>
            <input type="<?= $c['tipo']==='number'?'number':'text' ?>" name="valor" value="<?= esc($c['valor']) ?>" step="0.01" class="config-val-input">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary btn-sm">💾</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <?php elseif ($tab === 'audit'): ?>
    <!-- ═══════════════ AUDIT LOG ═══════════════ -->
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Usuário</th><th>Ação</th><th>Tabela</th><th>Registro</th><th>IP</th><th>Data</th></tr></thead>
        <tbody>
        <?php foreach ($data['logs'] as $l): ?>
        <tr>
          <td class="mono" style="color:var(--muted)"><?= $l['id'] ?></td>
          <td style="font-size:12px"><?= esc($l['nome'] ?? 'Sistema') ?><?php if($l['email']): ?><br><span style="color:var(--muted);font-size:11px"><?= esc($l['email']) ?></span><?php endif; ?></td>
          <td class="mono" style="color:var(--accent2)"><?= esc($l['acao']) ?></td>
          <td class="mono" style="font-size:11px"><?= esc($l['tabela'] ?? '—') ?></td>
          <td class="mono" style="color:var(--muted)"><?= $l['registro_id'] ?? '—' ?></td>
          <td class="mono" style="font-size:11px"><?= esc($l['ip'] ?? '—') ?></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/y H:i:s', strtotime($l['criado_em'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['logs'])): ?><tr><td colspan="7" class="empty">// audit log vazio</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'sql'): ?>
    <!-- ═══════════════ SQL LIVRE ═══════════════ -->
    <div class="warn-bar">⚠ Modo SQL Livre — qualquer consulta será executada diretamente no banco. Use com extremo cuidado.</div>
    <div class="sql-terminal">
      <div class="section-title" style="margin-bottom:14px">💻 Console SQL</div>
      <form method="post">
        <input type="hidden" name="acao" value="sql_exec">
        <input type="hidden" name="tab" value="sql">
        <textarea class="sql-input" name="sql_raw" placeholder="SELECT * FROM usuarios LIMIT 10;
-- ou qualquer outro SQL...
-- UPDATE, DELETE, INSERT são permitidos aqui."></textarea>
        <button type="submit" class="btn btn-primary">▶ Executar</button>
      </form>

      <?php if (!empty($data['sql_result'])): ?>
      <div class="sql-result-table">
        <div class="section-title" style="margin-bottom:10px">// Resultado (<?= count($data['sql_result']) ?> linhas)</div>
        <div class="table-wrap">
          <table>
            <thead><tr><?php foreach (array_keys($data['sql_result'][0]) as $k): ?><th><?= esc($k) ?></th><?php endforeach; ?></tr></thead>
            <tbody>
            <?php foreach ($data['sql_result'] as $row): ?>
            <tr><?php foreach ($row as $v): ?><td class="mono" style="font-size:12px"><?= esc((string)$v) ?></td><?php endforeach; ?></tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php elseif ($data['sql_result'] === []): ?>
      <div style="margin-top:16px;font-family:var(--mono);color:var(--muted);font-size:13px">// Nenhum resultado retornado.</div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div><!-- /content -->
</main>
</div>

<script>
function toggleEdit(id) {
  const row = document.getElementById(id);
  if (!row) return;
  row.classList.toggle('open');
}
// Auto-hide flash after 4s
const flash = document.querySelector('.flash');
if (flash) setTimeout(() => { flash.style.opacity='0'; flash.style.transition='opacity .5s'; }, 4000);
</script>
</body>
</html>
