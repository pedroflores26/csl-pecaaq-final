<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli("localhost", "root", "", "pecaaq");

if ($conn->connect_error) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro de conexão com o banco"
    ]);
    exit;
}

$conn->set_charset("utf8mb4");

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro na consulta: " . $conn->error
    ]);
    exit;
}

$categorias = [
    1 => "Motor",
    2 => "Suspensão",
    3 => "Freios",
    4 => "Elétrica",
    5 => "Transmissão",
    6 => "Filtros",
    7 => "Ignição",
    8 => "Arrefecimento"
];

$marcas = [
    1 => "Monroe",
    2 => "Moura",
    3 => "Gates",
    4 => "NGK",
    5 => "Bosch",
    6 => "Cofap",
    7 => "Fras-le",
    8 => "Mahle"
];

$produtos = [];

while ($row = $result->fetch_assoc()) {
    $imagem = trim($row["imagem_principal"] ?? "");

    $produtos[] = [
        "id" => (int)$row["id"],
        "nome" => $row["nome"] ?? "",

        "categoria" => $categorias[(int)($row["categoria_id"] ?? 0)] ?? "Outros",
        "marca" => $marcas[(int)($row["marca_id"] ?? 0)] ?? "",

        "preco" => (float)(
            !empty($row["preco_promocional"])
                ? $row["preco_promocional"]
                : $row["preco"]
        ),

        "img" => $imagem !== ""
            ? "../Dashboard/uploads/" . $imagem
            : "imgComprar/placeholder.jpg",

        "descricao" => !empty($row["descricao_curta"])
            ? $row["descricao_curta"]
            : ($row["descricao"] ?? ""),

        "avaliacao_media" => (float)($row["avaliacao_media"] ?? 0),
        "total_avaliacoes" => (int)($row["total_avaliacoes"] ?? 0),

        "disponibilidade" => $row["disponibilidade"] ?? "em_estoque",
        "destaque" => (bool)($row["destaque"] ?? 0),

        "badge" => !empty($row["destaque"]) ? "Destaque" : ""
    ];
}

echo json_encode([
    "status" => "ok",
    "produtos" => $produtos
], JSON_UNESCAPED_UNICODE);
?>