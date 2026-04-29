<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// ── CONFIG BANCO ─────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pecaaq');

// ── CONEXÃO ──────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode([
        'ok' => false,
        'erro' => 'Erro ao conectar no banco: ' . $conn->connect_error
    ]);
    exit;
}

$conn->set_charset('utf8mb4');

// ── HELPERS ──────────────────────────────
function jsonOk($msg){ echo json_encode(['ok'=>true,'msg'=>$msg]); exit; }
function jsonErr($msg){ echo json_encode(['ok'=>false,'erro'=>$msg]); exit; }

function limpar($v){ return trim(htmlspecialchars($v)); }
function soNum($v){ return preg_replace('/\D/','',$v); }

function validarEmail($e){
    return filter_var($e, FILTER_VALIDATE_EMAIL);
}

// ── RECEBE TIPO ──────────────────────────
$tipo = $_POST['tipo'] ?? '';

if (!$tipo) jsonErr('Tipo não enviado');


// ===================================================
// CLIENTE
// ===================================================
if ($tipo === 'cliente') {

    $nome  = limpar($_POST['nome'] ?? '');
    $email = limpar($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$nome) jsonErr('Nome obrigatório');
    if (!validarEmail($email)) jsonErr('Email inválido');
    if (strlen($senha) < 8) jsonErr('Senha muito curta');

    // Verifica duplicado
    $st = $conn->prepare("SELECT id FROM usuarios WHERE email=?");
    $st->bind_param("s", $email);
    $st->execute();
    if ($st->get_result()->num_rows > 0) {
        jsonErr('Email já cadastrado');
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $tipo_db = 'cliente';

    $st = $conn->prepare("
        INSERT INTO usuarios (nome, email, senha_hash, tipo)
        VALUES (?, ?, ?, ?)
    ");
    $st->bind_param("ssss", $nome, $email, $hash, $tipo_db);

    if ($st->execute()) {
        jsonOk('Cadastro realizado com sucesso!');
    } else {
        jsonErr('Erro ao cadastrar cliente');
    }
}


// ===================================================
// EMPRESA (CORRIGIDO)
// ===================================================
if ($tipo === 'empresa') {

    $nomeFantasia = limpar($_POST['nome_fantasia'] ?? '');
    $razao        = limpar($_POST['razao_social'] ?? '');
    $cnpj         = soNum($_POST['cnpj'] ?? '');
    $email        = limpar($_POST['email_comercial'] ?? '');
    $senha        = $_POST['senha'] ?? '';
    $telefone     = limpar($_POST['telefone_comercial'] ?? '');
    $whatsapp     = limpar($_POST['whatsapp'] ?? '');
    $categoria    = limpar($_POST['categoria_principal'] ?? '');

    if (!$nomeFantasia) jsonErr('Nome fantasia obrigatório');
    if (!$razao) jsonErr('Razão social obrigatória');
    if (strlen($cnpj) !== 14) jsonErr('CNPJ inválido');
    if (!validarEmail($email)) jsonErr('Email inválido');
    if (strlen($senha) < 8) jsonErr('Senha muito curta');

    // Verifica email duplicado
    $st = $conn->prepare("SELECT id FROM usuarios WHERE email=?");
    $st->bind_param("s", $email);
    $st->execute();
    if ($st->get_result()->num_rows > 0) {
        jsonErr('Email já cadastrado');
    }

    // Formata CNPJ
    $cnpjFmt = preg_replace(
        '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
        '$1.$2.$3/$4-$5',
        $cnpj
    );

    // ── TRANSAÇÃO ────────────────────────
    $conn->begin_transaction();

    try {

        // 1️⃣ INSERE USUÁRIO
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $tipo_db = 'empresa';

        $st = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha_hash, tipo)
            VALUES (?, ?, ?, ?)
        ");
        $st->bind_param("ssss", $nomeFantasia, $email, $hash, $tipo_db);
        $st->execute();

        $usuario_id = $conn->insert_id; // 🔥 IMPORTANTE

        $st->close();

        // 2️⃣ INSERE EMPRESA
        $st = $conn->prepare("
            INSERT INTO empresas (
                usuario_id,
                razao_social,
                nome_fantasia,
                cnpj,
                email_comercial,
                telefone_comercial,
                whatsapp,
                categoria_principal,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'aprovada')
        ");

        $st->bind_param(
            "isssssss",
            $usuario_id,
            $razao,
            $nomeFantasia,
            $cnpjFmt,
            $email,
            $telefone,
            $whatsapp,
            $categoria
        );

        $st->execute();
        $st->close();

        $conn->commit();

        jsonOk('Empresa cadastrada com sucesso!');

    } catch (Exception $e) {
        $conn->rollback();
        jsonErr('Erro ao cadastrar empresa');
    }
}

// ===================================================
jsonErr('Requisição inválida');