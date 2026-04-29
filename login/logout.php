<?php
// ════════════════════════════════════════════════
//  PeçaAQ — logout.php
//  Destrói a sessão PHP e limpa o localStorage
//  via página intermediária antes de redirecionar.
// ════════════════════════════════════════════════

session_start();

// Destrói sessão PHP completa
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PeçaAQ — Saindo...</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{
      min-height:100vh;display:flex;flex-direction:column;
      align-items:center;justify-content:center;
      background:#0a0a0a;color:#fff;
      font-family:'Barlow',sans-serif;gap:16px;
    }
    .loader{
      width:40px;height:40px;
      border:3px solid rgba(232,25,44,.2);
      border-top-color:#e8192c;
      border-radius:50%;
      animation:spin .6s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg)}}
    p{color:#666;font-size:.85rem}
  </style>
</head>
<body>
  <div class="loader"></div>
  <p>Encerrando sessão...</p>
  <script>
    try {
      localStorage.removeItem('usuarioLogado');
      sessionStorage.removeItem('recem_logado');
    } catch(e) {}
    window.location.href = '../login/indexLogin.php';
  </script>
</body>
</html>