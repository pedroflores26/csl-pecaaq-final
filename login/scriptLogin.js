// ════════════════════════════════════════════════
//  PeçaAQ — scriptLogin.js
//  Validação client-side, máscaras e UX do login
// ════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {

  // ── Elementos principais ──────────────────────
  const tabs        = document.querySelectorAll('.tab');
  const formCliente = document.getElementById('form-cliente');
  const formEmpresa = document.getElementById('form-empresa');

  // ── Toggle entre abas ─────────────────────────
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');

      if (tab.dataset.tab === 'cliente') {
        formCliente.style.display = 'flex';
        formEmpresa.style.display = 'none';
      } else {
        formEmpresa.style.display = 'flex';
        formCliente.style.display = 'none';
      }

      // Limpa erros ao trocar de aba
      document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    });
  });

  // ── Toggle mostrar/ocultar senha ─────────────
  document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling;
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      btn.querySelector('i').className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
  });

  // ── Máscara CNPJ ─────────────────────────────
  const cnpjInput = document.getElementById('cnpj-empresa');
  if (cnpjInput) {
    cnpjInput.addEventListener('input', () => {
      let v = cnpjInput.value.replace(/\D/g, '').substring(0, 14);
      if (v.length > 12) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
      else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
      else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
      else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3})/, '$1.$2');
      cnpjInput.value = v;
    });
  }

  // ── Validações ────────────────────────────────
  function isEmailValido(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
  }

  function isCNPJValido(cnpj) {
    const nums = cnpj.replace(/\D/g, '');
    if (nums.length !== 14) return false;
    if (/^(\d)\1+$/.test(nums)) return false;

    const calc = (n) => {
      let sum = 0, pos = n - 7;
      for (let i = n; i >= 1; i--) {
        sum += parseInt(nums.charAt(n - i)) * pos--;
        if (pos < 2) pos = 9;
      }
      const r = sum % 11;
      return r < 2 ? 0 : 11 - r;
    };
    return calc(12) === parseInt(nums.charAt(12)) && calc(13) === parseInt(nums.charAt(13));
  }

  function showError(id, msg) {
    const el = document.getElementById(id);
    if (el) el.textContent = msg;
  }
  function clearError(id) { showError(id, ''); }

  function markField(inputEl, valid) {
    inputEl.classList.toggle('invalid', !valid);
  }

  // ── Submissão: Cliente ────────────────────────
  if (formCliente) {
    const emailInput = document.getElementById('email-cliente');
    const senhaInput = document.getElementById('senha-cliente');
    const btnCliente = document.getElementById('btn-cliente');

    // Validação em tempo real
    emailInput?.addEventListener('blur', () => {
      const ok = isEmailValido(emailInput.value);
      markField(emailInput, ok);
      showError('err-email-cliente', ok ? '' : 'Informe um e-mail válido.');
    });
    emailInput?.addEventListener('input', () => clearError('err-email-cliente'));

    formCliente.addEventListener('submit', e => {
      let valido = true;

      if (!isEmailValido(emailInput.value)) {
        showError('err-email-cliente', 'Informe um e-mail válido.');
        markField(emailInput, false);
        valido = false;
      }
      if (!senhaInput.value.trim()) {
        showError('err-senha-cliente', 'Informe sua senha.');
        markField(senhaInput, false);
        valido = false;
      }

      if (!valido) {
        e.preventDefault();
        return;
      }

      // Feedback visual de loading
      setLoading(btnCliente, true);
    });
  }

  // ── Submissão: Empresa ────────────────────────
  if (formEmpresa) {
    const cnpjEl  = document.getElementById('cnpj-empresa');
    const senhaEl = document.getElementById('senha-empresa');
    const btnEmp  = document.getElementById('btn-empresa');

    cnpjEl?.addEventListener('blur', () => {
      const ok = isCNPJValido(cnpjEl.value);
      markField(cnpjEl, ok);
      showError('err-cnpj', ok ? '' : 'CNPJ inválido. Verifique o número digitado.');
    });
    cnpjEl?.addEventListener('input', () => clearError('err-cnpj'));

    formEmpresa.addEventListener('submit', e => {
      let valido = true;

      if (!isCNPJValido(cnpjEl.value)) {
        showError('err-cnpj', 'CNPJ inválido. Verifique o número digitado.');
        markField(cnpjEl, false);
        valido = false;
      }
      if (!senhaEl.value.trim()) {
        showError('err-senha-empresa', 'Informe sua senha.');
        markField(senhaEl, false);
        valido = false;
      }

      if (!valido) {
        e.preventDefault();
        return;
      }

      setLoading(btnEmp, true);
    });
  }

  // ── Helper: estado de loading no botão ───────
  function setLoading(btn, state) {
    if (!btn) return;
    btn.disabled = state;
    btn.classList.toggle('loading', state);
    if (!state) return;
    // Timeout de segurança: remove loading após 8s se algo travar
    setTimeout(() => setLoading(btn, false), 8000);
  }

  // ── Exibe erro vindo da URL (?erro=...) ──────
  const params = new URLSearchParams(window.location.search);
  const erroURL = params.get('erro');
  if (erroURL) {
    // Remove o parâmetro da URL sem reload
    window.history.replaceState({}, '', window.location.pathname);
  }

});