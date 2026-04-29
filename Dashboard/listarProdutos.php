<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'pecaaq');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) { echo json_encode(['status'=>'erro','mensagem'=>'Erro de conexão']); exit; }
if (empty($_SESSION['id_usuario'])) { echo json_encode(['status'=>'erro','mensagem'=>'Sessão expirada.']); exit; }

$id_usuario = (int)$_SESSION['id_usuario'];

// Busca empresa do usuário
$r = $conn->query("SELECT id FROM empresas WHERE usuario_id = $id_usuario LIMIT 1");
if (!$r || $r->num_rows === 0) { echo json_encode(['status'=>'ok','produtos',[]]); exit; }
$empresa_id = (int)$r->fetch_assoc()['id'];

$sql = "SELECT p.id, p.nome, p.preco, p.estoque, p.status, p.imagem_principal,
               c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        WHERE p.empresa_id = ?
        ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $empresa_id);
$stmt->execute();
$result = $stmt->get_result();

$produtos = [];
while ($row = $result->fetch_assoc()) {
    $row['foto_principal'] = !empty($row['imagem_principal'])
        ? '../Dashboard/uploads/' . $row['imagem_principal']
        : '../Comprar/imgComprar/pe%C3%A7a.webp';
    $produtos[] = $row;
}

echo json_encode(['status'=>'ok','produtos'=>$produtos]);
$stmt->close(); $conn->close();