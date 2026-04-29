<?php
$conn = new mysqli("localhost", "root", "", "pecaaq");

$nome = $_POST['nome'];
$preco = $_POST['preco'];

// IMAGEM
$imgNome = $_FILES['img']['name'];
$tmp = $_FILES['img']['tmp_name'];

$caminho = "uploads/" . $imgNome;

// move para pasta
move_uploaded_file($tmp, $caminho);

// salva no banco
$sql = "INSERT INTO produtos (nome, preco, img) VALUES ('$nome', '$preco', '$caminho')";
$conn->query($sql);
?>