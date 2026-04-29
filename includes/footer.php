<?php
/**
 * Footer padrão global — PeçaAQ
 *
 * Variável opcional:
 *
 * $base
 * Caminho relativo até a raiz do projeto
 * Exemplo:
 * '../'
 * './'
 */

$base = isset($base) ? $base : '../';
?>

<head>
    <meta charset="UTF-8">
    <title>PeçaAQ</title>

    <link rel="stylesheet" href="<?php echo $base; ?>includes/layout.css">
</head>

<footer id="contato" class="main-footer">

    <div class="footer-grid">
        <link rel="stylesheet" href="<?php echo $base; ?>includes/layout.css">
        <!-- MARCA -->
        <div class="footer-brand">
            <img
                src="<?php echo $base; ?>includes/logo1.png"
                alt="Logo PeçaAQ"
            >

            <p>
                PeçaAQ conecta compradores e fornecedores de peças automotivas
                com agilidade, segurança e confiança em todo o processo.
            </p>

            <div class="footer-socials">
                <a href="#" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>

                <a href="#" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>

                <a href="#" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <!-- NAVEGAÇÃO -->
        <div class="footer-col">
            <h4>Navegação</h4>

            <ul>
                <li>
                    <a href="<?php echo $base; ?>LandingPage/indexLandingPage.php">
                        Home
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>Comprar/indexComprar.php">
                        Comprar
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>lojas/lojas.php">
                        Lojas
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>Sobre/index.php">
                        Sobre
                    </a>
                </li>
            </ul>
        </div>

        <!-- CONTA -->
        <div class="footer-col">
            <h4>Conta</h4>

            <ul>
                <li>
                    <a href="<?php echo $base; ?>login/indexLogin.php">
                        Login
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>Cadastrar/indexCadastro.php">
                        Cadastro
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>dashboard_cliente.php">
                        Meu Perfil
                    </a>
                </li>

                <li>
                    <a href="<?php echo $base; ?>dashboard_empresa.php">
                        Painel Empresa
                    </a>
                </li>
            </ul>
        </div>

        <!-- CONTATO -->
        <div class="footer-col">
            <h4>Contato</h4>

            <ul>
                <li>
                    <a href="#">
                        <i class="fas fa-phone"></i>
                        (51) 9 9999-9999
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fas fa-envelope"></i>
                        contato@pecaaq.com
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fas fa-map-marker-alt"></i>
                        Porto Alegre, RS
                    </a>
                </li>
            </ul>
        </div>

    </div>

    <!-- RODAPÉ FINAL -->
    <div class="footer-bottom">

        <span>
            © <?php echo date('Y'); ?> PeçaAQ.
            Todos os direitos reservados.
        </span>

        <span>
            Feito com
            <span style="color: var(--red);">♥</span>
            por estudantes de TI
        </span>

    </div>

</footer>