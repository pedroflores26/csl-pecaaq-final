<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PeçaAQ — Cadastro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="styleCadastro.css"/>
</head>
<body>
 
  <!-- ════════ VISUAL ESQUERDO ════════ -->
  <div class="cad-visual" aria-hidden="true">
    <div class="cad-visual__overlay"></div>
    <div class="cad-visual__content">
      <a href="../LandingPage/indexLandingPage.php" class="visual-logo">
        <img src="../login/imgLogin/LogoPecaAq4.png" alt="PeçaAQ">
        <span>PEÇA<em>AQ</em></span>
      </a>
      <div class="visual-cards">
        <div class="vcard"><i class="fas fa-user-check"></i><span>Cadastro rápido e gratuito</span></div>
        <div class="vcard"><i class="fas fa-shield-halved"></i><span>Dados protegidos com criptografia</span></div>
        <div class="vcard"><i class="fas fa-truck-fast"></i><span>Entrega para todo o Brasil</span></div>
        <div class="vcard"><i class="fas fa-star"></i><span>+5.000 peças disponíveis</span></div>
      </div>
      <blockquote class="visual-quote">
        "Junte-se a milhares de<br>motoristas que já confiam<br>na <strong>PeçaAQ</strong>."
      </blockquote>
    </div>
  </div>
 
  <!-- ════════ PAINEL DIREITO ════════ -->
  <main class="cad-panel">
    <a href="../login/indexLogin.php" class="back-btn" id="backBtn">
      <i class="fas fa-arrow-left"></i> <span id="backLabel">Já tenho conta</span>
    </a>
 
    <div class="cad-box">
      <div class="cad-box__logo">
        <img src="../login/imgLogin/LogoPecaAq4.png" alt="PeçaAQ">
        <span>PEÇA<em>AQ</em></span>
      </div>
 
      <!-- ══ TELA 0: Escolha ══ -->
      <div id="telaEscolha" class="tela active">
        <h1 class="cad-title">Criar sua conta</h1>
        <p class="cad-sub">Como você vai usar a PeçaAQ?</p>
        <div class="tipo-cards">
          <button class="tipo-card" id="btnEscolhaCliente">
            <div class="tipo-card__icon"><i class="fas fa-user"></i></div>
            <div class="tipo-card__text">
              <strong>Sou Usuário / Cliente</strong>
              <span>Quero comprar peças automotivas</span>
            </div>
            <i class="fas fa-chevron-right tipo-card__arrow"></i>
          </button>
          <button class="tipo-card" id="btnEscolhaEmpresa">
            <div class="tipo-card__icon empresa"><i class="fas fa-building"></i></div>
            <div class="tipo-card__text">
              <strong>Sou Empresa / Fornecedor</strong>
              <span>Quero vender peças na plataforma</span>
            </div>
            <i class="fas fa-chevron-right tipo-card__arrow"></i>
          </button>
        </div>
        <p class="hint" style="margin-top:28px">
          Já tem uma conta? <a href="../login/indexLogin.php" class="link-login">Entrar agora</a>
        </p>
      </div>
 
      <!-- ══ TELA CLIENTE ══ -->
      <div id="telaCadCliente" class="tela">
        <div class="steps-bar" id="stepsCliente">
          <div class="step-dot active" data-s="1"><span>1</span><em>Pessoal</em></div>
          <div class="step-line"></div>
          <div class="step-dot" data-s="2"><span>2</span><em>Acesso</em></div>
          <div class="step-line"></div>
          <div class="step-dot" data-s="3"><span>3</span><em>Endereço</em></div>
        </div>
 
        <form id="formCliente" method="POST" action="cadastro.php" novalidate>
          <input type="hidden" name="tipo" value="cliente">
 
          <!-- C-Step 1 -->
          <div class="form-step active" id="cs1">
            <h2 class="step-title"><i class="fas fa-user"></i> Dados Pessoais</h2>
            <div class="form-row">
              <div class="form-field">
                <label for="c-nome">Nome *</label>
                <div class="input-wrap"><i class="fas fa-user"></i>
                  <input type="text" id="c-nome" name="nome" placeholder="Seu nome" required maxlength="120">
                </div>
                <span class="field-error" id="err-c-nome"></span>
              </div>
              <div class="form-field">
                <label for="c-sobrenome">Sobrenome *</label>
                <div class="input-wrap"><i class="fas fa-user"></i>
                  <input type="text" id="c-sobrenome" name="sobrenome" placeholder="Seu sobrenome" required maxlength="120">
                </div>
                <span class="field-error" id="err-c-sobrenome"></span>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="c-cpf">CPF *</label>
                <div class="input-wrap"><i class="fas fa-id-card"></i>
                  <input type="text" id="c-cpf" name="cpf" placeholder="000.000.000-00" required maxlength="14">
                </div>
                <span class="field-error" id="err-c-cpf"></span>
              </div>
              <div class="form-field">
                <label for="c-nasc">Data de Nascimento *</label>
                <div class="input-wrap"><i class="fas fa-calendar"></i>
                  <input type="date" id="c-nasc" name="data_nascimento" required>
                </div>
                <span class="field-error" id="err-c-nasc"></span>
              </div>
            </div>
            <div class="form-field">
              <label for="c-tel">Telefone / WhatsApp *</label>
              <div class="input-wrap"><i class="fas fa-phone"></i>
                <input type="tel" id="c-tel" name="telefone" placeholder="(51) 9 9999-9999" required maxlength="16">
              </div>
              <span class="field-error" id="err-c-tel"></span>
            </div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="irEscolha()"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="button" class="btn-primary" onclick="nextStep('c',1)">Próximo <i class="fas fa-arrow-right"></i></button>
            </div>
          </div>
 
          <!-- C-Step 2 -->
          <div class="form-step" id="cs2">
            <h2 class="step-title"><i class="fas fa-lock"></i> Dados de Acesso</h2>
            <div class="form-field">
              <label for="c-email">E-mail *</label>
              <div class="input-wrap"><i class="fas fa-envelope"></i>
                <input type="email" id="c-email" name="email" placeholder="seu@email.com" required>
              </div>
              <span class="field-error" id="err-c-email"></span>
            </div>
            <div class="form-field">
              <label for="c-senha">Senha *</label>
              <div class="input-wrap"><i class="fas fa-lock"></i>
                <input type="password" id="c-senha" name="senha" placeholder="Mínimo 8 caracteres" required minlength="8">
                <button type="button" class="toggle-pass"><i class="fas fa-eye"></i></button>
              </div>
              <div class="pass-strength">
                <div class="strength-bar"><div class="strength-fill" id="c-sfill"></div></div>
                <span id="c-slabel"></span>
              </div>
              <span class="field-error" id="err-c-senha"></span>
            </div>
            <div class="form-field">
              <label for="c-conf">Confirmar Senha *</label>
              <div class="input-wrap"><i class="fas fa-lock"></i>
                <input type="password" id="c-conf" name="confirmar_senha" placeholder="Repita a senha" required>
                <button type="button" class="toggle-pass"><i class="fas fa-eye"></i></button>
              </div>
              <span class="field-error" id="err-c-conf"></span>
            </div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="prevStep('c',2)"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="button" class="btn-primary" onclick="nextStep('c',2)">Próximo <i class="fas fa-arrow-right"></i></button>
            </div>
          </div>
 
          <!-- C-Step 3 -->
          <div class="form-step" id="cs3">
            <h2 class="step-title"><i class="fas fa-map-marker-alt"></i> Endereço</h2>
            <div class="form-row">
              <div class="form-field" style="flex:0 0 150px">
                <label for="c-cep">CEP *</label>
                <div class="input-wrap"><i class="fas fa-search"></i>
                  <input type="text" id="c-cep" name="cep" placeholder="00000-000" required maxlength="9">
                </div>
                <span class="field-error" id="err-c-cep"></span>
              </div>
              <div class="form-field">
                <label for="c-logradouro">Logradouro *</label>
                <div class="input-wrap"><i class="fas fa-road"></i>
                  <input type="text" id="c-logradouro" name="logradouro" placeholder="Rua, Avenida..." required>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field" style="flex:0 0 115px">
                <label for="c-numero">Número *</label>
                <div class="input-wrap"><i class="fas fa-hashtag"></i>
                  <input type="text" id="c-numero" name="numero" placeholder="123" required>
                </div>
                <span class="field-error" id="err-c-numero"></span>
              </div>
              <div class="form-field">
                <label for="c-comp">Complemento</label>
                <div class="input-wrap"><i class="fas fa-door-open"></i>
                  <input type="text" id="c-comp" name="complemento" placeholder="Apto, Bloco...">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="c-bairro">Bairro *</label>
                <div class="input-wrap"><i class="fas fa-map"></i>
                  <input type="text" id="c-bairro" name="bairro" placeholder="Bairro" required>
                </div>
              </div>
              <div class="form-field">
                <label for="c-cidade">Cidade *</label>
                <div class="input-wrap"><i class="fas fa-city"></i>
                  <input type="text" id="c-cidade" name="cidade" placeholder="Cidade" required>
                </div>
              </div>
              <div class="form-field" style="flex:0 0 86px">
                <label for="c-estado">UF *</label>
                <div class="input-wrap"><i class="fas fa-flag"></i>
                  <input type="text" id="c-estado" name="estado" placeholder="RS" required maxlength="2">
                </div>
              </div>
            </div>
            <label class="terms-check">
              <input type="checkbox" id="c-terms" name="aceitar_termos" required>
              <span>Li e aceito os <a href="#" target="_blank">Termos de Uso</a> e a <a href="#" target="_blank">Política de Privacidade</a></span>
            </label>
            <span class="field-error" id="err-c-terms"></span>
            <div class="alert alert-error" id="alertCliente" style="display:none"></div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="prevStep('c',3)"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="submit" class="btn-primary btn-submit" id="btnSubmitCliente">
                <span>Criar Conta</span><i class="fas fa-check"></i><div class="spinner"></div>
              </button>
            </div>
          </div>
        </form>
      </div>
 
      <!-- ══ TELA EMPRESA ══ -->
      <div id="telaCadEmpresa" class="tela">
        <div class="steps-bar" id="stepsEmpresa">
          <div class="step-dot active" data-s="1"><span>1</span><em>Empresa</em></div>
          <div class="step-line"></div>
          <div class="step-dot" data-s="2"><span>2</span><em>Responsável</em></div>
          <div class="step-line"></div>
          <div class="step-dot" data-s="3"><span>3</span><em>Acesso</em></div>
          <div class="step-line"></div>
          <div class="step-dot" data-s="4"><span>4</span><em>Endereço</em></div>
        </div>
 
        <form id="formEmpresa" method="POST" action="cadastro.php" novalidate>
          <input type="hidden" name="tipo" value="empresa">
 
          <!-- E-Step 1 -->
          <div class="form-step active" id="es1">
            <h2 class="step-title"><i class="fas fa-building"></i> Dados da Empresa</h2>
            <div class="form-field">
              <label for="e-razao">Razão Social *</label>
              <div class="input-wrap"><i class="fas fa-building"></i>
                <input type="text" id="e-razao" name="razao_social" placeholder="Razão Social Ltda." required maxlength="200">
              </div>
              <span class="field-error" id="err-e-razao"></span>
            </div>
            <div class="form-field">
              <label for="e-fantasia">Nome Fantasia *</label>
              <div class="input-wrap"><i class="fas fa-store"></i>
                <input type="text" id="e-fantasia" name="nome_fantasia" placeholder="Nome visível na plataforma" required maxlength="200">
              </div>
              <span class="field-error" id="err-e-fantasia"></span>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="e-cnpj">CNPJ *</label>
                <div class="input-wrap"><i class="fas fa-id-card"></i>
                  <input type="text" id="e-cnpj" name="cnpj" placeholder="00.000.000/0000-00" required maxlength="18">
                </div>
                <span class="field-error" id="err-e-cnpj"></span>
              </div>
              <div class="form-field">
                <label for="e-ie">Insc. Estadual</label>
                <div class="input-wrap"><i class="fas fa-file-alt"></i>
                  <input type="text" id="e-ie" name="inscricao_estadual" placeholder="Opcional" maxlength="30">
                </div>
              </div>
            </div>
            <div class="form-field">
              <label for="e-categoria">Categoria Principal *</label>
              <div class="input-wrap"><i class="fas fa-tag"></i>
                <select id="e-categoria" name="categoria_principal" required>
                  <option value="" disabled selected>Selecione...</option>
                  <option>Motor</option><option>Suspensão</option><option>Freios</option>
                  <option>Elétrica</option><option>Transmissão</option><option>Filtros</option>
                  <option>Ignição</option><option>Arrefecimento</option><option>Carroceria</option>
                  <option>Acessórios</option><option>Multimarcas</option>
                </select>
              </div>
              <span class="field-error" id="err-e-categoria"></span>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="e-telcom">Telefone Comercial *</label>
                <div class="input-wrap"><i class="fas fa-phone"></i>
                  <input type="tel" id="e-telcom" name="telefone_comercial" placeholder="(51) 3333-3333" required maxlength="15">
                </div>
                <span class="field-error" id="err-e-telcom"></span>
              </div>
              <div class="form-field">
                <label for="e-whats">WhatsApp</label>
                <div class="input-wrap"><i class="fab fa-whatsapp"></i>
                  <input type="tel" id="e-whats" name="whatsapp" placeholder="(51) 9 9999-9999" maxlength="16">
                </div>
              </div>
            </div>
            <div class="form-field">
              <label for="e-desc">Descrição</label>
              <div class="input-wrap textarea-wrap"><i class="fas fa-align-left"></i>
                <textarea id="e-desc" name="descricao" placeholder="Fale sobre sua empresa..." rows="3" maxlength="500"></textarea>
              </div>
            </div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="irEscolha()"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="button" class="btn-primary" onclick="nextStep('e',1)">Próximo <i class="fas fa-arrow-right"></i></button>
            </div>
          </div>
 
          <!-- E-Step 2 -->
          <div class="form-step" id="es2">
            <h2 class="step-title"><i class="fas fa-user-tie"></i> Responsável Legal</h2>
            <div class="form-row">
              <div class="form-field">
                <label for="e-resp-nome">Nome *</label>
                <div class="input-wrap"><i class="fas fa-user"></i>
                  <input type="text" id="e-resp-nome" name="responsavel_nome" placeholder="Nome" required maxlength="120">
                </div>
                <span class="field-error" id="err-e-resp-nome"></span>
              </div>
              <div class="form-field">
                <label for="e-resp-sob">Sobrenome *</label>
                <div class="input-wrap"><i class="fas fa-user"></i>
                  <input type="text" id="e-resp-sob" name="responsavel_sobrenome" placeholder="Sobrenome" required maxlength="120">
                </div>
                <span class="field-error" id="err-e-resp-sob"></span>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="e-resp-cpf">CPF Responsável *</label>
                <div class="input-wrap"><i class="fas fa-id-card"></i>
                  <input type="text" id="e-resp-cpf" name="responsavel_cpf" placeholder="000.000.000-00" required maxlength="14">
                </div>
                <span class="field-error" id="err-e-resp-cpf"></span>
              </div>
              <div class="form-field">
                <label for="e-resp-cargo">Cargo</label>
                <div class="input-wrap"><i class="fas fa-briefcase"></i>
                  <input type="text" id="e-resp-cargo" name="responsavel_cargo" placeholder="Sócio, Diretor..." maxlength="80">
                </div>
              </div>
            </div>
            <div class="form-field">
              <label for="e-resp-tel">Telefone Responsável *</label>
              <div class="input-wrap"><i class="fas fa-phone"></i>
                <input type="tel" id="e-resp-tel" name="responsavel_telefone" placeholder="(51) 9 9999-9999" required maxlength="16">
              </div>
              <span class="field-error" id="err-e-resp-tel"></span>
            </div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="prevStep('e',2)"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="button" class="btn-primary" onclick="nextStep('e',2)">Próximo <i class="fas fa-arrow-right"></i></button>
            </div>
          </div>
 
          <!-- E-Step 3 -->
          <div class="form-step" id="es3">
            <h2 class="step-title"><i class="fas fa-lock"></i> Dados de Acesso</h2>
            <div class="form-field">
              <label for="e-email">E-mail Comercial *</label>
              <div class="input-wrap"><i class="fas fa-envelope"></i>
                <input type="email" id="e-email" name="email_comercial" placeholder="contato@empresa.com.br" required>
              </div>
              <span class="field-error" id="err-e-email"></span>
            </div>
            <div class="form-field">
              <label for="e-senha">Senha *</label>
              <div class="input-wrap"><i class="fas fa-lock"></i>
                <input type="password" id="e-senha" name="senha" placeholder="Mínimo 8 caracteres" required minlength="8">
                <button type="button" class="toggle-pass"><i class="fas fa-eye"></i></button>
              </div>
              <div class="pass-strength">
                <div class="strength-bar"><div class="strength-fill" id="e-sfill"></div></div>
                <span id="e-slabel"></span>
              </div>
              <span class="field-error" id="err-e-senha"></span>
            </div>
            <div class="form-field">
              <label for="e-conf">Confirmar Senha *</label>
              <div class="input-wrap"><i class="fas fa-lock"></i>
                <input type="password" id="e-conf" name="confirmar_senha" placeholder="Repita a senha" required>
                <button type="button" class="toggle-pass"><i class="fas fa-eye"></i></button>
              </div>
              <span class="field-error" id="err-e-conf"></span>
            </div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="prevStep('e',3)"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="button" class="btn-primary" onclick="nextStep('e',3)">Próximo <i class="fas fa-arrow-right"></i></button>
            </div>
          </div>
 
          <!-- E-Step 4 -->
          <div class="form-step" id="es4">
            <h2 class="step-title"><i class="fas fa-map-marker-alt"></i> Endereço Comercial</h2>
            <div class="form-row">
              <div class="form-field" style="flex:0 0 150px">
                <label for="e-cep">CEP *</label>
                <div class="input-wrap"><i class="fas fa-search"></i>
                  <input type="text" id="e-cep" name="cep" placeholder="00000-000" required maxlength="9">
                </div>
                <span class="field-error" id="err-e-cep"></span>
              </div>
              <div class="form-field">
                <label for="e-logradouro">Logradouro *</label>
                <div class="input-wrap"><i class="fas fa-road"></i>
                  <input type="text" id="e-logradouro" name="logradouro" placeholder="Rua, Avenida..." required>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field" style="flex:0 0 115px">
                <label for="e-numero">Número *</label>
                <div class="input-wrap"><i class="fas fa-hashtag"></i>
                  <input type="text" id="e-numero" name="numero" placeholder="123" required>
                </div>
                <span class="field-error" id="err-e-numero"></span>
              </div>
              <div class="form-field">
                <label for="e-comp">Complemento</label>
                <div class="input-wrap"><i class="fas fa-door-open"></i>
                  <input type="text" id="e-comp" name="complemento" placeholder="Sala, Bloco...">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="e-bairro">Bairro *</label>
                <div class="input-wrap"><i class="fas fa-map"></i>
                  <input type="text" id="e-bairro" name="bairro" placeholder="Bairro" required>
                </div>
              </div>
              <div class="form-field">
                <label for="e-cidade">Cidade *</label>
                <div class="input-wrap"><i class="fas fa-city"></i>
                  <input type="text" id="e-cidade" name="cidade" placeholder="Cidade" required>
                </div>
              </div>
              <div class="form-field" style="flex:0 0 86px">
                <label for="e-estado">UF *</label>
                <div class="input-wrap"><i class="fas fa-flag"></i>
                  <input type="text" id="e-estado" name="estado" placeholder="RS" required maxlength="2">
                </div>
              </div>
            </div>
            <label class="terms-check">
              <input type="checkbox" id="e-terms" name="aceitar_termos" required>
              <span>Li e aceito os <a href="#" target="_blank">Termos de Uso</a>, <a href="#" target="_blank">Privacidade</a> e <a href="#" target="_blank">Condições para Fornecedores</a></span>
            </label>
            <span class="field-error" id="err-e-terms"></span>
            <div class="alert alert-error" id="alertEmpresa" style="display:none"></div>
            <div class="step-actions">
              <button type="button" class="btn-outline" onclick="prevStep('e',4)"><i class="fas fa-arrow-left"></i> Voltar</button>
              <button type="submit" class="btn-primary btn-submit" id="btnSubmitEmpresa">
                <span>Criar Conta Empresarial</span><i class="fas fa-check"></i><div class="spinner"></div>
              </button>
            </div>
          </div>
        </form>
      </div>
 
      <!-- ══ TELA SUCESSO ══ -->
      <div id="telaSucesso" class="tela">
        <div class="sucesso-wrap">
          <div class="sucesso-icon"><i class="fas fa-check"></i></div>
          <h2>Conta criada com sucesso!</h2>
          <p id="sucessoMsg">Seu cadastro foi realizado. Faça login para continuar.</p>
          <a href="../login/indexLogin.php" class="btn-primary" style="margin-top:8px;display:inline-flex;gap:8px">
            <i class="fas fa-sign-in-alt"></i> Ir para o Login
          </a>
        </div>
      </div>
 
    </div>
  </main>
 
  <script src="scriptCadastro.js"></script>
</body>
</html>