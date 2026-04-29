<?php
session_start();

/* ─────────────────────────────────────────
   CONEXÃO
───────────────────────────────────────── */
$conn = new mysqli('localhost', 'root', '', 'pecaaq');
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/* ─────────────────────────────────────────
   DADOS DO FORM
───────────────────────────────────────── */
$tipo  = $_POST['tipo']  ?? '';
$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($tipo) || empty($login) || empty($senha)) {
    header('Location: indexLogin.php?erro=Preencha todos os campos');
    exit;
}

/* Remove máscara (caso seja CNPJ) */
$loginLimpo = preg_replace('/\D/', '', $login);

/* ─────────────────────────────────────────
   LOGIN ADMIN (EMAIL)
───────────────────────────────────────── */
if (strtolower($tipo) === 'admin') {

    $sql = "SELECT id, nome, sobrenome, email, senha_hash, tipo
            FROM usuarios
            WHERE email = ? AND tipo = 'admin'
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
}

/* ─────────────────────────────────────────
   LOGIN CLIENTE (EMAIL)
───────────────────────────────────────── */
else if (strtolower($tipo) === 'cliente') {

    $sql = "SELECT id, nome, sobrenome, email, senha_hash, tipo
            FROM usuarios
            WHERE email = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
}

/* ─────────────────────────────────────────
   LOGIN EMPRESA (CNPJ)
───────────────────────────────────────── */
else if (strtolower($tipo) === 'empresa') {

    $sql = "SELECT u.id, u.nome, u.sobrenome, u.email, u.senha_hash, u.tipo
            FROM usuarios u
            INNER JOIN empresas e ON e.usuario_id = u.id
            WHERE e.cnpj = ? LIMIT 1";

    /* Formata CNPJ igual ao banco */
    $cnpjFmt = preg_replace(
        '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
        '$1.$2.$3/$4-$5',
        $loginLimpo
    );

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cnpjFmt);
}

/* ─────────────────────────────────────────
   TIPO INVÁLIDO
───────────────────────────────────────── */
else {
    header('Location: indexLogin.php?erro=Tipo inválido');
    exit;
}

/* ─────────────────────────────────────────
   EXECUTA CONSULTA
───────────────────────────────────────── */
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header('Location: indexLogin.php?erro=Usuário não encontrado');
    exit;
}

$user = $res->fetch_assoc();

/* ─────────────────────────────────────────
   VERIFICA SENHA
───────────────────────────────────────── */
if (!password_verify($senha, $user['senha_hash'])) {
    header('Location: indexLogin.php?erro=Senha incorreta');
    exit;
}

/* ─────────────────────────────────────────
   SESSÃO
───────────────────────────────────────── */
$_SESSION['id_usuario'] = $user['id'];
$_SESSION['nome']       = $user['nome'];
$_SESSION['email']      = $user['email'];
$_SESSION['tipo']       = $user['tipo'];

/* ─────────────────────────────────────────
   REDIRECIONAMENTO
───────────────────────────────────────── */
$tipoUser = strtolower($user['tipo']);

if ($tipoUser === 'admin') {
    $destino = '../godmode.php';
}
elseif ($tipoUser === 'empresa') {
    $destino = '../dashboard_empresa.php';
}
else {
    $destino = '../dashboard_cliente.php';
}

/* ─────────────────────────────────────────
   LOCALSTORAGE
───────────────────────────────────────── */
$payload = json_encode([
    'id_usuario' => $user['id'],
    'nome'       => $user['nome'],
    'email'      => $user['email'],
    'tipo'       => $user['tipo']
]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redirecionando...</title>
</head>
<body>

<script>
const usuario = <?php echo $payload; ?>;

/* salva no navegador (interface) */
localStorage.setItem(
    'usuarioLogado',
    JSON.stringify(usuario)
);

/* redireciona automaticamente */
window.location.href = "<?php echo $destino; ?>";
</script>

</body>
</html>