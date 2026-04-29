<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PeçaAQ — Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="styleLogin.css"/>
</head>
<body>

  <!-- ── LADO ESQUERDO: imagem + marca ── -->
  <div class="login-visual" aria-hidden="true">
    <div class="login-visual__overlay"></div>
    <div class="login-visual__content">
      <a href="../LandingPage/indexLandingPage.php" class="visual-logo">
        <img src="../includes/logo1.png" alt="PeçaAQ">
        <span>PEÇA<em>AQ</em></span>
      </a>
      <blockquote class="visual-quote">
        "A peça certa, no lugar certo,<br>no tempo certo."
      </blockquote>
    </div>
  </div>

  <!-- ── LADO DIREITO: formulário ── -->
  <main class="login-panel">

    <a href="../LandingPage/indexLandingPage.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> Voltar ao site
    </a>

    <div class="login-box">

      <!-- Logo mobile (só aparece em telas pequenas) -->
      <div class="login-box__logo">
        <img src="imgLogin/LogoPecaAq4.png" alt="PeçaAQ">
        <span>PEÇA<em>AQ</em></span>
      </div>

      <h1 class="login-box__title">Bem-vindo de volta</h1>
      <p class="login-box__sub">Acesse sua conta para continuar</p>

      <!-- Mensagem de erro/sucesso vinda do PHP -->
      <?php if (!empty($_GET['erro'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($_GET['erro']); ?>
        </div>
      <?php endif; ?>

      <!-- Tabs -->
      <div class="tab-selector" role="tablist">
        <button class="tab active" data-tab="cliente" role="tab" aria-selected="true">
          <i class="fas fa-user"></i> Sou Cliente
        </button>
        <button class="tab" data-tab="empresa" role="tab" aria-selected="false">
          <i class="fas fa-building"></i> Sou Empresa
        </button>
      </div>

      <!-- ── FORM CLIENTE ── -->
      <form id="form-cliente" class="login-form active" method="POST" action="login.php" novalidate>
        <input type="hidden" name="tipo" value="Cliente">

        <div class="form-field">
          <label for="email-cliente">E-mail</label>
          <div class="input-wrap">
            <i class="fas fa-envelope"></i>
            <input type="email" id="email-cliente" name="login"
                   placeholder="seu@email.com" required autocomplete="email">
          </div>
          <span class="field-error" id="err-email-cliente"></span>
        </div>

        <div class="form-field">
          <label for="senha-cliente">Senha</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" id="senha-cliente" name="senha"
                   placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="toggle-pass" tabindex="-1" aria-label="Mostrar senha">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <span class="field-error" id="err-senha-cliente"></span>
        </div>

        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="lembrar" id="lembrar-cliente">
            <span>Lembrar-me</span>
          </label>
          <a href="recuperar_senha.php" class="forgot-link">Esqueci a senha</a>
        </div>

        <button type="submit" class="btn-login" id="btn-cliente">
          <span>Entrar</span>
          <i class="fas fa-arrow-right"></i>
        </button>

        <p class="hint">
          Não tem uma conta?
          <a href="../Cadastrar/indexCadastro.php" class="link-cadastro">Cadastre-se grátis</a>
        </p>
      </form>

      <!-- ── FORM EMPRESA ── -->
      <form id="form-empresa" class="login-form" method="POST" action="login.php" novalidate
            style="display:none">
        <input type="hidden" name="tipo" value="Empresa">

        <div class="form-field">
          <label for="cnpj-empresa">CNPJ</label>
          <div class="input-wrap">
            <i class="fas fa-id-card"></i>
            <input type="text" id="cnpj-empresa" name="login"
                   placeholder="00.000.000/0000-00" required
                   autocomplete="organization" maxlength="18">
          </div>
          <span class="field-error" id="err-cnpj"></span>
        </div>

        <div class="form-field">
          <label for="senha-empresa">Senha</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" id="senha-empresa" name="senha"
                   placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="toggle-pass" tabindex="-1" aria-label="Mostrar senha">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <span class="field-error" id="err-senha-empresa"></span>
        </div>

        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="lembrar" id="lembrar-empresa">
            <span>Lembrar-me</span>
          </label>
          <a href="recuperar_senha.php" class="forgot-link">Esqueci a senha</a>
        </div>

        <button type="submit" class="btn-login" id="btn-empresa">
          <span>Entrar</span>
          <i class="fas fa-arrow-right"></i>
        </button>

        <p class="hint">
          Não tem uma conta?
          <a href="../Cadastrar/indexCadastro.php" class="link-cadastro">Cadastre-se grátis</a>
        </p>
      </form>

    </div><!-- /login-box -->
  </main>

  <script src="scriptLogin.js"></script>
</body>
</html>