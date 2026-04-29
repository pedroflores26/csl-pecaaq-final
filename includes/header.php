<?php
/**
 * Header padrão global — PeçaAQ
 */

$base = isset($base) ? $base : '../';
$activePage = isset($activePage) ? $activePage : '';
$extraHeaderHtml = isset($extraHeaderHtml) ? $extraHeaderHtml : '';

function ativoMenu($pagina, $activePage) {
    return $activePage === $pagina ? ' class="nav-active"' : '';
}
?>

<header class="main-header">

    <!-- LOGO -->
    <a href="<?php echo $base; ?>LandingPage/indexLandingPage.php" class="logo-area">
        <img
            src="<?php echo $base; ?>includes/logo1.png"
            alt="Logo PeçaAQ"
        >
        <span>PEÇA<em>AQ</em></span>
    </a>

    <!-- MENU -->
    <nav class="main-nav">
        <ul>
            <li>
                <a
                    href="<?php echo $base; ?>LandingPage/indexLandingPage.php"
                    <?php echo ativoMenu('home', $activePage); ?>
                >
                    Home
                </a>
            </li>

            <li>
                <a
                    href="<?php echo $base; ?>Comprar/indexComprar.php"
                    <?php echo ativoMenu('comprar', $activePage); ?>
                >
                    Comprar
                </a>
            </li>

            <li>
                <a
                    href="<?php echo $base; ?>lojas/lojas.php"
                    <?php echo ativoMenu('lojas', $activePage); ?>
                >
                    Lojas
                </a>
            </li>

            <li>
                <a
                    href="<?php echo $base; ?>Sobre/index.php"
                    <?php echo ativoMenu('sobre', $activePage); ?>
                >
                    Sobre
                </a>
            </li>

            <li>
                <a href="#contato">
                    Contato
                </a>
            </li>
        </ul>
    </nav>

    <!-- AÇÕES -->
    <div class="header-actions">

        <?php echo $extraHeaderHtml; ?>

        <button
            type="button"
            class="btn-header"
            id="headerLoginBtn"
        >
            Faça seu login
        </button>

    </div>

    <!-- MENU MOBILE -->
    <button
        class="nav__toggle"
        aria-label="Menu"
        aria-expanded="false"
        id="menuToggle"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>

</header>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("headerLoginBtn");
    const toggle = document.getElementById("menuToggle");
    const nav = document.querySelector(".main-nav");

    const BASE = <?php echo json_encode($base); ?>;

    // LOGIN / PERFIL

    if (btn) {
        const userData = localStorage.getItem("usuarioLogado");

        if (!userData) {
            btn.textContent = "Faça seu login";

            btn.onclick = function () {
                window.location.href = BASE + "login/indexLogin.php";
            };
        } else {
            try {
                const usuario = JSON.parse(userData);

                btn.textContent = "Meu Perfil";

                btn.onclick = function () {
                    const tipo = (usuario.tipo || "").toLowerCase();

                    if (tipo && tipo !== "cliente") {
                        window.location.href = BASE + "dashboard_empresa.php";
                    } else {
                        window.location.href = BASE + "dashboard_cliente.php";
                    }
                };

                // BOTÃO SAIR

                if (!document.querySelector(".btnSair")) {
                    const sair = document.createElement("button");

                    sair.textContent = "Sair";
                    sair.className = "btn-header btnSair";
                    sair.style.background = "#2b2b2b";
                    sair.style.marginLeft = "8px";

                    btn.parentElement.appendChild(sair);

                    sair.addEventListener("click", function () {
                        localStorage.removeItem("usuarioLogado");
                        window.location.href = BASE + "login/indexLogin.php";
                    });
                }

            } catch (e) {
                console.log("Erro ao ler usuário:", e);
            }
        }
    }

    // MENU MOBILE

    if (toggle && nav) {
        toggle.addEventListener("click", function () {
            nav.classList.toggle("mobile-open");

            const aberto = nav.classList.contains("mobile-open");

            toggle.setAttribute(
                "aria-expanded",
                aberto ? "true" : "false"
            );
        });
    }

});
</script>