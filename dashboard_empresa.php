<?php
// ═══════════════════════════════════════════════════════
//  PeçaAQ — DASHBOARD DA EMPRESA (FORNECEDOR)
//  Painel de gestão para empresas parceiras.
// ═══════════════════════════════════════════════════════

session_start();

// Proteção de sessão
if (empty($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'empresa') {
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

// Busca empresa do usuário
$empresa = [];
if ($dbOk) {
    $res = q($conn, "SELECT * FROM empresas WHERE usuario_id=?", 'i', $uid);
    $empresa = $res ? $res->fetch_assoc() : [];
}
$eid = (int)($empresa['id'] ?? 0);

// ── Ações POST ───────────────────────────────────────────
$msg = ''; $msgType = 'ok';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dbOk && $eid) {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'create_produto') {
        $nome     = $conn->real_escape_string(trim($_POST['nome'] ?? ''));
        $descricao= $conn->real_escape_string(trim($_POST['descricao'] ?? ''));
        $preco    = floatval(str_replace(',', '.', $_POST['preco'] ?? 0));
        $estoque  = (int)($_POST['estoque'] ?? 0);
        $sku_raw  = trim($_POST['sku'] ?? '') ?: 'SKU-' . uniqid();
        $sku      = $conn->real_escape_string($sku_raw);
        $cat_id   = (int)($_POST['categoria_id'] ?? 1);
        $marca_id = (int)($_POST['marca_id'] ?? 0) ?: 'NULL';
        $slug     = $conn->real_escape_string(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $_POST['nome'] ?? '')) . '-' . uniqid());

        // Upload de imagem
        $imagem = '';
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/Dashboard/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                $fname = uniqid('prod_') . '.' . $ext;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadDir . $fname)) {
                    $imagem = $conn->real_escape_string($fname);
                }
            }
        }

        $marca_val = is_numeric($marca_id) && $marca_id > 0 ? $marca_id : 'NULL';
        $conn->query("INSERT INTO produtos (nome,slug,descricao,preco,estoque,sku,categoria_id,marca_id,empresa_id,imagem_principal,status,criado_em,atualizado_em) VALUES ('$nome','$slug','$descricao',$preco,$estoque,'$sku',$cat_id,$marca_val,$eid,'$imagem','ativo',NOW(),NOW())");
        $msg = "Produto cadastrado com sucesso!";
    }
    elseif ($acao === 'update_produto') {
        $id      = (int)($_POST['id'] ?? 0);
        $preco   = floatval($_POST['preco'] ?? 0);
        $estoque = (int)($_POST['estoque'] ?? 0);
        $status  = $_POST['status'] ?? 'ativo';
        $dest    = isset($_POST['destaque']) ? 1 : 0;
        // Garante que só edita produtos da própria empresa
        $conn->query("UPDATE produtos SET preco=$preco, estoque=$estoque, status='$status', destaque=$dest, atualizado_em=NOW() WHERE id=$id AND empresa_id=$eid");
        $msg = "Produto atualizado.";
    }
    elseif ($acao === 'delete_produto') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM produtos WHERE id=$id AND empresa_id=$eid");
        $msg = "Produto removido."; $msgType = 'warn';
    }
    elseif ($acao === 'update_pedido_status') {
        $pedido_id  = (int)($_POST['pedido_id'] ?? 0);
        $status     = $_POST['status'] ?? '';
        $rastreamento = $conn->real_escape_string($_POST['rastreamento'] ?? '');
        // Só pode atualizar status de envio (não pode aprovar pagamento, por ex.)
        $statusPermitidos = ['em_separacao','enviado','entregue'];
        if (in_array($status, $statusPermitidos)) {
            // Confirma que o pedido tem itens da empresa
            $temItem = scalar($conn, "SELECT COUNT(*) FROM pedido_itens pi JOIN produtos p ON p.id=pi.produto_id WHERE pi.pedido_id=? AND p.empresa_id=?", 'ii', $pedido_id, $eid);
            if ($temItem) {
                $conn->query("UPDATE pedidos SET status='$status', rastreamento='$rastreamento', atualizado_em=NOW() WHERE id=$pedido_id");
                $desc = "Status alterado para '$status' pela empresa.";
                $conn->query("INSERT INTO pedido_historico (pedido_id, status, descricao) VALUES ($pedido_id,'$status','$desc')");
                $msg = "Pedido atualizado.";
            }
        }
    }
    elseif ($acao === 'update_perfil_empresa') {
        $telefone = $conn->real_escape_string($_POST['telefone'] ?? '');
        $descricao= $conn->real_escape_string($_POST['descricao'] ?? '');
        $conn->query("UPDATE empresas SET telefone='$telefone', descricao='$descricao', atualizado_em=NOW() WHERE id=$eid");
        $msg = "Perfil da empresa atualizado.";
    }
    elseif ($acao === 'abrir_ticket') {
        $assunto  = $conn->real_escape_string($_POST['assunto'] ?? '');
        $categoria= $conn->real_escape_string($_POST['categoria'] ?? 'duvida');
        $mensagem = $conn->real_escape_string($_POST['mensagem'] ?? '');
        $numero   = 'TK-' . strtoupper(substr(uniqid(), -8));
        $conn->query("INSERT INTO tickets (numero, usuario_id, assunto, categoria, mensagem, status, prioridade) VALUES ('$numero',$uid,'$assunto','$categoria','$mensagem','aberto','media')");
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
$data = [];

if ($dbOk && $eid) {
    if ($tab === 'dashboard') {
        $data['total_produtos']  = scalar($conn, "SELECT COUNT(*) FROM produtos WHERE empresa_id=?", 'i', $eid) ?? 0;
        $data['produtos_ativos'] = scalar($conn, "SELECT COUNT(*) FROM produtos WHERE empresa_id=? AND status='ativo'", 'i', $eid) ?? 0;
        $data['estoque_critico'] = scalar($conn, "SELECT COUNT(*) FROM produtos WHERE empresa_id=? AND estoque <= 5 AND estoque > 0", 'i', $eid) ?? 0;
        $data['sem_estoque']     = scalar($conn, "SELECT COUNT(*) FROM produtos WHERE empresa_id=? AND estoque = 0", 'i', $eid) ?? 0;
        $data['total_vendas']    = scalar($conn, "SELECT SUM(pi.quantidade) FROM pedido_itens pi JOIN produtos p ON p.id=pi.produto_id WHERE p.empresa_id=?", 'i', $eid) ?? 0;
$data['receita_bruta'] = scalar($conn, "SELECT SUM(pi.total) FROM pedido_itens pi JOIN produtos p ON p.id=pi.produto_id JOIN pedidos pd ON pd.id=pi.pedido_id WHERE p.empresa_id=? AND pd.status NOT IN ('cancelado','devolvido','reembolsado')", 'i', $eid) ?? 0;
        $data['pedidos_recentes']= rows($conn, "SELECT DISTINCT pd.id, pd.numero, pd.status, pd.total, pd.criado_em, u.nome FROM pedidos pd JOIN pedido_itens pi ON pi.pedido_id=pd.id JOIN produtos p ON p.id=pi.produto_id JOIN usuarios u ON u.id=pd.usuario_id WHERE p.empresa_id=? ORDER BY pd.criado_em DESC LIMIT 8", 'i', $eid);
        $data['prods_top']       = rows($conn, "SELECT p.nome, p.preco, p.estoque, p.total_vendas, p.avaliacao_media FROM produtos p WHERE p.empresa_id=? ORDER BY p.total_vendas DESC LIMIT 5", 'i', $eid);
        $data['repasses_pend']   = scalar($conn, "SELECT COUNT(*) FROM repasses WHERE empresa_id=? AND status='pendente'", 'i', $eid) ?? 0;
    }
    elseif ($tab === 'produtos') {
        $busca = $conn->real_escape_string($_GET['busca'] ?? '');
        $extra = $busca ? "AND (p.nome LIKE '%$busca%' OR p.sku LIKE '%$busca%')" : '';
        $data['produtos'] = rows($conn, "SELECT p.id, p.nome, p.sku, p.preco, p.estoque, p.status, p.destaque, p.avaliacao_media, p.total_vendas, c.nome cat, m.nome marca FROM produtos p LEFT JOIN categorias c ON c.id=p.categoria_id LEFT JOIN marcas m ON m.id=p.marca_id WHERE p.empresa_id=$eid $extra ORDER BY p.criado_em DESC LIMIT 200");
        $data['categorias']= rows($conn, "SELECT id, nome FROM categorias ORDER BY nome");
        $data['marcas']    = rows($conn, "SELECT id, nome FROM marcas ORDER BY nome");
    }
    elseif ($tab === 'pedidos') {
        $data['pedidos'] = rows($conn, "SELECT DISTINCT pd.id, pd.numero, pd.status, pd.total, pd.rastreamento, pd.criado_em, u.nome cliente, u.email FROM pedidos pd JOIN pedido_itens pi ON pi.pedido_id=pd.id JOIN produtos p ON p.id=pi.produto_id JOIN usuarios u ON u.id=pd.usuario_id WHERE p.empresa_id=? ORDER BY pd.criado_em DESC LIMIT 200", 'i', $eid);
    }
    elseif ($tab === 'repasses') {
        $data['repasses'] = rows($conn, "SELECT * FROM repasses WHERE empresa_id=? ORDER BY criado_em DESC", 'i', $eid);
        $data['total_liquido'] = scalar($conn, "SELECT SUM(valor_liquido) FROM repasses WHERE empresa_id=? AND status='pago'", 'i', $eid) ?? 0;
    }
    elseif ($tab === 'tickets') {
        $data['tickets'] = rows($conn, "SELECT id, numero, assunto, categoria, prioridade, status, criado_em FROM tickets WHERE usuario_id=? ORDER BY criado_em DESC", 'i', $uid);
    }
    elseif ($tab === 'perfil') {
        // empresa já carregada
    }
}

$statusColors = [
    'ativo'=>'#22c55e','inativo'=>'#6b7280','rascunho'=>'#94a3b8','pendente'=>'#f59e0b',
    'aguardando_pagamento'=>'#f59e0b','pagamento_aprovado'=>'#3b82f6',
    'em_separacao'=>'#8b5cf6','enviado'=>'#06b6d4','entregue'=>'#22c55e',
    'cancelado'=>'#ef4444','devolvido'=>'#f97316','reembolsado'=>'#ec4899',
    'aberto'=>'#ef4444','em_atendimento'=>'#f59e0b','resolvido'=>'#22c55e','fechado'=>'#6b7280',
    'pago'=>'#22c55e','processando'=>'#3b82f6','estornado'=>'#ef4444',
    'urgente'=>'#ef4444','alta'=>'#f97316','media'=>'#f59e0b','baixa'=>'#6b7280',
    'aprovada'=>'#22c55e','rejeitada'=>'#ef4444','suspensa'=>'#ef4444',
];

$nomeEmpresa = $empresa['nome_fantasia'] ?? $empresa['razao_social'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($nomeEmpresa) ?> — Painel PeçaAQ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg:      #060b0f;
  --surface: #0b1520;
  --panel:   #0f1e2e;
  --border:  #1a3350;
  --accent:  #f59e0b;
  --accent2: #e8192c;
  --blue:    #3b82f6;
  --green:   #22c55e;
  --cyan:    #06b6d4;
  --text:    #dde8f0;
  --muted:   #3d6080;
  --mono:    'JetBrains Mono', monospace;
  --sans:    'DM Sans', sans-serif;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--sans);
  font-size: 15px;
  min-height: 100vh;
  background-image:
    radial-gradient(ellipse 60% 40% at 80% 10%, rgba(245,158,11,.05) 0%, transparent 55%),
    radial-gradient(ellipse 50% 40% at 20% 90%, rgba(6,182,212,.04) 0%, transparent 50%),
    repeating-linear-gradient(0deg, transparent, transparent 49px, rgba(26,51,80,.04) 50px),
    repeating-linear-gradient(90deg, transparent, transparent 49px, rgba(26,51,80,.04) 50px);
}

/* ── Layout ── */
.layout { display: flex; min-height: 100vh; }

/* ── Sidebar ── */
.sidebar {
  width: 250px; flex-shrink: 0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  position: sticky; top: 0; height: 100vh;
}
.logo {
  padding: 22px 20px 18px;
  border-bottom: 1px solid var(--border);
}
.logo-brand { font-size: 20px; font-weight: 700; letter-spacing: 2px; color: var(--text); }
.logo-brand em { color: var(--accent2); font-style: normal; }
.logo-empresa {
  margin-top: 10px;
  font-size: 13px; font-weight: 600; color: var(--accent);
  display: flex; align-items: center; gap: 6px;
}
.logo-empresa::before { content: ''; display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 8px var(--accent); animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{ opacity:1; } 50%{ opacity:.5; } }
.logo-cnpj { font-family: var(--mono); font-size: 10px; color: var(--muted); margin-top: 3px; }

.status-bar {
  margin: 12px 16px;
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px;
  border-radius: 6px; border: 1px solid;
  font-size: 11px; font-family: var(--mono);
}
.status-ok  { background: rgba(34,197,94,.06); border-color: rgba(34,197,94,.2); color: var(--green); }
.status-pen { background: rgba(245,158,11,.06); border-color: rgba(245,158,11,.2); color: var(--accent); }
.status-err { background: rgba(232,25,44,.06);  border-color: rgba(232,25,44,.2); color: var(--accent2); }

.nav { flex: 1; padding: 8px 0; overflow-y: auto; }
.nav-group { padding: 8px 20px 4px; font-size: 9px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; font-family: var(--mono); }
.nav a {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 20px;
  color: var(--muted);
  text-decoration: none;
  font-size: 14px; font-weight: 500;
  border-left: 3px solid transparent;
  transition: all .15s;
  position: relative;
}
.nav a:hover { color: var(--text); background: rgba(245,158,11,.04); }
.nav a.active { color: var(--accent); border-left-color: var(--accent); background: rgba(245,158,11,.07); }
.nav-icon { font-size: 16px; width: 20px; text-align: center; }
.nav-badge {
  margin-left: auto; background: var(--accent2); color: #fff;
  font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 10px;
}

.sidebar-footer {
  padding: 14px 20px;
  border-top: 1px solid var(--border);
  display: flex; flex-direction: column; gap: 8px;
}
.sidebar-link {
  display: flex; align-items: center; gap: 8px;
  color: var(--muted); text-decoration: none;
  font-size: 13px; font-weight: 500;
  transition: color .15s;
}
.sidebar-link:hover { color: var(--accent); }

/* ── Main ── */
.main { flex: 1; overflow-x: hidden; }
.topbar {
  padding: 14px 28px;
  border-bottom: 1px solid var(--border);
  background: rgba(6,11,15,.9);
  backdrop-filter: blur(12px);
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 100;
}
.page-title { font-size: 18px; font-weight: 700; }
.page-title span { color: var(--accent); }
.topbar-meta { font-family: var(--mono); font-size: 11px; color: var(--muted); text-align: right; line-height: 1.6; }

.content { padding: 24px 28px; }

/* ── Flash ── */
.flash {
  padding: 12px 18px; margin-bottom: 20px; border-radius: 6px;
  font-size: 14px; display: flex; align-items: center; gap: 10px; border: 1px solid;
}
.flash-ok   { background: rgba(34,197,94,.08); border-color: var(--green); color: var(--green); }
.flash-warn { background: rgba(245,158,11,.08); border-color: var(--accent); color: var(--accent); }
.flash-err  { background: rgba(232,25,44,.08);  border-color: var(--accent2); color: var(--accent2); }

/* ── Stats ── */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card {
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 20px;
  position: relative; overflow: hidden;
  transition: border-color .2s, transform .2s;
}
.stat-card:hover { border-color: var(--accent); transform: translateY(-2px); }
.stat-card .top-bar {
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent, var(--accent), transparent);
}
.stat-label { font-size: 10px; color: var(--muted); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; font-family: var(--mono); }
.stat-val { font-family: var(--mono); font-size: 26px; font-weight: 700; color: var(--accent); }
.stat-sub { font-size: 11px; color: var(--muted); margin-top: 4px; }

/* ── Section ── */
.section { margin-bottom: 28px; }
.section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.section-title { font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: 1.5px; text-transform: uppercase; font-family: var(--mono); display: flex; align-items: center; gap: 8px; }
.section-title::before { content: '⬡'; color: var(--accent); font-size: 10px; }

/* ── Tables ── */
.table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; }
thead tr { background: var(--surface); }
th {
  padding: 10px 14px; text-align: left;
  font-family: var(--mono); font-size: 10px; color: var(--muted);
  letter-spacing: 2px; text-transform: uppercase;
  border-bottom: 1px solid var(--border); white-space: nowrap;
}
td {
  padding: 11px 14px; border-bottom: 1px solid rgba(26,51,80,.4);
  font-size: 13px; vertical-align: middle;
}
tbody tr:hover { background: rgba(245,158,11,.02); }
tbody tr:last-child td { border-bottom: none; }
.mono { font-family: var(--mono); font-size: 12px; }

/* ── Badges ── */
.badge {
  display: inline-block; padding: 2px 9px; border-radius: 4px;
  font-family: var(--mono); font-size: 10px; font-weight: 600;
  letter-spacing: 0.5px; text-transform: uppercase; border: 1px solid;
}

/* ── Buttons ── */
.btn {
  padding: 8px 16px; border-radius: 6px; border: 1px solid;
  cursor: pointer; font-family: var(--sans); font-weight: 600;
  font-size: 13px; transition: all .15s; text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
}
.btn-primary { background: var(--accent); border-color: var(--accent); color: #000; }
.btn-primary:hover { background: #d97706; }
.btn-outline  { background: transparent; border-color: var(--border); color: var(--muted); }
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }
.btn-danger   { background: rgba(232,25,44,.1); border-color: rgba(232,25,44,.4); color: var(--accent2); }
.btn-danger:hover { background: rgba(232,25,44,.2); }
.btn-green    { background: rgba(34,197,94,.1); border-color: rgba(34,197,94,.4); color: var(--green); }
.btn-green:hover { background: rgba(34,197,94,.2); }
.btn-sm { padding: 5px 12px; font-size: 12px; }

/* ── Edit row ── */
.edit-row { display: none; }
.edit-row.open { display: table-row; }
.edit-row td { background: rgba(245,158,11,.03); padding: 14px; }
.edit-form { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
.field { display: flex; flex-direction: column; gap: 4px; }
.field label { font-family: var(--mono); font-size: 10px; color: var(--muted); letter-spacing: 1px; text-transform: uppercase; }
.field input, .field select {
  background: var(--surface); border: 1px solid var(--border);
  color: var(--text); padding: 7px 10px; border-radius: 4px;
  font-family: var(--mono); font-size: 12px; outline: none; min-width: 120px;
}
.field input:focus, .field select:focus { border-color: var(--accent); }

/* ── Form ── */
.form-card {
  background: var(--panel); border: 1px solid var(--border);
  border-radius: 10px; padding: 24px; margin-bottom: 24px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1/-1; }
.form-group label { font-size: 10px; color: var(--muted); letter-spacing: 1.5px; text-transform: uppercase; font-family: var(--mono); }
.form-group input, .form-group select, .form-group textarea {
  background: var(--surface); border: 1px solid var(--border);
  color: var(--text); padding: 10px 14px; border-radius: 6px;
  font-family: var(--sans); font-size: 14px; outline: none;
  transition: border-color .2s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--accent); }
.form-group textarea { resize: vertical; min-height: 90px; }

/* ── Repasse card ── */
.repasse-summary {
  background: var(--panel); border: 1px solid var(--border);
  border-radius: 10px; padding: 20px; margin-bottom: 20px;
  display: flex; gap: 28px; align-items: center; flex-wrap: wrap;
}
.repasse-stat { text-align: center; }
.repasse-stat .val { font-family: var(--mono); font-size: 22px; font-weight: 700; }
.repasse-stat .lbl { font-size: 11px; color: var(--muted); margin-top: 4px; letter-spacing: 1px; }

/* ── Empty ── */
.empty { text-align: center; padding: 40px; color: var(--muted); font-family: var(--mono); font-size: 13px; }

/* ── Aviso empresa não aprovada ── */
.alert-banner {
  background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.3);
  border-radius: 10px; padding: 18px 22px; margin-bottom: 24px;
  display: flex; align-items: center; gap: 14px;
}
.alert-banner .icon { font-size: 28px; }
.alert-banner strong { color: var(--accent); }
.alert-banner p { font-size: 13px; color: var(--muted); margin-top: 4px; }

/* ── Top produtos mini ── */
.mini-table td, .mini-table th { padding: 8px 12px; font-size: 12px; }

/* ── Search ── */
.search-row { display: flex; gap: 10px; margin-bottom: 16px; align-items: center; }
.search-input {
  background: var(--surface); border: 1px solid var(--border);
  color: var(--text); padding: 9px 14px; border-radius: 6px;
  font-family: var(--sans); font-size: 14px; outline: none;
  width: 260px; transition: border-color .2s;
}
.search-input:focus { border-color: var(--accent); }
</style>
</head>
<body>
<div class="layout">

<!-- ── Sidebar ── -->
<aside class="sidebar">
  <div class="logo">
    <div class="logo-brand">PEÇA<em>AQ</em></div>
    <div class="logo-empresa">🏪 <?= esc(substr($nomeEmpresa, 0, 26)) ?></div>
    <div class="logo-cnpj"><?= esc($empresa['cnpj'] ?? '') ?></div>
  </div>

  <?php
  $estStatus = $empresa['status'] ?? 'pendente';
  $verif     = $empresa['verificada'] ?? 0;
  if ($estStatus === 'aprovada' && $verif) {
      echo '<div class="status-bar status-ok">✅ Empresa Verificada</div>';
  } elseif ($estStatus === 'pendente') {
      echo '<div class="status-bar status-pen">⏳ Aprovação Pendente</div>';
  } else {
      echo '<div class="status-bar status-err">⚠ Status: ' . esc($estStatus) . '</div>';
  }
  ?>

  <nav class="nav">
    <div class="nav-group">Gestão</div>
    <?php
    $pedidos_pendentes = $dbOk && $eid ? (scalar($conn, "SELECT COUNT(DISTINCT pd.id) FROM pedidos pd JOIN pedido_itens pi ON pi.pedido_id=pd.id JOIN produtos p ON p.id=pi.produto_id WHERE p.empresa_id=? AND pd.status='pagamento_aprovado'", 'i', $eid) ?? 0) : 0;
    $tabs = [
      'dashboard' => ['📊', 'Visão Geral'],
      'produtos'  => ['📦', 'Meus Produtos'],
      'pedidos'   => ['🛒', 'Pedidos', $pedidos_pendentes],
      'repasses'  => ['💰', 'Financeiro'],
    ];
   foreach ($tabs as $k => $item) {
    [$icon, $label] = $item;
    $badge = $item[2] ?? 0;
        $cls = $tab === $k ? 'active' : '';
        $badgeHtml = !empty($badge) ? "<span class='nav-badge'>$badge</span>" : '';
        echo "<a href='?tab=$k' class='$cls'><span class='nav-icon'>$icon</span> $label $badgeHtml</a>";
    }
    ?>
    <div class="nav-group" style="margin-top:8px">Conta</div>
    <?php
    $tabs2 = [
      'tickets'   => ['💬', 'Suporte'],
      'perfil'    => ['🏢', 'Perfil da Empresa'],
    ];
    foreach ($tabs2 as $k => [$icon, $label]) {
        $cls = $tab === $k ? 'active' : '';
        echo "<a href='?tab=$k' class='$cls'><span class='nav-icon'>$icon</span> $label</a>";
    }
    ?>
  </nav>
  <div class="sidebar-footer">
    <a href="Comprar/indexComprar.php" class="sidebar-link">🔍 Ver catálogo</a>
    <a href="login/logout.php" class="sidebar-link">↩ Sair da conta</a>
  </div>
</aside>

<!-- ── Main ── -->
<main class="main">
  <div class="topbar">
    <div class="page-title">
      <?= ['dashboard'=>'📊','produtos'=>'📦','pedidos'=>'🛒','repasses'=>'💰','tickets'=>'💬','perfil'=>'🏢'][$tab] ?>
      <span><?= ['dashboard'=>'Visão Geral','produtos'=>'Meus Produtos','pedidos'=>'Pedidos','repasses'=>'Financeiro','tickets'=>'Suporte','perfil'=>'Perfil da Empresa'][$tab] ?></span>
    </div>
    <div class="topbar-meta">
      <?= date('d/m/Y H:i') ?><br>
      ⭐ <?= number_format($empresa['avaliacao_media'] ?? 0, 1) ?> · <?= ($empresa['total_vendas'] ?? 0) ?> vendas
    </div>
  </div>

  <div class="content">

    <?php if ($flash): ?>
    <div class="flash flash-<?= esc($flash['type']) ?>">
      <?= $flash['type'] === 'ok' ? '✔' : ($flash['type'] === 'warn' ? '⚠' : '✖') ?>
      <?= esc($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <?php if (!$eid): ?>
    <div class="alert-banner">
      <div class="icon">⚠</div>
      <div>
        <strong>Empresa não encontrada</strong>
        <p>Sua conta de empresa ainda não possui um perfil cadastrado. Entre em contato com o suporte.</p>
      </div>
    </div>

    <?php elseif ($tab === 'dashboard'): ?>
    <!-- ══════════ DASHBOARD ══════════ -->
    <?php if ($estStatus !== 'aprovada'): ?>
    <div class="alert-banner">
      <div class="icon">⏳</div>
      <div>
        <strong>Empresa aguardando aprovação</strong>
        <p>Seu cadastro está sendo analisado pela equipe PeçaAQ. Você será notificado quando aprovado.</p>
      </div>
    </div>
    <?php endif; ?>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="top-bar"></div>
        <div class="stat-label">Total de Produtos</div>
        <div class="stat-val"><?= $data['total_produtos'] ?></div>
        <div class="stat-sub"><?= $data['produtos_ativos'] ?> ativos</div>
      </div>
      <div class="stat-card">
        <div class="top-bar" style="background: linear-gradient(90deg, transparent, var(--accent2), transparent)"></div>
        <div class="stat-label">Estoque Crítico</div>
        <div class="stat-val" style="color: <?= $data['estoque_critico'] > 0 ? 'var(--accent)' : 'var(--muted)' ?>"><?= $data['estoque_critico'] ?></div>
        <div class="stat-sub"><?= $data['sem_estoque'] ?> sem estoque</div>
      </div>
      <div class="stat-card">
        <div class="top-bar" style="background: linear-gradient(90deg, transparent, var(--green), transparent)"></div>
        <div class="stat-label">Total Vendido</div>
        <div class="stat-val" style="color:var(--green)"><?= number_format($data['total_vendas']) ?></div>
        <div class="stat-sub">unidades</div>
      </div>
      <div class="stat-card">
        <div class="top-bar" style="background: linear-gradient(90deg, transparent, var(--cyan), transparent)"></div>
        <div class="stat-label">Receita Bruta</div>
        <div class="stat-val" style="font-size:18px;color:var(--cyan)">R$ <?= number_format($data['receita_bruta'], 2, ',', '.') ?></div>
        <div class="stat-sub"><?= $data['repasses_pend'] ?> repasses pendentes</div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
      <div class="section">
        <div class="section-head">
          <div class="section-title">Pedidos Recentes</div>
          <a href="?tab=pedidos" class="btn btn-outline btn-sm">Ver todos →</a>
        </div>
        <div class="table-wrap">
          <table class="mini-table">
            <thead><tr><th>Número</th><th>Cliente</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($data['pedidos_recentes'] as $p): $sc = $statusColors[$p['status']] ?? '#6b7280'; ?>
            <tr>
              <td class="mono" style="color:var(--cyan)"><?= esc($p['numero']) ?></td>
              <td><?= esc($p['nome']) ?></td>
              <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15;font-size:9px"><?= str_replace('_',' ',esc($p['status'])) ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($data['pedidos_recentes'])): ?><tr><td colspan="3" class="empty">// sem pedidos</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="section">
        <div class="section-head">
          <div class="section-title">Top Produtos</div>
          <a href="?tab=produtos" class="btn btn-outline btn-sm">Gerenciar →</a>
        </div>
        <div class="table-wrap">
          <table class="mini-table">
            <thead><tr><th>Produto</th><th>Preço</th><th>Vendas</th><th>⭐</th></tr></thead>
            <tbody>
            <?php foreach ($data['prods_top'] as $p): ?>
            <tr>
              <td style="font-size:12px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= esc($p['nome']) ?></td>
              <td class="mono" style="color:var(--green)">R$ <?= number_format($p['preco'],2,',','.') ?></td>
              <td class="mono"><?= $p['total_vendas'] ?></td>
              <td class="mono" style="font-size:11px"><?= number_format($p['avaliacao_media'],1) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($data['prods_top'])): ?><tr><td colspan="4" class="empty">// sem produtos</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php elseif ($tab === 'produtos'): ?>
    <!-- ══════════ PRODUTOS ══════════ -->
    <div class="form-card">
      <div class="section-title" style="margin-bottom:16px">➕ Cadastrar Novo Produto</div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="create_produto">
        <input type="hidden" name="tab" value="produtos">
        <div class="form-grid">
          <div class="form-group">
            <label>Nome do Produto</label>
            <input type="text" name="nome" placeholder="Ex: Amortecedor Monroe Dianteiro" required>
          </div>
          <div class="form-group">
            <label>SKU</label>
            <input type="text" name="sku" placeholder="Ex: MON-001-D">
          </div>
          <div class="form-group">
            <label>Categoria</label>
            <select name="categoria_id">
              <option value="0">Selecionar...</option>
              <?php foreach ($data['categorias'] ?? [] as $c): ?>
              <option value="<?= $c['id'] ?>"><?= esc($c['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Marca</label>
            <select name="marca_id">
              <option value="0">Selecionar...</option>
              <?php foreach ($data['marcas'] ?? [] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= esc($m['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Preço (R$)</label>
            <input type="number" name="preco" step="0.01" placeholder="0,00" min="0" required>
          </div>
          <div class="form-group">
            <label>Estoque Inicial</label>
            <input type="number" name="estoque" placeholder="0" min="0" required>
          </div>
          <div class="form-group full">
            <label>Imagem do Produto</label>
            <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif" style="background:var(--surface);border:2px dashed var(--border);border-radius:6px;padding:12px;color:var(--muted);cursor:pointer;width:100%">
            <span style="font-size:11px;color:var(--muted);margin-top:2px">JPG, PNG, WEBP ou GIF · Máx 5MB</span>
          </div>
          <div class="form-group full">
            <label>Descrição</label>
            <textarea name="descricao" placeholder="Detalhes do produto, compatibilidade, especificações..."></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:8px">⚡ Cadastrar Produto</button>
      </form>
    </div>

    <div class="search-row">
      <form method="get" style="display:flex;gap:10px">
        <input type="hidden" name="tab" value="produtos">
        <input class="search-input" type="text" name="busca" placeholder="Buscar por nome ou SKU..." value="<?= esc($_GET['busca']??'') ?>">
        <button type="submit" class="btn btn-outline">Buscar</button>
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Nome</th><th>SKU</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Status</th><th>Vendas</th><th>⭐</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['produtos'] ?? [] as $p):
          $sc = $statusColors[$p['status']] ?? '#6b7280';
          $estoqueColor = $p['estoque'] == 0 ? 'var(--accent2)' : ($p['estoque'] <= 5 ? 'var(--accent)' : 'var(--text)');
        ?>
        <tr id="row-p-<?= $p['id'] ?>">
          <td class="mono" style="color:var(--muted)">#<?= $p['id'] ?></td>
          <td><strong><?= esc($p['nome']) ?></strong><?= $p['destaque'] ? ' 🌟' : '' ?><br><span style="font-size:11px;color:var(--muted)"><?= esc($p['cat']) ?> · <?= esc($p['marca']) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= esc($p['sku']) ?></td>
          <td style="font-size:12px"><?= esc($p['cat']) ?></td>
          <td class="mono" style="color:var(--green)">R$ <?= number_format($p['preco'],2,',','.') ?></td>
          <td class="mono" style="color:<?= $estoqueColor ?>;font-weight:<?= $p['estoque'] <= 5 ? '700' : '400' ?>"><?= $p['estoque'] ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15"><?= esc($p['status']) ?></span></td>
          <td class="mono"><?= $p['total_vendas'] ?></td>
          <td class="mono" style="font-size:11px"><?= number_format($p['avaliacao_media'],1) ?></td>
          <td style="white-space:nowrap;display:flex;gap:4px">
            <button class="btn btn-outline btn-sm" onclick="toggleEdit('ep-<?= $p['id'] ?>')">✏</button>
            <form method="post" style="display:inline" onsubmit="return confirm('Remover produto?')">
              <input type="hidden" name="acao" value="delete_produto">
              <input type="hidden" name="tab" value="produtos">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑</button>
            </form>
          </td>
        </tr>
        <tr class="edit-row" id="ep-<?= $p['id'] ?>">
          <td colspan="10">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_produto">
              <input type="hidden" name="tab" value="produtos">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['ativo','inativo','rascunho'] as $s): ?>
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
              <div class="field" style="flex-direction:row;align-items:center;gap:6px;margin-top:18px">
                <input type="checkbox" name="destaque" <?= $p['destaque']?'checked':'' ?>>
                <label>Destaque</label>
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-sm btn-outline" style="margin-top:18px" onclick="toggleEdit('ep-<?= $p['id'] ?>')">✕</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['produtos'])): ?><tr><td colspan="10" class="empty">// nenhum produto cadastrado ainda</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'pedidos'): ?>
    <!-- ══════════ PEDIDOS ══════════ -->
    <div class="table-wrap">
      <table>
        <thead><tr><th>Número</th><th>Cliente</th><th>Total</th><th>Status</th><th>Rastreamento</th><th>Data</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($data['pedidos'] ?? [] as $p): $sc = $statusColors[$p['status']] ?? '#6b7280'; ?>
        <tr>
          <td class="mono" style="color:var(--cyan)"><?= esc($p['numero']) ?></td>
          <td><?= esc($p['cliente']) ?><br><span style="font-size:11px;color:var(--muted)"><?= esc($p['email']) ?></span></td>
          <td class="mono" style="color:var(--green)">R$ <?= number_format($p['total'],2,',','.') ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15;font-size:9px"><?= str_replace('_',' ',esc($p['status'])) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= esc($p['rastreamento'] ?: '—') ?></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
          <td>
            <?php if (in_array($p['status'], ['pagamento_aprovado','em_separacao','enviado'])): ?>
            <button class="btn btn-outline btn-sm" onclick="toggleEdit('eo-<?= $p['id'] ?>')">✏ Atualizar</button>
            <?php else: ?>
            <span style="color:var(--muted);font-size:12px">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <tr class="edit-row" id="eo-<?= $p['id'] ?>">
          <td colspan="7">
            <form method="post" class="edit-form">
              <input type="hidden" name="acao" value="update_pedido_status">
              <input type="hidden" name="tab" value="pedidos">
              <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
              <div class="field"><label>Status</label>
                <select name="status">
                  <?php foreach (['em_separacao','enviado','entregue'] as $s): ?>
                  <option value="<?= $s ?>" <?= $p['status']===$s?'selected':'' ?>><?= str_replace('_',' ',$s) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field"><label>Código Rastreamento</label>
                <input type="text" name="rastreamento" value="<?= esc($p['rastreamento']) ?>" placeholder="BR123456789">
              </div>
              <button type="submit" class="btn btn-green btn-sm" style="margin-top:18px">💾 Salvar</button>
              <button type="button" class="btn btn-outline btn-sm" style="margin-top:18px" onclick="toggleEdit('eo-<?= $p['id'] ?>')">✕</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['pedidos'])): ?><tr><td colspan="7" class="empty">// nenhum pedido encontrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'repasses'): ?>
    <!-- ══════════ FINANCEIRO ══════════ -->
    <div class="repasse-summary">
      <div class="repasse-stat">
        <div class="val" style="color:var(--green)">R$ <?= number_format($data['total_liquido'], 2, ',', '.') ?></div>
        <div class="lbl">Total Recebido</div>
      </div>
      <div style="width:1px;height:40px;background:var(--border)"></div>
      <div class="repasse-stat">
        <div class="val" style="color:var(--accent)"><?= $data['repasses_pend'] ?? 0 ?></div>
        <div class="lbl">Repasses Pendentes</div>
      </div>
      <div style="width:1px;height:40px;background:var(--border)"></div>
      <div class="repasse-stat">
        <div class="val"><?= count($data['repasses'] ?? []) ?></div>
        <div class="lbl">Total de Repasses</div>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Bruto</th><th>Taxa (%)</th><th>Taxa (R$)</th><th>Líquido</th><th>Status</th><th>Data</th></tr></thead>
        <tbody>
        <?php foreach ($data['repasses'] ?? [] as $r): $sc = $statusColors[$r['status']] ?? '#6b7280'; ?>
        <tr>
          <td class="mono" style="color:var(--muted)">#<?= $r['id'] ?></td>
          <td class="mono">R$ <?= number_format($r['valor_bruto'],2,',','.') ?></td>
          <td class="mono" style="color:var(--accent)"><?= $r['taxa_plat'] ?>%</td>
          <td class="mono" style="color:var(--accent2)">- R$ <?= number_format($r['valor_taxa'],2,',','.') ?></td>
          <td class="mono" style="color:var(--green);font-weight:700">R$ <?= number_format($r['valor_liquido'],2,',','.') ?></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15"><?= esc($r['status']) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/Y', strtotime($r['criado_em'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['repasses'])): ?><tr><td colspan="7" class="empty">// nenhum repasse registrado</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'tickets'): ?>
    <!-- ══════════ SUPORTE ══════════ -->
    <div class="form-card">
      <div class="section-title" style="margin-bottom:16px">💬 Abrir Novo Ticket</div>
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
              <option value="produto">Produto / Catálogo</option>
              <option value="pedido">Pedido / Entrega</option>
              <option value="financeiro">Financeiro / Repasse</option>
              <option value="conta">Conta da Empresa</option>
              <option value="outro">Outro</option>
            </select>
          </div>
          <div class="form-group full">
            <label>Mensagem</label>
            <textarea name="mensagem" placeholder="Descreva com detalhes..." required></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:8px">📨 Enviar Ticket</button>
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr><th>Número</th><th>Assunto</th><th>Categoria</th><th>Prioridade</th><th>Status</th><th>Data</th></tr></thead>
        <tbody>
        <?php foreach ($data['tickets'] ?? [] as $t):
          $pc = $statusColors[$t['prioridade']] ?? '#6b7280';
          $sc = $statusColors[$t['status']] ?? '#6b7280';
        ?>
        <tr>
          <td class="mono" style="color:var(--accent)"><?= esc($t['numero']) ?></td>
          <td style="max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= esc($t['assunto']) ?></td>
          <td style="font-size:12px;color:var(--muted)"><?= esc($t['categoria']) ?></td>
          <td><span class="badge" style="color:<?= $pc ?>;border-color:<?= $pc ?>30;background:<?= $pc ?>15"><?= esc($t['prioridade']) ?></span></td>
          <td><span class="badge" style="color:<?= $sc ?>;border-color:<?= $sc ?>30;background:<?= $sc ?>15"><?= esc($t['status']) ?></span></td>
          <td class="mono" style="font-size:11px;color:var(--muted)"><?= date('d/m/Y H:i', strtotime($t['criado_em'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['tickets'])): ?><tr><td colspan="6" class="empty">// nenhum ticket</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'perfil'): ?>
    <!-- ══════════ PERFIL ══════════ -->
    <div class="form-card">
      <div class="section-title" style="margin-bottom:20px">Dados da Empresa</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
        <?php
        $campos = ['Razão Social'=>'razao_social','CNPJ'=>'cnpj','Nome Fantasia'=>'nome_fantasia','Avaliação'=>'avaliacao_media','Status'=>'status','Verificada'=>'verificada'];
        foreach ($campos as $label => $campo):
          $val = $empresa[$campo] ?? '—';
          if ($campo === 'verificada') $val = $val ? '✅ Sim' : '❌ Não';
          if ($campo === 'avaliacao_media') $val = number_format($val,1) . ' ⭐';
        ?>
        <div class="form-group">
          <label><?= $label ?></label>
          <input type="text" value="<?= esc((string)$val) ?>" disabled style="opacity:.6;cursor:not-allowed">
        </div>
        <?php endforeach; ?>
      </div>
      <hr style="border-color:var(--border);margin-bottom:20px">
      <form method="post">
        <input type="hidden" name="acao" value="update_perfil_empresa">
        <input type="hidden" name="tab" value="perfil">
        <div class="form-grid">
          <div class="form-group">
            <label>Telefone de Contato</label>
            <input type="text" name="telefone" value="<?= esc($empresa['telefone'] ?? '') ?>" placeholder="(51) 9 9999-9999">
          </div>
          <div style=""></div>
          <div class="form-group full">
            <label>Descrição da Empresa</label>
            <textarea name="descricao" placeholder="Fale sobre sua empresa, especialidades, diferenciais..."><?= esc($empresa['descricao'] ?? '') ?></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:12px">💾 Salvar Alterações</button>
      </form>
    </div>
    <?php endif; ?>

  </div>
</main>
</div>
<script>
function toggleEdit(id) {
  const row = document.getElementById(id);
  if (!row) return;
  row.classList.toggle('open');
}
const flash = document.querySelector('.flash');
if (flash) setTimeout(() => { flash.style.transition='opacity .5s'; flash.style.opacity='0'; }, 4000);
</script>
</body>
</html>