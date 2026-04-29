<?php
// ═══════════════════════════════════════════════════
//  PeçaAQ — api.php
//  Endpoint único de CRUD para os três dashboards.
//  Todas as requisições vêm via fetch() JSON.
//  Requer sessão PHP ativa para autenticação.
// ═══════════════════════════════════════════════════
 
session_start();
header('Content-Type: application/json; charset=utf-8');
 
// ── Helpers ─────────────────────────────────────────
function jsonOk($data = []): never  { echo json_encode(['ok'=>true]  + (array)$data); exit; }
function jsonErr(string $m, int $c=400): never {
    http_response_code($c);
    echo json_encode(['ok'=>false,'erro'=>$m]);
    exit;
}
function limpar(string $v): string { return trim(htmlspecialchars($v, ENT_QUOTES,'UTF-8')); }
function soNum(string $v):  string { return preg_replace('/\D/','',$v); }
 
// ── Auth ────────────────────────────────────────────
if (empty($_SESSION['id_usuario'])) jsonErr('Não autenticado.', 401);
 
$userId  = (int)$_SESSION['id_usuario'];
$tipoSess = $_SESSION['tipo_usuario'] ?? '';
 
function isAdmin(): bool   { return $GLOBALS['tipoSess'] === 'admin'; }
function isEmpresa(): bool { return $GLOBALS['tipoSess'] === 'Fornecedor'; }
function isCliente(): bool { return $GLOBALS['tipoSess'] === 'Cliente'; }
function somenteAdmin(): void { if (!isAdmin()) jsonErr('Acesso negado.', 403); }
 
// ── Banco ───────────────────────────────────────────
$conn = new mysqli('localhost','root','','pecaaq');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) jsonErr('Falha no banco.', 500);
 
// ── Rota ────────────────────────────────────────────
$metodo = $_SERVER['REQUEST_METHOD'];
$acao   = limpar($_GET['acao'] ?? '');
 
$body = [];
if ($metodo !== 'GET') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? [];
    // também aceita FormData
    if (empty($body)) $body = $_POST;
}
 
// ════════════════════════════════════════════════════
//  PERFIL
// ════════════════════════════════════════════════════
if ($acao === 'get_perfil') {
    $st = $conn->prepare(
        'SELECT u.id, u.nome, u.sobrenome, u.email, u.cpf, u.telefone,
                u.data_nascimento, u.avatar_url, u.tipo, u.status, u.criado_em,
                e.cep, e.logradouro, e.numero, e.complemento, e.bairro, e.cidade, e.estado
         FROM usuarios u
         LEFT JOIN enderecos e ON e.id = u.endereco_id
         WHERE u.id = ? LIMIT 1'
    );
    $st->bind_param('i', $userId);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $st->close();
    jsonOk(['perfil' => $row]);
}
 
if ($acao === 'update_perfil' && $metodo === 'POST') {
    $nome  = limpar($body['nome']      ?? '');
    $sob   = limpar($body['sobrenome'] ?? '');
    $tel   = limpar($body['telefone']  ?? '');
    $nasc  = limpar($body['data_nascimento'] ?? '');
 
    if (strlen($nome) < 2) jsonErr('Nome inválido.');
    if (strlen($sob)  < 2) jsonErr('Sobrenome inválido.');
 
    $st = $conn->prepare('UPDATE usuarios SET nome=?,sobrenome=?,telefone=?,data_nascimento=?,atualizado_em=NOW() WHERE id=?');
    $st->bind_param('ssssi', $nome,$sob,$tel,$nasc,$userId);
    $st->execute();
    $st->close();
 
    // Atualiza endereço se vier
    if (!empty($body['logradouro'])) {
        $stU = $conn->prepare('SELECT endereco_id FROM usuarios WHERE id=? LIMIT 1');
        $stU->bind_param('i', $userId); $stU->execute();
        $r = $stU->get_result()->fetch_assoc(); $stU->close();
        $endId = $r['endereco_id'] ?? null;
 
        $cep = soNum($body['cep'] ?? '');
        $cepFmt = strlen($cep)===8 ? substr($cep,0,5).'-'.substr($cep,5,3) : $body['cep'];
        $log = limpar($body['logradouro']  ?? '');
        $num = limpar($body['numero']      ?? '');
        $comp= limpar($body['complemento'] ?? '');
        $bai = limpar($body['bairro']      ?? '');
        $cid = limpar($body['cidade']      ?? '');
        $est = strtoupper(limpar($body['estado'] ?? ''));
 
        if ($endId) {
            $stE = $conn->prepare('UPDATE enderecos SET cep=?,logradouro=?,numero=?,complemento=?,bairro=?,cidade=?,estado=? WHERE id=?');
            $stE->bind_param('sssssssi', $cepFmt,$log,$num,$comp,$bai,$cid,$est,$endId);
            $stE->execute(); $stE->close();
        } else {
            $stE = $conn->prepare('INSERT INTO enderecos (cep,logradouro,numero,complemento,bairro,cidade,estado) VALUES (?,?,?,?,?,?,?)');
            $stE->bind_param('sssssss', $cepFmt,$log,$num,$comp,$bai,$cid,$est);
            $stE->execute();
            $newEnd = $conn->insert_id; $stE->close();
            $stU2 = $conn->prepare('UPDATE usuarios SET endereco_id=? WHERE id=?');
            $stU2->bind_param('ii', $newEnd,$userId); $stU2->execute(); $stU2->close();
        }
    }
    jsonOk(['msg' => 'Perfil atualizado com sucesso.']);
}
 
if ($acao === 'update_senha' && $metodo === 'POST') {
    $atual = $body['senha_atual'] ?? '';
    $nova  = $body['nova_senha']  ?? '';
    $conf  = $body['confirmar']   ?? '';
 
    if (strlen($nova) < 8)  jsonErr('Nova senha deve ter ao menos 8 caracteres.');
    if ($nova !== $conf)    jsonErr('As senhas não coincidem.');
 
    $st = $conn->prepare('SELECT senha_hash FROM usuarios WHERE id=? LIMIT 1');
    $st->bind_param('i', $userId); $st->execute();
    $row = $st->get_result()->fetch_assoc(); $st->close();
 
    if (!password_verify($atual, $row['senha_hash'])) jsonErr('Senha atual incorreta.');
 
    $hash = password_hash($nova, PASSWORD_BCRYPT, ['cost'=>12]);
    $st = $conn->prepare('UPDATE usuarios SET senha_hash=? WHERE id=?');
    $st->bind_param('si', $hash,$userId); $st->execute(); $st->close();
    jsonOk(['msg'=>'Senha atualizada.']);
}
 
// ════════════════════════════════════════════════════
//  PEDIDOS (cliente)
// ════════════════════════════════════════════════════
if ($acao === 'get_pedidos') {
    $limit  = min((int)($_GET['limit'] ?? 20), 100);
    $offset = (int)($_GET['offset'] ?? 0);
    $status = limpar($_GET['status'] ?? '');
 
    $where  = isAdmin() ? '' : "WHERE p.usuario_id = $userId";
    $filterStatus = $status ? " AND p.status = '$status'" : '';
    if (isAdmin() && $status) $where = "WHERE p.status = '$status'";
 
    $sql = "SELECT p.id, p.numero, p.status, p.total, p.metodo_pagamento,
                   p.criado_em, p.rastreamento, p.transportadora, p.previsao_entrega,
                   u.nome, u.sobrenome, u.email,
                   COUNT(pi.id) as qtd_itens
            FROM pedidos p
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN pedido_itens pi ON pi.pedido_id = p.id
            $where $filterStatus
            GROUP BY p.id
            ORDER BY p.criado_em DESC
            LIMIT $limit OFFSET $offset";
 
    $result = $conn->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    jsonOk(['pedidos' => $rows]);
}
 
if ($acao === 'get_pedido_detalhe') {
    $pedidoId = (int)($_GET['id'] ?? 0);
    $st = $conn->prepare(
        'SELECT p.*, u.nome, u.sobrenome, u.email, u.telefone,
                e.logradouro, e.numero, e.bairro, e.cidade, e.estado, e.cep
         FROM pedidos p
         JOIN usuarios u ON u.id = p.usuario_id
         LEFT JOIN enderecos e ON e.id = p.endereco_entrega_id
         WHERE p.id = ? AND (p.usuario_id = ? OR ?) LIMIT 1'
    );
    $isAdm = isAdmin() ? 1 : 0;
    $st->bind_param('iii', $pedidoId, $userId, $isAdm);
    $st->execute();
    $pedido = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$pedido) jsonErr('Pedido não encontrado.', 404);
 
    $st2 = $conn->prepare(
        'SELECT pi.*, emp.nome_fantasia as empresa_nome
         FROM pedido_itens pi
         JOIN empresas emp ON emp.id = pi.empresa_id
         WHERE pi.pedido_id = ?'
    );
    $st2->bind_param('i', $pedidoId); $st2->execute();
    $itens = $st2->get_result()->fetch_all(MYSQLI_ASSOC); $st2->close();
 
    $st3 = $conn->prepare('SELECT * FROM pedido_historico WHERE pedido_id=? ORDER BY criado_em ASC');
    $st3->bind_param('i', $pedidoId); $st3->execute();
    $hist = $st3->get_result()->fetch_all(MYSQLI_ASSOC); $st3->close();
 
    jsonOk(['pedido'=>$pedido,'itens'=>$itens,'historico'=>$hist]);
}
 
if ($acao === 'update_pedido_status' && $metodo === 'POST') {
    // Empresa pode atualizar status dos seus pedidos; admin pode tudo
    $pedidoId = (int)($body['pedido_id'] ?? 0);
    $novoStatus = limpar($body['status'] ?? '');
    $obs = limpar($body['observacao'] ?? '');
 
    $statusValidos = ['aguardando_pagamento','pagamento_aprovado','em_separacao','enviado','entregue','cancelado','devolvido','reembolsado'];
    if (!in_array($novoStatus, $statusValidos)) jsonErr('Status inválido.');
 
    if (!isAdmin() && !isEmpresa()) jsonErr('Sem permissão.', 403);
 
    $st = $conn->prepare('UPDATE pedidos SET status=?,atualizado_em=NOW() WHERE id=?');
    $st->bind_param('si', $novoStatus,$pedidoId); $st->execute(); $st->close();
 
    $st2 = $conn->prepare('INSERT INTO pedido_historico (pedido_id,status,descricao,usuario_id) VALUES (?,?,?,?)');
    $st2->bind_param('issi', $pedidoId,$novoStatus,$obs,$userId); $st2->execute(); $st2->close();
 
    jsonOk(['msg'=>'Status atualizado.']);
}
 
// ════════════════════════════════════════════════════
//  PRODUTOS (empresa / admin CRUD)
// ════════════════════════════════════════════════════
if ($acao === 'get_produtos') {
    $busca   = limpar($_GET['q']        ?? '');
    $catId   = (int)($_GET['categoria'] ?? 0);
    $limit   = min((int)($_GET['limit'] ?? 20), 100);
    $offset  = (int)($_GET['offset']    ?? 0);
    $status  = limpar($_GET['status']   ?? '');
 
    $where = [];
    $params = []; $types = '';
 
    if (!isAdmin()) {
        // empresa só vê os próprios
        $stEmp = $conn->prepare('SELECT id FROM empresas WHERE usuario_id=? LIMIT 1');
        $stEmp->bind_param('i',$userId); $stEmp->execute();
        $emp = $stEmp->get_result()->fetch_assoc(); $stEmp->close();
        if (!$emp) jsonErr('Empresa não encontrada.',404);
        $empId = (int)$emp['id'];
        $where[] = "p.empresa_id = $empId";
    }
    if ($busca)  { $where[] = "(p.nome LIKE ? OR p.sku LIKE ?)"; $b="%$busca%"; $params[]=&$b; $params[]=&$b; $types.='ss'; }
    if ($catId)  { $where[] = "p.categoria_id = $catId"; }
    if ($status) { $where[] = "p.status = '$status'"; }
 
    $whereSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';
    $sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome, emp.nome_fantasia as empresa_nome
            FROM produtos p
            LEFT JOIN categorias c ON c.id=p.categoria_id
            LEFT JOIN marcas m ON m.id=p.marca_id
            LEFT JOIN empresas emp ON emp.id=p.empresa_id
            $whereSQL ORDER BY p.criado_em DESC LIMIT $limit OFFSET $offset";
 
    if ($params) {
        $st = $conn->prepare($sql);
        array_unshift($params, $types);
        call_user_func_array([$st,'bind_param'], $params);
        $st->execute();
        $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
    } else {
        $rows = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    jsonOk(['produtos'=>$rows]);
}
 
if ($acao === 'criar_produto' && $metodo === 'POST') {
    if (!isEmpresa() && !isAdmin()) jsonErr('Sem permissão.',403);
 
    $stEmp = $conn->prepare('SELECT id FROM empresas WHERE usuario_id=? LIMIT 1');
    $stEmp->bind_param('i',$userId); $stEmp->execute();
    $emp = $stEmp->get_result()->fetch_assoc(); $stEmp->close();
    if (!$emp) jsonErr('Empresa não encontrada.',404);
    $empId = (int)$emp['id'];
 
    $nome   = limpar($body['nome']         ?? '');
    $catId  = (int)($body['categoria_id']  ?? 0);
    $mrkId  = $body['marca_id'] ? (int)$body['marca_id'] : null;
    $preco  = (float)($body['preco']        ?? 0);
    $estoque= (int)($body['estoque']        ?? 0);
    $sku    = limpar($body['sku']           ?? uniqid('PRD'));
    $desc   = limpar($body['descricao']     ?? '');
    $disp   = limpar($body['disponibilidade']?? 'em_estoque');
    $status = isAdmin() ? 'ativo' : 'pendente';
    $slug   = strtolower(preg_replace('/[^a-z0-9]+/','-', $nome)).'-'.time();
 
    if (strlen($nome) < 2) jsonErr('Nome do produto inválido.');
    if ($preco <= 0)       jsonErr('Preço inválido.');
    if ($catId <= 0)       jsonErr('Categoria obrigatória.');
 
    $st = $conn->prepare(
        'INSERT INTO produtos (empresa_id,categoria_id,marca_id,nome,slug,descricao,
         sku,preco,estoque,disponibilidade,status)
         VALUES (?,?,?,?,?,?,?,?,?,?,?)'
    );
    $st->bind_param('iiissssdiss',
        $empId,$catId,$mrkId,$nome,$slug,$desc,$sku,$preco,$estoque,$disp,$status
    );
    $st->execute();
    $newId = $conn->insert_id;
    $st->close();
    jsonOk(['id'=>$newId,'msg'=>'Produto criado com sucesso.']);
}
 
if ($acao === 'update_produto' && $metodo === 'POST') {
    if (!isEmpresa() && !isAdmin()) jsonErr('Sem permissão.',403);
 
    $prodId = (int)($body['id'] ?? 0);
    $nome   = limpar($body['nome']          ?? '');
    $preco  = (float)($body['preco']         ?? 0);
    $estoque= (int)($body['estoque']         ?? 0);
    $desc   = limpar($body['descricao']      ?? '');
    $disp   = limpar($body['disponibilidade']?? 'em_estoque');
    $status = limpar($body['status']         ?? 'ativo');
    $catId  = (int)($body['categoria_id']    ?? 0);
 
    if ($prodId <= 0) jsonErr('ID inválido.');
 
    $st = $conn->prepare(
        'UPDATE produtos SET nome=?,preco=?,estoque=?,descricao=?,disponibilidade=?,status=?,categoria_id=?,atualizado_em=NOW()
         WHERE id=?'
    );
    $st->bind_param('sdisssii', $nome,$preco,$estoque,$desc,$disp,$status,$catId,$prodId);
    $st->execute(); $st->close();
    jsonOk(['msg'=>'Produto atualizado.']);
}
 
if ($acao === 'delete_produto' && $metodo === 'POST') {
    if (!isEmpresa() && !isAdmin()) jsonErr('Sem permissão.',403);
    $prodId = (int)($body['id'] ?? 0);
    $st = $conn->prepare('UPDATE produtos SET status=? WHERE id=?');
    $inativo = 'inativo';
    $st->bind_param('si', $inativo,$prodId); $st->execute(); $st->close();
    jsonOk(['msg'=>'Produto desativado.']);
}
 
// ════════════════════════════════════════════════════
//  USUÁRIOS (admin CRUD)
// ════════════════════════════════════════════════════
if ($acao === 'get_usuarios') {
    somenteAdmin();
    $busca  = limpar($_GET['q']      ?? '');
    $tipo   = limpar($_GET['tipo']   ?? '');
    $status = limpar($_GET['status'] ?? '');
    $limit  = min((int)($_GET['limit'] ?? 20), 200);
    $offset = (int)($_GET['offset']  ?? 0);
 
    $where = []; $params=[]; $types='';
    if ($busca)  { $where[]="(u.nome LIKE ? OR u.email LIKE ?)"; $b="%$busca%"; $params[]=&$b;$params[]=&$b;$types.='ss'; }
    if ($tipo)   { $where[]="u.tipo='$tipo'"; }
    if ($status) { $where[]="u.status='$status'"; }
    $wSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';
 
    $sql = "SELECT u.id,u.nome,u.sobrenome,u.email,u.tipo,u.status,u.criado_em,u.ultimo_login
            FROM usuarios u $wSQL ORDER BY u.criado_em DESC LIMIT $limit OFFSET $offset";
 
    if ($params) {
        $st = $conn->prepare($sql);
        array_unshift($params,$types);
        call_user_func_array([$st,'bind_param'],$params);
        $st->execute();
        $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
    } else {
        $rows = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    jsonOk(['usuarios'=>$rows]);
}
 
if ($acao === 'update_usuario_status' && $metodo === 'POST') {
    somenteAdmin();
    $uid    = (int)($body['id']     ?? 0);
    $status = limpar($body['status']?? '');
    if (!in_array($status,['ativo','inativo','suspenso'])) jsonErr('Status inválido.');
    $st = $conn->prepare('UPDATE usuarios SET status=? WHERE id=?');
    $st->bind_param('si',$status,$uid); $st->execute(); $st->close();
    jsonOk(['msg'=>'Usuário atualizado.']);
}
 
if ($acao === 'delete_usuario' && $metodo === 'POST') {
    somenteAdmin();
    $uid = (int)($body['id'] ?? 0);
    $st = $conn->prepare('UPDATE usuarios SET status=? WHERE id=?');
    $inativo = 'inativo';
    $st->bind_param('si',$inativo,$uid); $st->execute(); $st->close();
    jsonOk(['msg'=>'Usuário desativado.']);
}
 
// ════════════════════════════════════════════════════
//  EMPRESAS (admin aprova/rejeita)
// ════════════════════════════════════════════════════
if ($acao === 'get_empresas') {
    somenteAdmin();
    $status = limpar($_GET['status'] ?? '');
    $busca  = limpar($_GET['q']      ?? '');
    $limit  = min((int)($_GET['limit'] ?? 20), 100);
    $offset = (int)($_GET['offset']  ?? 0);
    $where=[]; $params=[]; $types='';
    if ($busca)  { $where[]="(e.nome_fantasia LIKE ? OR e.cnpj LIKE ?)"; $b="%$busca%"; $params[]=&$b;$params[]=&$b;$types.='ss'; }
    if ($status) { $where[]="e.status='$status'"; }
    $wSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';
 
    $sql = "SELECT e.id,e.nome_fantasia,e.razao_social,e.cnpj,e.status,e.verificada,
                   e.avaliacao_media,e.total_vendas,e.criado_em,
                   u.email,u.nome as responsavel
            FROM empresas e
            JOIN usuarios u ON u.id=e.usuario_id
            $wSQL ORDER BY e.criado_em DESC LIMIT $limit OFFSET $offset";
 
    if ($params) {
        $st=$conn->prepare($sql);
        array_unshift($params,$types);
        call_user_func_array([$st,'bind_param'],$params);
        $st->execute(); $rows=$st->get_result()->fetch_all(MYSQLI_ASSOC); $st->close();
    } else { $rows=$conn->query($sql)->fetch_all(MYSQLI_ASSOC); }
    jsonOk(['empresas'=>$rows]);
}
 
if ($acao === 'update_empresa_status' && $metodo === 'POST') {
    somenteAdmin();
    $eid    = (int)($body['id']     ?? 0);
    $status = limpar($body['status']?? '');
    if (!in_array($status,['pendente','aprovada','suspensa','rejeitada'])) jsonErr('Status inválido.');
    $st = $conn->prepare('UPDATE empresas SET status=? WHERE id=?');
    $st->bind_param('si',$status,$eid); $st->execute(); $st->close();
    jsonOk(['msg'=>'Empresa atualizada.']);
}
 
// ════════════════════════════════════════════════════
//  DASHBOARD STATS
// ════════════════════════════════════════════════════
if ($acao === 'get_stats_admin') {
    somenteAdmin();
    $stats = [];
    $stats['total_usuarios']  = $conn->query("SELECT COUNT(*) c FROM usuarios WHERE tipo='cliente'")->fetch_assoc()['c'];
    $stats['total_empresas']  = $conn->query("SELECT COUNT(*) c FROM empresas WHERE status='aprovada'")->fetch_assoc()['c'];
    $stats['pendentes_emp']   = $conn->query("SELECT COUNT(*) c FROM empresas WHERE status='pendente'")->fetch_assoc()['c'];
    $stats['total_pedidos']   = $conn->query("SELECT COUNT(*) c FROM pedidos")->fetch_assoc()['c'];
    $stats['receita_mes']     = $conn->query("SELECT COALESCE(SUM(total),0) v FROM pedidos WHERE MONTH(criado_em)=MONTH(NOW()) AND status NOT IN('cancelado','devolvido')")->fetch_assoc()['v'];
    $stats['total_produtos']  = $conn->query("SELECT COUNT(*) c FROM produtos WHERE status='ativo'")->fetch_assoc()['c'];
    $stats['pendentes_prod']  = $conn->query("SELECT COUNT(*) c FROM produtos WHERE status='pendente'")->fetch_assoc()['c'];
    jsonOk(['stats'=>$stats]);
}
 
if ($acao === 'get_stats_empresa') {
    if (!isEmpresa()) jsonErr('Sem permissão.',403);
    $stEmp=$conn->prepare('SELECT id FROM empresas WHERE usuario_id=? LIMIT 1');
    $stEmp->bind_param('i',$userId);$stEmp->execute();
    $emp=$stEmp->get_result()->fetch_assoc();$stEmp->close();
    if (!$emp) jsonErr('Empresa não encontrada.',404);
    $empId=(int)$emp['id'];
 
    $stats=[];
    $stats['total_produtos']=$conn->query("SELECT COUNT(*) c FROM produtos WHERE empresa_id=$empId AND status='ativo'")->fetch_assoc()['c'];
    $stats['total_pedidos'] =$conn->query("SELECT COUNT(DISTINCT pedido_id) c FROM pedido_itens WHERE empresa_id=$empId")->fetch_assoc()['c'];
    $stats['receita_total'] =$conn->query("SELECT COALESCE(SUM(total),0) v FROM repasses WHERE empresa_id=$empId AND status='pago'")->fetch_assoc()['v'];
    $stats['pendente_repasse']=$conn->query("SELECT COALESCE(SUM(valor_liquido),0) v FROM repasses WHERE empresa_id=$empId AND status='pendente'")->fetch_assoc()['v'];
    $stats['estoque_critico']=$conn->query("SELECT COUNT(*) c FROM produtos WHERE empresa_id=$empId AND estoque<=estoque_minimo AND status='ativo'")->fetch_assoc()['c'];
    jsonOk(['stats'=>$stats]);
}
 
if ($acao === 'get_stats_cliente') {
    $stats=[];
    $stats['total_pedidos']  =$conn->query("SELECT COUNT(*) c FROM pedidos WHERE usuario_id=$userId")->fetch_assoc()['c'];
    $stats['pedidos_entregues']=$conn->query("SELECT COUNT(*) c FROM pedidos WHERE usuario_id=$userId AND status='entregue'")->fetch_assoc()['c'];
    $stats['em_andamento']   =$conn->query("SELECT COUNT(*) c FROM pedidos WHERE usuario_id=$userId AND status IN('aguardando_pagamento','pagamento_aprovado','em_separacao','enviado')")->fetch_assoc()['c'];
    $stats['total_gasto']    =$conn->query("SELECT COALESCE(SUM(total),0) v FROM pedidos WHERE usuario_id=$userId AND status NOT IN('cancelado','devolvido')")->fetch_assoc()['v'];
    jsonOk(['stats'=>$stats]);
}
 
// ════════════════════════════════════════════════════
//  NOTIFICAÇÕES
// ════════════════════════════════════════════════════
if ($acao === 'get_notificacoes') {
    $st=$conn->prepare('SELECT * FROM notificacoes WHERE usuario_id=? ORDER BY criado_em DESC LIMIT 30');
    $st->bind_param('i',$userId);$st->execute();
    $rows=$st->get_result()->fetch_all(MYSQLI_ASSOC);$st->close();
    jsonOk(['notificacoes'=>$rows]);
}
if ($acao === 'marcar_notif_lida' && $metodo==='POST') {
    $nid=(int)($body['id']??0);
    $st=$conn->prepare('UPDATE notificacoes SET lida=1 WHERE id=? AND usuario_id=?');
    $st->bind_param('ii',$nid,$userId);$st->execute();$st->close();
    jsonOk();
}
if ($acao === 'marcar_todas_lidas' && $metodo==='POST') {
    $st=$conn->prepare('UPDATE notificacoes SET lida=1 WHERE usuario_id=?');
    $st->bind_param('i',$userId);$st->execute();$st->close();
    jsonOk();
}
 
// ════════════════════════════════════════════════════
//  CATEGORIAS / MARCAS (leitura p/ selects)
// ════════════════════════════════════════════════════
if ($acao === 'get_categorias') {
    $rows=$conn->query('SELECT id,nome FROM categorias WHERE ativo=1 ORDER BY ordem')->fetch_all(MYSQLI_ASSOC);
    jsonOk(['categorias'=>$rows]);
}
if ($acao === 'get_marcas') {
    $rows=$conn->query('SELECT id,nome FROM marcas WHERE ativo=1 ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
    jsonOk(['marcas'=>$rows]);
}
 
// ════════════════════════════════════════════════════
//  CONFIGURAÇÕES (admin)
// ════════════════════════════════════════════════════
if ($acao === 'get_configs') {
    somenteAdmin();
    $rows=$conn->query('SELECT * FROM configuracoes ORDER BY grupo,chave')->fetch_all(MYSQLI_ASSOC);
    jsonOk(['configs'=>$rows]);
}
if ($acao === 'update_config' && $metodo==='POST') {
    somenteAdmin();
    $chave = limpar($body['chave'] ?? '');
    $valor = limpar($body['valor'] ?? '');
    if (!$chave) jsonErr('Chave inválida.');
    $st=$conn->prepare('UPDATE configuracoes SET valor=? WHERE chave=?');
    $st->bind_param('ss',$valor,$chave);$st->execute();$st->close();
    jsonOk(['msg'=>'Configuração salva.']);
}
 
$conn->close();
jsonErr('Ação não encontrada.', 404);