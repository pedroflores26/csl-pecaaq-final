<?php
// merge_cart_on_login.php (usa produtos)
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
if (!$raw) {
    echo json_encode(['status'=>'ok','message'=>'no_payload','cart_count'=>array_sum($_SESSION['cart']??[] )]);
    exit;
}
$data = json_decode($raw, true);
if ($data === null) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'JSON inválido']);
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

foreach ($data as $item) {
    $id = isset($item['id']) ? intval($item['id']) : (isset($item['id_produto']) ? intval($item['id_produto']) : 0);
    $qtd = isset($item['quantidade']) ? max(1,intval($item['quantidade'])) : 1;
    if ($id <= 0) continue;
    if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = ['quantidade'=>0,'preco'=>0,'nome'=>''];
    $_SESSION['cart'][$id]['quantidade'] += $qtd;
}

// calcular cart_count
$cart_count = 0;
foreach ($_SESSION['cart'] as $it) $cart_count += (int)$it['quantidade'];

echo json_encode(['status'=>'ok','message'=>'merged','cart_count'=>$cart_count,'cart'=>$_SESSION['cart']]);
exit;
