// ═══════════════════════════════════════════════════
//  PeçaAQ — scriptCadastro.js
//  Navega entre telas, valida steps, busca CEP
// ═══════════════════════════════════════════════════
 
document.addEventListener('DOMContentLoaded', () => {
 
  // ── Estado ──────────────────────────────────────
  const state = { tipoAtual: null, stepCliente: 1, stepEmpresa: 1 };
 
  // ── Telas ───────────────────────────────────────
  const telaEscolha     = document.getElementById('telaEscolha');
  const telaCadCliente  = document.getElementById('telaCadCliente');
  const telaCadEmpresa  = document.getElementById('telaCadEmpresa');
  const telaSucesso     = document.getElementById('telaSucesso');
  const backBtn         = document.getElementById('backBtn');
  const backLabel       = document.getElementById('backLabel');
 
  function showTela(id) {
    [telaEscolha, telaCadCliente, telaCadEmpresa, telaSucesso].forEach(t => {
      t.classList.remove('active');
    });
    document.getElementById(id).classList.add('active');
  }
 
  // ── Clique nos cards de tipo ─────────────────────
  document.getElementById('btnEscolhaCliente').addEventListener('click', () => {
    state.tipoAtual = 'c';
    showTela('telaCadCliente');
    setStep('c', 1);
    backLabel.textContent = 'Voltar';
    backBtn.onclick = (e) => { e.preventDefault(); irEscolha(); };
  });
 
  document.getElementById('btnEscolhaEmpresa').addEventListener('click', () => {
    state.tipoAtual = 'e';
    showTela('telaCadEmpresa');
    setStep('e', 1);
    backLabel.textContent = 'Voltar';
    backBtn.onclick = (e) => { e.preventDefault(); irEscolha(); };
  });
 
  // ── irEscolha (global p/ botões inline) ──────────
  window.irEscolha = function () {
    showTela('telaEscolha');
    backLabel.textContent = 'Já tenho conta';
    backBtn.onclick = null;
    backBtn.href = '../login/indexLogin.php';
  };
 
  // ── Navegação entre steps ────────────────────────
  window.nextStep = function (tipo, atual) {
    if (!validarStep(tipo, atual)) return;
    const prox = atual + 1;
    setStep(tipo, prox);
  };
 
  window.prevStep = function (tipo, atual) {
    setStep(tipo, atual - 1);
  };
 
  function setStep(tipo, num) {
    const prefixTela = tipo === 'c' ? 'cs' : 'es';
    const prefixBar  = tipo === 'c' ? 'stepsCliente' : 'stepsEmpresa';
 
    // Esconde todos os steps do form
    document.querySelectorAll(`#${prefixTela.slice(0,2) === 'cs' ? 'formCliente' : 'formEmpresa'} .form-step`).forEach(s => s.classList.remove('active'));
 
    const stepEl = document.getElementById(prefixTela + num);
    if (stepEl) stepEl.classList.add('active');
 
    // Atualiza dots
    const bar = document.getElementById(prefixBar);
    if (bar) {
      bar.querySelectorAll('.step-dot').forEach(dot => {
        const s = parseInt(dot.dataset.s);
        dot.classList.remove('active', 'done');
        if (s < num)  dot.classList.add('done');
        if (s === num) dot.classList.add('active');
      });
    }
 
    if (tipo === 'c') state.stepCliente = num;
    else state.stepEmpresa = num;
 
    // Scrolla pro topo do form
    document.querySelector('.cad-panel').scrollTo({ top: 0, behavior: 'smooth' });
  }
 
  // ── Toggle senha ─────────────────────────────────
  document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling;
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      btn.querySelector('i').className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
  });
 
  // ── Força da senha ───────────────────────────────
  function setupPassStrength(inputId, fillId, labelId) {
    const input = document.getElementById(inputId);
    const fill  = document.getElementById(fillId);
    const label = document.getElementById(labelId);
    if (!input || !fill || !label) return;
 
    input.addEventListener('input', () => {
      const v = input.value;
      let score = 0;
      if (v.length >= 8)               score++;
      if (/[A-Z]/.test(v))             score++;
      if (/[0-9]/.test(v))             score++;
      if (/[^A-Za-z0-9]/.test(v))      score++;
 
      const levels = [
        { pct: '0%',   color: 'transparent', text: '' },
        { pct: '25%',  color: '#e8192c',     text: 'Fraca' },
        { pct: '50%',  color: '#f39c12',     text: 'Média' },
        { pct: '75%',  color: '#3498db',     text: 'Boa' },
        { pct: '100%', color: '#27ae60',     text: 'Forte' },
      ];
      const lvl = levels[score] ?? levels[0];
      fill.style.width     = lvl.pct;
      fill.style.background = lvl.color;
      label.textContent    = lvl.text;
      label.style.color    = lvl.color;
    });
  }
  setupPassStrength('c-senha', 'c-sfill', 'c-slabel');
  setupPassStrength('e-senha', 'e-sfill', 'e-slabel');
 
  // ── Máscaras ─────────────────────────────────────
  function maskCPF(input) {
    input.addEventListener('input', () => {
      let v = input.value.replace(/\D/g,'').slice(0,11);
      if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2})/,'$1.$2.$3-$4');
      else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3})/,'$1.$2.$3');
      else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3})/,'$1.$2');
      input.value = v;
    });
  }
  function maskCNPJ(input) {
    input.addEventListener('input', () => {
      let v = input.value.replace(/\D/g,'').slice(0,14);
      if (v.length > 12) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/,'$1.$2.$3/$4-$5');
      else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4})/,'$1.$2.$3/$4');
      else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3})/,'$1.$2.$3');
      else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3})/,'$1.$2');
      input.value = v;
    });
  }
  function maskPhone(input) {
    input.addEventListener('input', () => {
      let v = input.value.replace(/\D/g,'').slice(0,11);
      if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{0,4})/,'($1) $2-$3');
      else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4})/,'($1) $2-$3');
      else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/,'($1) $2');
      input.value = v;
    });
  }
  function maskCEP(input, prefix) {
    input.addEventListener('input', () => {
      let v = input.value.replace(/\D/g,'').slice(0,8);
      if (v.length > 5) v = v.replace(/^(\d{5})(\d{0,3})/,'$1-$2');
      input.value = v;
      if (v.replace('-','').length === 8) buscarCEP(v, prefix);
    });
  }
 
  maskCPF(document.getElementById('c-cpf'));
  maskPhone(document.getElementById('c-tel'));
  maskCEP(document.getElementById('c-cep'), 'c');
 
  maskCNPJ(document.getElementById('e-cnpj'));
  maskCPF(document.getElementById('e-resp-cpf'));
  maskPhone(document.getElementById('e-telcom'));
  maskPhone(document.getElementById('e-whats'));
  maskPhone(document.getElementById('e-resp-tel'));
  maskCEP(document.getElementById('e-cep'), 'e');
 
  // Estado UF maiúsculo automático
  ['c-estado','e-estado'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => { el.value = el.value.toUpperCase(); });
  });
 
  // ── Busca CEP via ViaCEP ─────────────────────────
  async function buscarCEP(cep, prefix) {
    const num = cep.replace(/\D/g,'');
    if (num.length !== 8) return;
    try {
      const res  = await fetch(`https://viacep.com.br/ws/${num}/json/`);
      const data = await res.json();
      if (data.erro) return;
      const set = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
      set(`${prefix}-logradouro`, data.logradouro || '');
      set(`${prefix}-bairro`,     data.bairro     || '');
      set(`${prefix}-cidade`,     data.localidade || '');
      set(`${prefix}-estado`,     data.uf         || '');
    } catch {}
  }
 
  // ── Validações ───────────────────────────────────
  function isEmail(v)   { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()); }
  function isCPF(v)     {
    const n = v.replace(/\D/g,'');
    if (n.length !== 11 || /^(\d)\1+$/.test(n)) return false;
    let s = 0;
    for (let i=0;i<9;i++) s += +n[i]*(10-i);
    let r = (s*10)%11; if (r===10||r===11) r=0;
    if (r !== +n[9]) return false;
    s=0; for (let i=0;i<10;i++) s += +n[i]*(11-i);
    r=(s*10)%11; if (r===10||r===11) r=0;
    return r === +n[10];
  }
  function isCNPJ(v)    {
    const n = v.replace(/\D/g,'');
    if (n.length !== 14 || /^(\d)\1+$/.test(n)) return false;
    const calc = (len) => {
      let s=0, pos=len-7;
      for (let i=len;i>=1;i--) { s += +n[len-i]*pos--; if (pos<2) pos=9; }
      const r=s%11; return r<2?0:11-r;
    };
    return calc(12)=== +n[12] && calc(13)=== +n[13];
  }
 
  function err(id, msg) { const el=document.getElementById(id); if(el) el.textContent=msg; }
  function ok(id)       { err(id,''); }
  function mark(el, valid) { if(el){ el.classList.toggle('invalid',!valid); el.classList.toggle('valid',valid); } }
 
  function validarStep(tipo, step) {
    let valido = true;
    const v = (id, condition, errId, msg) => {
      const el = document.getElementById(id);
      if (!condition) { mark(el,false); err(errId,msg); valido=false; }
      else { mark(el,true); ok(errId); }
    };
 
    if (tipo === 'c') {
      switch(step) {
        case 1:
          v('c-nome', document.getElementById('c-nome')?.value.trim().length >= 2, 'err-c-nome', 'Informe seu nome.');
          v('c-sobrenome', document.getElementById('c-sobrenome')?.value.trim().length >= 2, 'err-c-sobrenome', 'Informe seu sobrenome.');
          v('c-cpf', isCPF(document.getElementById('c-cpf')?.value||''), 'err-c-cpf', 'CPF inválido.');
          v('c-nasc', !!document.getElementById('c-nasc')?.value, 'err-c-nasc', 'Informe sua data de nascimento.');
          v('c-tel', document.getElementById('c-tel')?.value.replace(/\D/g,'').length >= 10, 'err-c-tel', 'Telefone inválido.');
          break;
        case 2:
          v('c-email', isEmail(document.getElementById('c-email')?.value||''), 'err-c-email', 'E-mail inválido.');
          v('c-senha', (document.getElementById('c-senha')?.value||'').length >= 8, 'err-c-senha', 'Senha deve ter ao menos 8 caracteres.');
          const cs = document.getElementById('c-senha')?.value;
          const cc = document.getElementById('c-conf')?.value;
          v('c-conf', cs && cs === cc, 'err-c-conf', 'As senhas não coincidem.');
          break;
        case 3:
          v('c-cep', document.getElementById('c-cep')?.value.replace(/\D/g,'').length === 8, 'err-c-cep', 'CEP inválido.');
          v('c-numero', !!document.getElementById('c-numero')?.value.trim(), 'err-c-numero', 'Informe o número.');
          const tc = document.getElementById('c-terms');
          if (!tc?.checked) { err('err-c-terms','Você precisa aceitar os termos.'); valido=false; }
          else ok('err-c-terms');
          break;
      }
    } else {
      switch(step) {
        case 1:
          v('e-razao', document.getElementById('e-razao')?.value.trim().length >= 3, 'err-e-razao', 'Informe a razão social.');
          v('e-fantasia', document.getElementById('e-fantasia')?.value.trim().length >= 2, 'err-e-fantasia', 'Informe o nome fantasia.');
          v('e-cnpj', isCNPJ(document.getElementById('e-cnpj')?.value||''), 'err-e-cnpj', 'CNPJ inválido.');
          v('e-categoria', !!document.getElementById('e-categoria')?.value, 'err-e-categoria', 'Selecione uma categoria.');
          v('e-telcom', document.getElementById('e-telcom')?.value.replace(/\D/g,'').length >= 10, 'err-e-telcom', 'Telefone inválido.');
          break;
        case 2:
          v('e-resp-nome', document.getElementById('e-resp-nome')?.value.trim().length >= 2, 'err-e-resp-nome', 'Informe o nome.');
          v('e-resp-sob',  document.getElementById('e-resp-sob')?.value.trim().length >= 2,  'err-e-resp-sob',  'Informe o sobrenome.');
          v('e-resp-cpf',  isCPF(document.getElementById('e-resp-cpf')?.value||''),          'err-e-resp-cpf',  'CPF inválido.');
          v('e-resp-tel',  document.getElementById('e-resp-tel')?.value.replace(/\D/g,'').length >= 10, 'err-e-resp-tel', 'Telefone inválido.');
          break;
        case 3:
          v('e-email', isEmail(document.getElementById('e-email')?.value||''), 'err-e-email', 'E-mail inválido.');
          v('e-senha', (document.getElementById('e-senha')?.value||'').length >= 8, 'err-e-senha', 'Mínimo 8 caracteres.');
          const es = document.getElementById('e-senha')?.value;
          const ec = document.getElementById('e-conf')?.value;
          v('e-conf', es && es === ec, 'err-e-conf', 'As senhas não coincidem.');
          break;
        case 4:
          v('e-cep', document.getElementById('e-cep')?.value.replace(/\D/g,'').length === 8, 'err-e-cep', 'CEP inválido.');
          v('e-numero', !!document.getElementById('e-numero')?.value.trim(), 'err-e-numero', 'Informe o número.');
          const te = document.getElementById('e-terms');
          if (!te?.checked) { err('err-e-terms','Você precisa aceitar os termos.'); valido=false; }
          else ok('err-e-terms');
          break;
      }
    }
    return valido;
  }
 
  // ── Submit dos formulários ───────────────────────
  async function submitForm(formId, btnId, alertId) {
    const form = document.getElementById(formId);
    const btn  = document.getElementById(btnId);
    if (!form) return;
 
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      btn.classList.add('loading');
      btn.disabled = true;
 
      const data = new FormData(form);
 
      try {
        const res  = await fetch('cadastro.php', { method:'POST', body: data });
        const json = await res.json();
 
        if (json.ok) {
          showTela('telaSucesso');
          document.getElementById('sucessoMsg').textContent =
            json.msg || 'Cadastro realizado com sucesso!';
        } else {
          const alertEl = document.getElementById(alertId);
          if (alertEl) {
            alertEl.textContent = json.erro || 'Erro ao cadastrar. Tente novamente.';
            alertEl.style.display = 'flex';
          }
        }
      } catch {
        const alertEl = document.getElementById(alertId);
        if (alertEl) {
          alertEl.textContent = 'Erro de conexão. Verifique e tente novamente.';
          alertEl.style.display = 'flex';
        }
      } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
      }
    });
  }
 
  submitForm('formCliente', 'btnSubmitCliente', 'alertCliente');
  submitForm('formEmpresa', 'btnSubmitEmpresa', 'alertEmpresa');
});