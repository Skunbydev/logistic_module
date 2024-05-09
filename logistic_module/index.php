<?php
include './includes/conexao_BD.php';
$ConexaoMy = DBConnectMy();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

</head>

<body>
  <?php include './includes/bootstrap.php'; ?>
  <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="navbar-Index">
    <div class="container px-5">
      <a href="#page-top" class="navbar-brand fw-bold">Prod</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="bi-list"></i>
      </button>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto me-4 my-3 my-lg-0">
          <li class="nav-item">
            <a class="nav-link me-lg-3" href="#Novidades">Novidades</a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-lg-3" href="#Sobre">Sobre</a>
          </li>
        </ul>
        <button class="btn btn-primary rounded-pill px-3 mb-2 mb-lg-0" data-bs-toggle="modal" data-bs-target="#modalLogin">
          <span><i class="bi bi-door-open-fill"></i></span>
          <span class="ms-2">Login</span>
        </button>
      </div>
    </div>
  </nav>

  <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLogin" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h1 class="modal-title fs-4" id="tituloModalLogin">Login</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row align-items-center">
              <div class="col-md-6 text-center">
                <img src="./includes/img/logistic_image.svg" alt="Imagem de login" class="img-fluid shadow">
              </div>
              <div class="col-md-6">
                <form>
                  <div class="mb-3">
                    <label for="email_cliente" class="form-label">Endereço de email</label>
                    <input type="email" class="form-control" id="email_cliente" aria-describedby="emailHelp" placeholder="email@email.com" name="email_cliente">
                    <div id="emailHelp" class="form-text">Nunca compartilharemos seu email com ninguém.</div>
                  </div>
                  <div class="mb-3">
                    <label for="senha_cliente" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha_cliente" name="senha_cliente" placeholder="******">
                  </div>
                  <button type="submit" class="btn btn-primary" onclick="entrarIndex()">Entrar</button>
                  <button type="button" class="btn btn-secondary" data-bs-target="#modalRecuperarSenha" data-bs-toggle="modal">Recuperar senha</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalRecuperarSenha" aria-hidden="true" aria-labelledby="modalRecuperarSenha" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="tituloRecuperarSenha">Recuperar Senha</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label for="email_recuperacao" class="form-label">Endereço de email</label>
          <input type="email" class="form-control" id="email_recuperacao" aria-describedby="emailHelp" placeholder="email@email.com" name="email_recuperacao">
          <div class="text-center">
            <div id="emailHelp" class="form-text">Se não lembrar do email, contate nosso suporte: (81)99346-6199.</div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary" data-bs-target="#modalLogin" data-bs-toggle="modal">Voltar</button>
          <button class="btn btn-primary" onclick="enviarEmail()">Enviar</button>
        </div>
      </div>
    </div>
    <?php include './includes/scripts/script.php'; ?>
  </div>

  <script>

    function entrarIndex() {
      console.log('oi');
    }
    function enviarEmail(email) {
      if ($("#email_recuperacao").val() == "" || $("#email_recuperacao").val() == null) {
        alert("Informe o email!");
        $("#email_recuperacao").focus();
        return false;
      }
    }
  </script>

</body>

</html>