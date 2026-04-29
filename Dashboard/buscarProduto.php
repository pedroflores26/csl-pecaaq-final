<?php
$conn = new mysqli("localhost", "root", "", "pecaaq");

if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

$produtos = [];

while($row = $result->fetch_assoc()){
    $produtos[] = $row;
}

echo json_encode($produtos);
?>