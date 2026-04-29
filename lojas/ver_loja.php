<?php
$conn = new mysqli("localhost", "root", "", "pecaaq");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/*
==================================================
PEGA O ID DA LOJA
==================================================
*/
$empresa_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($empresa_id <= 0) {
    die("Loja não encontrada.");
}

/*
==================================================
BUSCA DADOS DA EMPRESA
==================================================
*/
$sqlEmpresa = "SELECT nome_fantasia, avaliacao_media, total_vendas
               FROM empresas
               WHERE id = ? LIMIT 1";

$stmtEmpresa = $conn->prepare($sqlEmpresa);
$stmtEmpresa->bind_param("i", $empresa_id);
$stmtEmpresa->execute();
$resEmpresa = $stmtEmpresa->get_result();

if ($resEmpresa->num_rows === 0) {
    die("Loja não encontrada.");
}

$empresa = $resEmpresa->fetch_assoc();

/*
==================================================
BUSCA PRODUTOS DA EMPRESA
==================================================
*/
$sqlProdutos = "SELECT
                    id,
                    nome,
                    preco,
                    preco_promocional,
                    imagem_principal,
                    descricao_curta,
                    estoque,
                    destaque
                FROM produtos
                WHERE empresa_id = ?
                AND status = 'ativo'
                ORDER BY destaque DESC, id DESC";

$stmtProdutos = $conn->prepare($sqlProdutos);
$stmtProdutos->bind_param("i", $empresa_id);
$stmtProdutos->execute();
$resProdutos = $stmtProdutos->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($empresa['nome_fantasia']); ?> - Loja</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #0b0b0b;
    color: #fff;
}

.container {
    width: 90%;
    max-width: 1300px;
    margin: auto;
    padding: 40px 0;
}

.topo {
    margin-bottom: 40px;
}

.voltar {
    display: inline-block;
    margin-bottom: 20px;
    color: #ff2b2b;
    text-decoration: none;
    font-weight: bold;
}

.voltar:hover {
    opacity: .8;
}

h1 {
    font-size: 42px;
    margin-bottom: 10px;
}

.info-loja {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    color: #ccc;
    margin-top: 15px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(280px,1fr));
    gap: 25px;
}

.card {
    background: #151515;
    border: 1px solid #222;
    border-radius: 14px;
    overflow: hidden;
    transition: .3s;
}

.card:hover {
    transform: translateY(-5px);
    border-color: #ff2b2b;
}

.card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    background: #111;
}

.card-body {
    padding: 20px;
}

.card h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.desc {
    color: #aaa;
    font-size: 14px;
    margin-bottom: 15px;
    min-height: 40px;
}

.preco {
    font-size: 24px;
    color: #ff2b2b;
    font-weight: bold;
    margin-bottom: 10px;
}

.estoque {
    font-size: 14px;
    color: #bbb;
    margin-bottom: 15px;
}

.btn {
    display: inline-block;
    width: 100%;
    text-align: center;
    background: #ff2b2b;
    color: white;
    text-decoration: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    transition: .2s;
}

.btn:hover {
    background: #d80000;
}

.vazio {
    text-align: center;
    padding: 80px 0;
    color: #aaa;
    font-size: 18px;
}
</style>
</head>
<body>

<div class="container">

    <div class="topo">
        <a href="lojas.php" class="voltar">
            <i class="fas fa-arrow-left"></i> Voltar para lojas
        </a>

        <h1><?php echo htmlspecialchars($empresa['nome_fantasia']); ?></h1>

        <div class="info-loja">
            <span>
                <i class="fas fa-star"></i>
                Avaliação:
                <?php echo number_format($empresa['avaliacao_media'], 1, ',', '.'); ?>/5
            </span>

            <span>
                <i class="fas fa-cart-shopping"></i>
                <?php echo (int)$empresa['total_vendas']; ?> vendas
            </span>
        </div>
    </div>

    <?php if ($resProdutos->num_rows > 0): ?>

        <div class="grid">

            <?php while ($produto = $resProdutos->fetch_assoc()): ?>

                <?php
                $img = !empty($produto['imagem_principal'])
                    ? "../Dashboard/uploads/" . $produto['imagem_principal']
                    : "../Comprar/imgComprar/placeholder.jpg";

                $precoFinal = !empty($produto['preco_promocional'])
                    ? $produto['preco_promocional']
                    : $produto['preco'];
                ?>

                <div class="card">

                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">

                    <div class="card-body">

                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

                        <p class="desc">
                            <?php echo htmlspecialchars($produto['descricao_curta'] ?: 'Produto automotivo disponível nesta loja.'); ?>
                        </p>

                        <div class="preco">
                            R$ <?php echo number_format($precoFinal, 2, ',', '.'); ?>
                        </div>

                        <div class="estoque">
                            Estoque disponível:
                            <?php echo (int)$produto['estoque']; ?>
                        </div>

                        <a href="../Comprar/indexComprar.php" class="btn">
                            Comprar agora
                        </a>

                    </div>
                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="vazio">
            Esta loja ainda não possui produtos cadastrados.
        </div>

    <?php endif; ?>

</div>

</body>
</html>