"use strict";

const PRODUTOS = [];

const state = {
  produtos: [...PRODUTOS],
  filtrados: [...PRODUTOS],
  cart: [],
  page: 1,
  perPage: 9,
  viewMode: "grid",
  search: "",
  categorias: [],
  marcas: [],
  precoMin: 0,
  precoMax: 2000,
  sort: "relevancia",
  modalProduto: null,
  modalQty: 1,
};

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

function fmtPreco(val) {
  return Number(val || 0).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

function renderStars(rating) {
  rating = Number(rating || 0);
  const full = Math.floor(rating);
  const half = rating - full >= 0.5 ? 1 : 0;
  const empty = 5 - full - half;
  return "★".repeat(full) + (half ? "½" : "") + "☆".repeat(empty);
}

function showToast(msg, icon = "fa-check-circle") {
  const t = $("#toast");
  if (!t) return;
  t.innerHTML = `<i class="fas ${icon}"></i> ${msg}`;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 2800);
}

function applyFilters() {
  let result = [...state.produtos];

  if (state.search) {
    const q = state.search.toLowerCase();
    result = result.filter((p) =>
      p.nome.toLowerCase().includes(q) ||
      p.categoria.toLowerCase().includes(q) ||
      p.marca.toLowerCase().includes(q) ||
      p.desc.toLowerCase().includes(q)
    );
  }

  if (state.categorias.length) {
    result = result.filter((p) => state.categorias.includes(p.categoria));
  }

  if (state.marcas.length) {
    result = result.filter((p) => state.marcas.includes(p.marca));
  }

  result = result.filter(
    (p) => Number(p.preco) >= state.precoMin && Number(p.preco) <= state.precoMax
  );

  switch (state.sort) {
    case "menor_preco":
      result.sort((a, b) => a.preco - b.preco);
      break;
    case "maior_preco":
      result.sort((a, b) => b.preco - a.preco);
      break;
    case "nome_az":
      result.sort((a, b) => a.nome.localeCompare(b.nome));
      break;
    case "nome_za":
      result.sort((a, b) => b.nome.localeCompare(a.nome));
      break;
    default:
      result.sort((a, b) => (b.destaque ? 1 : 0) - (a.destaque ? 1 : 0));
  }

  state.filtrados = result;
  state.page = 1;
  renderProducts();
  renderPagination();
  renderResultsCount();
  renderActivePills();
}

function renderProducts() {
  const grid = $("#productsGrid");
  const emptyState = $("#emptyState");

  const start = (state.page - 1) * state.perPage;
  const page = state.filtrados.slice(start, start + state.perPage);

  if (!page.length) {
    grid.innerHTML = "";
    emptyState.style.display = "block";
    return;
  }

  emptyState.style.display = "none";

  grid.innerHTML = page.map((p, i) => `
    <article class="prod-card" data-id="${p.id}" style="animation-delay:${i * 0.05}s" role="button" tabindex="0">
      <div class="prod-card__img">
        <img src="${p.img}" alt="${p.nome}" loading="lazy" onerror="this.src='imgComprar/placeholder.jpg'">
        ${p.badge ? `<span class="prod-card__badge">${p.badge}</span>` : ""}
      </div>

      <div class="prod-card__body">
        <p class="prod-card__cat">${p.categoria}</p>
        <h3 class="prod-card__name">${p.nome}</h3>
        <p class="prod-card__brand">${p.marca}</p>

        <div class="prod-card__rating">
          <span class="stars">${renderStars(p.rating)}</span>
          <span class="rating-count">(${p.reviews})</span>
        </div>

        <div class="prod-card__footer">
          <div>
            <div class="prod-card__price">${fmtPreco(p.preco)}</div>
            <div class="prod-card__installment">em até 6x de ${fmtPreco(p.preco / 6)}</div>
          </div>

          <button class="prod-card__add" data-id="${p.id}" aria-label="Adicionar ao carrinho">
            <i class="fas fa-cart-plus"></i>
          </button>
        </div>
      </div>
    </article>
  `).join("");

  grid.querySelectorAll(".prod-card").forEach((card) => {
    card.addEventListener("click", (e) => {
      if (e.target.closest(".prod-card__add")) return;
      openModal(Number(card.dataset.id));
    });

    card.addEventListener("keydown", (e) => {
      if (e.key === "Enter") openModal(Number(card.dataset.id));
    });
  });

  grid.querySelectorAll(".prod-card__add").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      addToCart(Number(btn.dataset.id));
    });
  });
}

function renderResultsCount() {
  const el = $("#resultsCount");
  const total = state.filtrados.length;
  el.innerHTML = `<strong>${total}</strong> peça${total !== 1 ? "s" : ""} encontrada${total !== 1 ? "s" : ""}`;
}

function renderPagination() {
  const pag = $("#pagination");
  const totalPages = Math.ceil(state.filtrados.length / state.perPage);

  if (totalPages <= 1) {
    pag.innerHTML = "";
    return;
  }

  let html = "";

  if (state.page > 1) {
    html += `<button class="page-btn" data-p="${state.page - 1}"><i class="fas fa-chevron-left"></i></button>`;
  }

  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || Math.abs(i - state.page) <= 1) {
      html += `<button class="page-btn ${i === state.page ? "active" : ""}" data-p="${i}">${i}</button>`;
    } else if (Math.abs(i - state.page) === 2) {
      html += `<span style="color:var(--muted);padding:0 4px">…</span>`;
    }
  }

  if (state.page < totalPages) {
    html += `<button class="page-btn" data-p="${state.page + 1}"><i class="fas fa-chevron-right"></i></button>`;
  }

  pag.innerHTML = html;

  pag.querySelectorAll(".page-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      state.page = Number(btn.dataset.p);
      renderProducts();
      renderPagination();
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  });
}

function renderActivePills() {
  const wrap = $("#activeFilters");
  const pills = [];

  state.categorias.forEach((c) => {
    pills.push({
      label: c,
      remove: () => {
        state.categorias = state.categorias.filter((x) => x !== c);
        syncCheckboxes();
        applyFilters();
      },
    });
  });

  state.marcas.forEach((m) => {
    pills.push({
      label: m,
      remove: () => {
        state.marcas = state.marcas.filter((x) => x !== m);
        syncCheckboxes();
        applyFilters();
      },
    });
  });

  if (state.search) {
    pills.push({
      label: `"${state.search}"`,
      remove: () => {
        state.search = "";
        $("#searchInput").value = "";
        $("#searchClear").classList.remove("visible");
        applyFilters();
      },
    });
  }

  if (state.precoMax < 2000 || state.precoMin > 0) {
    pills.push({
      label: `R$ ${state.precoMin}–${state.precoMax}`,
      remove: () => {
        state.precoMin = 0;
        state.precoMax = 2000;
        $("#priceMin").value = "";
        $("#priceMax").value = "";
        $("#priceSlider").value = 2000;
        applyFilters();
      },
    });
  }

  wrap.innerHTML = pills.map((p, i) => `
    <span class="filter-pill">
      ${p.label}
      <button data-idx="${i}" aria-label="Remover filtro">
        <i class="fas fa-times"></i>
      </button>
    </span>
  `).join("");

  wrap.querySelectorAll(".filter-pill button").forEach((btn) => {
    btn.addEventListener("click", () => pills[Number(btn.dataset.idx)].remove());
  });
}

function syncCheckboxes() {
  $$('input[name="categoria"]').forEach((cb) => {
    cb.checked = state.categorias.includes(cb.value);
  });

  $$('input[name="marca"]').forEach((cb) => {
    cb.checked = state.marcas.includes(cb.value);
  });
}

function addToCart(id, qty = 1) {
  const prod = state.produtos.find((p) => Number(p.id) === Number(id));
  if (!prod) return;

  const existing = state.cart.find((i) => Number(i.id) === Number(id));

  if (existing) {
    existing.qty += qty;
  } else {
    state.cart.push({ ...prod, qty });
  }

  saveCart();
  renderCart();
  updateCartCount();
  showToast(`${prod.nome} adicionado ao carrinho!`);
}

function removeFromCart(id) {
  state.cart = state.cart.filter((i) => Number(i.id) !== Number(id));
  saveCart();
  renderCart();
  updateCartCount();
}

function changeQty(id, delta) {
  const item = state.cart.find((i) => Number(i.id) === Number(id));
  if (!item) return;

  item.qty = Math.max(1, item.qty + delta);

  saveCart();
  renderCart();
  updateCartCount();
}

function updateCartCount() {
  const total = state.cart.reduce((s, i) => s + i.qty, 0);
  const el = $("#cartCount");

  el.textContent = total;
  el.classList.add("bump");

  setTimeout(() => el.classList.remove("bump"), 250);
}

function renderCart() {
  const body = $("#cartItems");
  const empty = $("#cartEmpty");
  const footer = $("#cartFooter");

  if (!state.cart.length) {
    body.innerHTML = "";
    empty.style.display = "block";
    footer.style.display = "none";
    return;
  }

  empty.style.display = "none";
  footer.style.display = "block";

  body.innerHTML = state.cart.map((item) => `
    <li class="cart-item">
      <div class="cart-item__img">
        <img src="${item.img}" alt="${item.nome}" onerror="this.src='imgComprar/placeholder.jpg'">
      </div>

      <div class="cart-item__info">
        <p class="cart-item__name">${item.nome}</p>
        <p class="cart-item__brand">${item.marca}</p>

        <div class="cart-item__controls">
          <div class="cart-item__qty">
            <button data-id="${item.id}" data-delta="-1"><i class="fas fa-minus"></i></button>
            <span>${item.qty}</span>
            <button data-id="${item.id}" data-delta="1"><i class="fas fa-plus"></i></button>
          </div>

          <span class="cart-item__price">${fmtPreco(item.preco * item.qty)}</span>
        </div>
      </div>

      <button class="cart-item__remove" data-id="${item.id}" aria-label="Remover">
        <i class="fas fa-trash"></i>
      </button>
    </li>
  `).join("");

  const subtotal = state.cart.reduce((s, i) => s + i.preco * i.qty, 0);
  $("#cartSubtotal").textContent = fmtPreco(subtotal);

  body.querySelectorAll("[data-delta]").forEach((btn) => {
    btn.addEventListener("click", () =>
      changeQty(Number(btn.dataset.id), Number(btn.dataset.delta))
    );
  });

  body.querySelectorAll(".cart-item__remove").forEach((btn) => {
    btn.addEventListener("click", () => removeFromCart(Number(btn.dataset.id)));
  });
}

function saveCart() {
  try {
    localStorage.setItem("pecaaq_cart", JSON.stringify(state.cart));
  } catch (e) {}
}

function loadCart() {
  try {
    const saved = localStorage.getItem("pecaaq_cart");
    if (saved) state.cart = JSON.parse(saved);
  } catch (e) {}
}

function openModal(id) {
  const prod = state.produtos.find((p) => Number(p.id) === Number(id));
  if (!prod) return;

  state.modalProduto = prod;
  state.modalQty = 1;

  $("#modalImg").src = prod.img;
  $("#modalImg").alt = prod.nome;

  $("#modalBadge").textContent = prod.badge || "";
  $("#modalBadge").style.display = prod.badge ? "inline" : "none";

  $("#modalCategory").textContent = prod.categoria;
  $("#modalTitle").textContent = prod.nome;
  $("#modalRating").innerHTML = `<span class="stars">${renderStars(prod.rating)}</span> <span>${prod.rating} (${prod.reviews} avaliações)</span>`;
  $("#modalDesc").textContent = prod.desc;
  $("#modalMarca").textContent = prod.marca;
  $("#modalPrice").textContent = fmtPreco(prod.preco);
  $("#modalInstallment").textContent = `ou 6x de ${fmtPreco(prod.preco / 6)} sem juros`;
  $("#qtyValue").textContent = 1;

  $("#modalOverlay").classList.add("open");
  document.body.style.overflow = "hidden";
}

function closeModal() {
  $("#modalOverlay").classList.remove("open");
  document.body.style.overflow = "";
}

function setupHeader() {
  const btn = $("#headerLoginBtn");
  if (!btn) return;

  const userData = localStorage.getItem("usuarioLogado");

  if (!userData) {
    btn.textContent = "Faça seu login";
    btn.onclick = () => (window.location.href = "../login/indexLogin.php");
    return;
  }

  try {
    const u = JSON.parse(userData);

    btn.textContent = "Meu Perfil";
    btn.onclick = () => {
      window.location.href =
        u.tipo && u.tipo.toLowerCase() !== "cliente"
          ? "../dashboard_empresa.php"
          : "../dashboard_cliente.php?tab=perfil";
    };

    if (!document.querySelector(".btnSair")) {
      const sair = document.createElement("button");
      sair.textContent = "Sair";
      sair.className = "btnSair btn-header";
      sair.style.background = "#333";
      sair.style.marginLeft = "6px";

      btn.parentElement.appendChild(sair);

      sair.addEventListener("click", () => {
        localStorage.removeItem("usuarioLogado");
        localStorage.removeItem("pecaaq_cart");
        location.reload();
      });
    }
  } catch (e) {}
}

document.addEventListener("DOMContentLoaded", () => {
  loadCart();
  renderCart();
  updateCartCount();
  setupHeader();

  fetch("get_product.php")
    .then((r) => r.json())
    .then((data) => {
      if (data.status === "ok" && data.produtos.length > 0) {
        const prods = data.produtos.map((p) => ({
          id: Number(p.id),
          nome: p.nome || "",
          categoria: p.categoria || "Outros",
          marca: p.marca || "",
          preco: Number(p.preco || 0),
          precoOriginal: p.preco_original ? Number(p.preco_original) : null,
          img: p.img || "imgComprar/placeholder.jpg",
          desc: p.descricao || "",
          rating: Number(p.avaliacao_media || 0),
          reviews: Number(p.total_avaliacoes || 0),
          disponibilidade: p.disponibilidade || "em_estoque",
          badge: p.badge || "",
          destaque: Boolean(p.destaque),
          empresa: p.empresa || "",
        }));

        state.produtos = prods;
        state.filtrados = [...prods];
      }

      applyFilters();
    })
    .catch((err) => {
      console.error("Erro ao carregar produtos:", err);
      applyFilters();
    });

  const searchInput = $("#searchInput");
  const searchClear = $("#searchClear");

  searchInput.addEventListener("input", () => {
    state.search = searchInput.value.trim();
    searchClear.classList.toggle("visible", !!state.search);
    applyFilters();
  });

  searchClear.addEventListener("click", () => {
    searchInput.value = "";
    state.search = "";
    searchClear.classList.remove("visible");
    applyFilters();
    searchInput.focus();
  });

  $$('input[name="categoria"]').forEach((cb) => {
    cb.addEventListener("change", () => {
      if (cb.checked) {
        state.categorias.push(cb.value);
      } else {
        state.categorias = state.categorias.filter((c) => c !== cb.value);
      }

      applyFilters();
    });
  });

  $$('input[name="marca"]').forEach((cb) => {
    cb.addEventListener("change", () => {
      if (cb.checked) {
        state.marcas.push(cb.value);
      } else {
        state.marcas = state.marcas.filter((m) => m !== cb.value);
      }

      applyFilters();
    });
  });

  const priceMin = $("#priceMin");
  const priceMax = $("#priceMax");
  const priceSlider = $("#priceSlider");

  priceSlider.addEventListener("input", () => {
    state.precoMax = Number(priceSlider.value);
    priceMax.value = priceSlider.value;
    applyFilters();
  });

  priceMin.addEventListener("input", () => {
    state.precoMin = Number(priceMin.value) || 0;
    applyFilters();
  });

  priceMax.addEventListener("input", () => {
    state.precoMax = Number(priceMax.value) || 2000;
    priceSlider.value = state.precoMax;
    applyFilters();
  });

  function clearAllFilters() {
    state.categorias = [];
    state.marcas = [];
    state.precoMin = 0;
    state.precoMax = 2000;
    state.search = "";

    searchInput.value = "";
    searchClear.classList.remove("visible");

    priceSlider.value = 2000;
    priceMin.value = "";
    priceMax.value = "";

    syncCheckboxes();
    applyFilters();
  }

  $("#clearFilters").addEventListener("click", clearAllFilters);
  $("#emptyReset").addEventListener("click", clearAllFilters);
  $("#applyFilters").addEventListener("click", applyFilters);

  $("#sortSelect").addEventListener("change", (e) => {
    state.sort = e.target.value;
    applyFilters();
  });

  $("#gridViewBtn").addEventListener("click", () => {
    state.viewMode = "grid";
    $("#productsGrid").classList.remove("list-view");
    $("#gridViewBtn").classList.add("active");
    $("#listViewBtn").classList.remove("active");
  });

  $("#listViewBtn").addEventListener("click", () => {
    state.viewMode = "list";
    $("#productsGrid").classList.add("list-view");
    $("#listViewBtn").classList.add("active");
    $("#gridViewBtn").classList.remove("active");
  });

  $$(".filter-group__toggle").forEach((btn) => {
    btn.addEventListener("click", () => {
      const expanded = btn.getAttribute("aria-expanded") === "true";
      btn.setAttribute("aria-expanded", String(!expanded));
    });
  });

  const cartBtn = $("#cartBtn");
  const cartDrawer = $("#cartDrawer");
  const cartOverlay = $("#cartOverlay");
  const cartClose = $("#cartClose");

  function openCart() {
    cartDrawer.classList.add("open");
    cartOverlay.classList.add("open");
    document.body.style.overflow = "hidden";
  }

  function closeCart() {
    cartDrawer.classList.remove("open");
    cartOverlay.classList.remove("open");
    document.body.style.overflow = "";
  }

  cartBtn.addEventListener("click", openCart);
  cartClose.addEventListener("click", closeCart);
  cartOverlay.addEventListener("click", closeCart);

  $("#modalClose").addEventListener("click", closeModal);

  $("#modalOverlay").addEventListener("click", (e) => {
    if (e.target === $("#modalOverlay")) closeModal();
  });

  $("#qtyMinus").addEventListener("click", () => {
    state.modalQty = Math.max(1, state.modalQty - 1);
    $("#qtyValue").textContent = state.modalQty;
  });

  $("#qtyPlus").addEventListener("click", () => {
    state.modalQty++;
    $("#qtyValue").textContent = state.modalQty;
  });

  $("#modalAddCart").addEventListener("click", () => {
    if (state.modalProduto) {
      addToCart(state.modalProduto.id, state.modalQty);
      closeModal();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal();
      closeCart();
    }
  });

  const filterToggleMobile = $("#filterToggleMobile");
  const sidebar = $("#sidebar");

  filterToggleMobile.addEventListener("click", () => {
    sidebar.classList.toggle("mobile-open");
  });

  const toggle = document.querySelector(".nav__toggle");
  const nav = document.querySelector("nav");

  if (toggle && nav) {
    toggle.addEventListener("click", () => {
      const open = nav.style.display === "flex";
      nav.style.display = open ? "none" : "flex";
      toggle.setAttribute("aria-expanded", String(!open));
    });
  }
});