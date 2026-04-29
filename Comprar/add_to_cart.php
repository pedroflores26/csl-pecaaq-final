<?php
// add_to_cart.php (usa produtos)
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id_produto']) ? intval($_POST['id_produto']) : 0;
$qtd = isset($_POST['quantidade']) ? max(1,intval($_POST['quantidade'])) : 1;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'id_produto inválido']);
    exit;
}

// DB config - ajuste conforme necessário
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "pecaaq";

$conn = new mysqli($servidor, $usuario, $senha, $banco);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Erro conexão DB: '.$conn->connect_error]);
    exit;
}

// buscar produto (nome, preco, foto) - ajuste nomes de coluna se diferente
$stmt = $conn->prepare("SELECT id_produto, nome, preco, foto_principal FROM produtos WHERE id_produto = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$prod = $res->fetch_assoc();
$stmt->close();

if (!$prod) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Produto não encontrado']);
    $conn->close();
    exit;
}

// garante sessao cart
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

// estrutura: $_SESSION['cart'][<id_produto>] = ['quantidade'=>N, 'preco'=>X, 'nome'=>..]
if (!isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = ['quantidade'=>0, 'preco'=> (float)$prod['preco'], 'nome'=> $prod['nome']];
}
$_SESSION['cart'][$id]['quantidade'] += $qtd;

// recalcula totais
$cart_total = 0.0; $cart_count = 0;
foreach ($_SESSION['cart'] as $pid => $item) {
    $cart_total += ((float)$item['preco']) * (int)$item['quantidade'];
    $cart_count += (int)$item['quantidade'];
}

echo json_encode(['status'=>'ok','message'=>'adicionado','cart_count'=>$cart_count,'cart_total'=>number_format($cart_total,2,'.',''),'cart'=>$_SESSION['cart']]);
$conn->close();
exit;
