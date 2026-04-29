<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'pecaaq');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) { echo json_encode(['status'=>'error','message'=>'Falha na conexão']); exit; }

if (empty($_SESSION['id_usuario'])) { echo json_encode(['status'=>'error','message'=>'Sessão expirada.']); exit; }
$id_usuario = (int)$_SESSION['id_usuario'];

// Busca empresa_id do usuário logado
$r = $conn->query("SELECT id FROM empresas WHERE usuario_id = $id_usuario LIMIT 1");
if (!$r || $r->num_rows === 0) { echo json_encode(['status'=>'error','message'=>'Empresa não encontrada.']); exit; }
$empresa_id = (int)$r->fetch_assoc()['id'];

// Campos
$nome         = trim($_POST['nome']        ?? '');
$sku          = trim($_POST['sku']         ?? '') ?: 'SKU-'.uniqid();
$descricao    = trim($_POST['descricao']   ?? '');
$preco        = (float) preg_replace('/[^\d\.]/', '', str_replace(['.', ','], ['', '.'], trim($_POST['preco'] ?? '0')));
$estoque      = (int)($_POST['estoque']    ?? 0);
$id_categoria = (int)($_POST['id_categoria'] ?? 1);
$id_marca     = ($_POST['id_marca'] ?? '') !== '' ? (int)$_POST['id_marca'] : null;

if ($nome === '' || $preco <= 0) { echo json_encode(['status'=>'error','message'=>'Nome e preço são obrigatórios.']); exit; }

$slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $nome)) . '-' . uniqid();

// Upload imagem
$imagem = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
        $fname = uniqid('prod_') . '.' . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fname)) $imagem = $fname;
    }
}

// Insere com estrutura correta
$sql = "INSERT INTO produtos (empresa_id,categoria_id,marca_id,nome,slug,descricao,sku,preco,estoque,imagem_principal,status,criado_em,atualizado_em)
        VALUES (?,?,?,?,?,?,?,?,?,?,'ativo',NOW(),NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiissssdis', $empresa_id,$id_categoria,$id_marca,$nome,$slug,$descricao,$sku,$preco,$estoque,$imagem);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Produto cadastrado com sucesso!','produto'=>['id'=>$stmt->insert_id,'nome'=>$nome,'imagem_principal'=>$imagem,'preco'=>number_format($preco,2,'.','')]]);
} else {
    echo json_encode(['status'=>'error','message'=>'Erro ao inserir: '.$stmt->error]);
}
$stmt->close(); $conn->close();