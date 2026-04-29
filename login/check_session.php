<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id_usuario'])) {
    echo json_encode(['logado' => false]);
    exit;
}

echo json_encode([
    'logado' => true,
    'id'     => (int) $_SESSION['id_usuario'],
    'nome'   => $_SESSION['nome']  ?? '',
    'email'  => $_SESSION['email'] ?? '', // só funciona se salvar no login
    'tipo'   => $_SESSION['tipo']  ?? '',
]);
exit;