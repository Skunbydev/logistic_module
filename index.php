<?php
session_name("login_cliente");
@session_start();
include './includes/conexao_BD.php';
$ConexaoMy = DBConnectMy();

if (isset($_POST["metodo"]) == "Logar") {
  $email_cliente = $_POST['email_cliente'];
  $senha_cliente = $_POST['senha_cliente'];
  $sql = "SELECT *, email_cliente, senha_cliente FROM clientes";
  $result = mysqli_query($ConexaoMy, $sql);

  if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
      if ($email_cliente == $row['email_cliente'] && $senha_cliente == $row['senha_cliente']) {
        $arRetorno[0] = 1;
        $arRetorno[1] = "Login bem sucedido!";
        $_SESSION["login_cliente_auth"] = "1";
      } else {
        $arRetorno[0] = "0";
        $_SESSION["login_cliente_auth"] = "0";
        $arRetorno[1] = "Login falhou. Email ou senha incorretos.";


      }
    } else {
      $arRetorno[0] = "0";
      $arRetorno[1] = "Usuário não encontrado";
    }
  } else {
    $arRetorno[0] = "0";
    $arRetorno[1] = "Erro na consulta SQL: " . mysqli_error($ConexaoMy);
  }
  // Retorna os dados JSON para o JavaScript
  die(json_encode($arRetorno));
}
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
  <?php include './includes/scripts/script.php'; ?>
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
                <div class="mb-3">
                  <label for="email_cliente" class="form-label">Endereço de email</label>
                  <input type="email" class="form-control" id="email_cliente" aria-describedby="emailHelp" placeholder="email@email.com" name="email_cliente">

                  <div id="emailHelp" class="form-text">Nunca compartilharemos seu email com ninguém.</div>
                </div>
                <div class="mb-3">
                  <label for="senha_cliente" class="form-label">Senha</label>
                  <input type="password" class="form-control" id="senha_cliente" name="senha_cliente" placeholder="******">
                </div>
                <button type="submit" class="btn btn-primary" onclick="Logar()">Entrar</button>
                <button type="button" class="btn btn-secondary" data-bs-target="#modalRecuperarSenha" data-bs-toggle="modal">Recuperar senha</button>
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
  </div>

  <div class="conteudo-primario-login">
    <div class="container px-5">
      <div class="row gx-5 align-items-center">
        <div class="col-lg-6">
          <div class="mb-5 mb-lg-0 text-center text-lg-start">
            <h1 class="display-1 1h-1 mb-3 text-font-lg">A Líder em Logística</h1>
            <p class="lead fw-normal text-muted mb-5 text-font-md">Desde sua fundação em 2003, a EvilCorp tem sido uma força pioneira no setor de logística. Ao longo dos anos, consolidamos nossa posição como líder de mercado, oferecendo soluções abrangentes e eficientes para uma variedade de necessidades logísticas.</p>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="conteudo-photo">
            <div class="imagem-wrapper">
              <img src="./includes/img/logo_tela_inicio.svg" alt="Logo Tela Início">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <aside class="text-center bg-gradient-primary-to-secondary" style="padding-top: 5rem; padding-bottom: 5rem;">
    <div class="container px-5">
      <div class="row gx-5 justify-content-center">
        <div class="col-xl-8">
          <blockquote class="h2 fs-1 text-white mb-4">
            "Simplesmente incrível! Utilizei os serviços da EvilCorp para enviar um pacote internacionalmente e fiquei extremamente impressionada com a eficiência e profissionalismo da equipe."
          </blockquote>
          <img src="./includes/img/evil_corp_logo.png" alt="logo da empresa" style="height: 7rem">
        </div>
      </div>
    </div>
  </aside>
  <section id="servicos" style="padding-top: 5rem; padding-bottom: 5rem; background-color: #f9f9f9;">
    <div class="container px-5">
      <div class="row gx-5 align-items-center">
        <div class="col-lg-8 order-lg-1 mb-5 mb-lg-0">
          <div class="container-fluid px-5">
            <div class="row gx-5">
              <div class="col-md-6 mb-5">
                <div class="text-center p-4 rounded bg-white shadow-sm">
                  <i class="bi bi-amazon text-primary fs-2 mb-3"></i>
                  <h3 class="font-alt mb-3">Parceria Estratégica com a Amazon</h3>
                  <p class="text-muted">Trabalhamos em estreita colaboração com a Amazon para garantir que nossos clientes desfrutem de acesso privilegiado aos melhores serviços e recursos disponíveis.</p>
                </div>
              </div>
              <div class="col-md-6 mb-5">
                <div class="text-center p-4 rounded bg-white shadow-sm">
                  <i class="bi bi-amazon text-primary fs-2 mb-3"></i>
                  <h3 class="font-alt mb-3">Amplie seus Horizontes com a EvilCorp</h3>
                  <p class="text-muted">Através da nossa parceria com a Amazon, oferecemos soluções logísticas integradas para atender às suas necessidades mais complexas, proporcionando tranquilidade e eficiência em cada etapa do processo.</p>
                </div>
              </div>
              <div class="col-md-6 mb-5">
                <div class="text-center p-4 rounded bg-white shadow-sm">
                  <i class="bi bi-amazon text-primary fs-2 mb-3"></i>
                  <h3 class="font-alt mb-3">Experiência Logística de Primeira Classe</h3>
                  <p class="text-muted">Nosso compromisso com a excelência logística é reforçado pela nossa colaboração com a Amazon, permitindo-nos oferecer serviços de primeira classe que superam as expectativas dos nossos clientes.</p>
                </div>
              </div>
              <div class="col-md-6 mb-5">
                <div class="text-center p-4 rounded bg-white shadow-sm">
                  <i class="bi bi-amazon text-primary fs-2 mb-3"></i>
                  <h3 class="font-alt mb-3">Inovação e Eficiência em Logística</h3>
                  <p class="text-muted">Como parceiros da Amazon, estamos constantemente inovando e aprimorando nossos serviços para oferecer soluções logísticas de ponta que impulsionam o crescimento e o sucesso dos nossos clientes.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 order-lg-0">
          <div class="text-center">
            <img src="./includes/img/feature_logo_principal.svg" alt="Logo EvilCorp" width="250">
          </div>
        </div>
      </div>
    </div>
  </section>





  <?php include './includes/scripts/script.php'; ?>
  <script>
    function Logar() {
      if ($("#email_cliente").val() == "" || $("#email_cliente").val() == null) {
        alert("Informe o email, por gentileza.");
        $("#email_cliente").focus();
        return false;
      }
      if ($("#senha_cliente").val() == "" || $("#senha_cliente").val() == null) {
        alert("Informe a senha, por gentileza.");
        $("#senha_cliente").focus();
        return false;
      }
      var parametros = new FormData();
      parametros.append("metodo", "Logar");
      parametros.append("email_cliente", $("#email_cliente").val());
      parametros.append("senha_cliente", $("#senha_cliente").val());

      $.ajax({
        type: "POST",
        url: '<?php echo $_SERVER['PHP_SELF']; ?>',
        data: parametros,
        contentType: false,
        processData: false,
        beforeSend: function () {
          $('#div_loading_modal_login').show();
        },
        success: function (retorno) {
          $('#div_loading_modal_login').hide();
          try {
            var arRetorno = JSON.parse(retorno);
            alert(arRetorno[1]);
            if (arRetorno[0] == 1) {
              window.location = ('home.php');
            } else {
              console.log('Falha no login');
            }
          } catch (erro) {
            alert('deu erro');
            console.log(erro);
            console.log(retorno);
          }
        }
      });

      return false; // Impede o envio padrão do formulário
    }

  </script>

</body>

</html>
<?php DBClose($ConexaoMy); ?>