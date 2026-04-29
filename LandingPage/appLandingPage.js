document.addEventListener("DOMContentLoaded", () => {
  // ---------- BOTÃO EXPLORAR ----------
  const explorarBtn = document.getElementById("explorarBtn");
  if (explorarBtn) {
    explorarBtn.addEventListener("click", () => {
      try {
        // Sempre vai para o catálogo de compras
        window.location.href = "../Comprar/indexComprar.php";
      } catch (err) {
        console.error("Erro ao redirecionar no explorarBtn:", err);
      }
    });
  } else {
    console.warn("Botão 'explorarBtn' não encontrado no DOM. Verifique se o id está correto no HTML.");
  }

  // ---------- (restante do seu código existente) ----------
  // perfil/login/logout (exemplo simplificado - mantenha sua lógica já funcional)
  const userArea = document.querySelector(".btn-cadastro");
  const usuarioData = localStorage.getItem("usuarioLogado");

  if (!usuarioData) {
    if (userArea) {
      userArea.textContent = "Faça seu login";
      userArea.onclick = () => window.location.href = "../login/indexLogin.php";
      userArea.style.display = "inline-block";
    }
  } else {
    try {
      const usuario = JSON.parse(usuarioData);

      // Se acabou de logar (flag do login), redireciona para o catálogo
      if (sessionStorage.getItem("recem_logado")) {
        sessionStorage.removeItem("recem_logado");
        window.location.href = "../Comprar/indexComprar.php";
        return;
      }

      if (userArea) {
        userArea.textContent = "Perfil";
        userArea.style.background = usuario.tipo && usuario.tipo.toLowerCase() === "empresa"
          ? "#f39c12"
          : "#27ae60";
        userArea.onclick = () => {
          if (usuario.tipo && (usuario.tipo.toLowerCase() === "empresa" || usuario.tipo.toLowerCase() === "fornecedor")) {
            window.location.href = "../dashboard_empresa.php";
          } else {
            window.location.href = "../dashboard_cliente.php";
          }
        };
      }

      // cria botão sair se não existir
      let logoutBtn = document.querySelector(".btnSair");
      if (!logoutBtn) {
        logoutBtn = document.createElement("button");
        logoutBtn.textContent = "Sair";
        logoutBtn.className = "btnSair";
        logoutBtn.style.marginLeft = "10px";
        logoutBtn.style.background = "#e74c3c";
        logoutBtn.style.border = "none";
        logoutBtn.style.color = "#fff";
        logoutBtn.style.padding = "10px 20px";
        logoutBtn.style.borderRadius = "10px";
        logoutBtn.style.cursor = "pointer";
        userArea.parentElement.appendChild(logoutBtn);
        logoutBtn.addEventListener("click", () => {
          localStorage.removeItem("usuarioLogado");
          location.reload();
        });
      }
    } catch (err) {
      console.warn("Erro ao processar usuarioLogado:", err);
    }
  }

  // ---------- CARROSSEL (se estiver aqui) ----------
  (function initHero() {
    const slides = document.querySelectorAll(".hero__slide");
    if (!slides.length) return;
    let index = 0;
    const show = (i) => slides.forEach((s, idx) => s.classList.toggle("active", idx === i));
    show(0);
    setInterval(() => {
      index = (index + 1) % slides.length;
      show(index);
    }, 5000);
  })();

  // ---------- MENU MOBILE (se estiver aqui) ----------
  const toggle = document.querySelector(".nav__toggle");
  const nav = document.querySelector(".nav");
  if (toggle && nav) {
    toggle.addEventListener("click", () => {
      const open = nav.style.display === "flex";
      nav.style.display = open ? "none" : "flex";
      toggle.setAttribute("aria-expanded", String(!open));
    });
  }
});
