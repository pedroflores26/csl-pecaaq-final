<?php
header("Content-Type: application/json; charset=utf-8");
session_start();




try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli("localhost", "root", "", "pecaaq");
    $conn->set_charset("utf8mb4");

    // Verifica sessão
    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(['ok' => false, 'msg' => 'Usuário não autenticado.']);
        exit;
    }

    $id_usuario = intval($_SESSION['id_usuario']);

    // Verifica ID do produto
    $id_produto = $_POST['id_produto'] ?? 0;

    if ($id_produto == 0) {
        echo json_encode(['ok' => false, 'msg' => 'ID de produto inválido']);
        exit;
    }

    // Confirma se o produto pertence ao usuário logado
    $sql = "SELECT id_produto FROM produtos WHERE id_produto = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_produto, $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode(['ok' => false, 'msg' => 'Produto não pertence ao usuário']);
        exit;
    }

    // Excluir produto
    $sqlDelete = "DELETE FROM produtos WHERE id_produto = ? AND id_usuario = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $id_produto, $id_usuario);
    $stmtDelete->execute();

    echo json_encode(['ok' => true, 'msg' => 'Produto excluído com sucesso']);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
?>
