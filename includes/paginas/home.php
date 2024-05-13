<?php session_name("login_cliente");
session_start();

if ($_SESSION["login_cliente_auth"] != "1") {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: logout.php');
  exit;
}
include '../conexao_BD.php';
$ConexaoMy = DBConnectMy();




?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HOME</title>
</head>

<body>
  <?php include '../bootstrap.php'; ?>
  <?php include '../scripts/script.php'; ?>
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <a href="/" class="d-flex align-items-center m-2 m-lg-0 text-dark text-decoration-none">
          <h1 class="h4 mb-0">PROD</h1>
        </a>

        <ul class="nav col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="" class="nav-link px-2 link-secondary">item 1</a></li>
          <li><a href="" class="nav-link px-2 link-secondary">item 2</a></li>
          <li><a href="" class="nav-link px-2 link-secondary">item 3</a></li>
        </ul>
      </div>
      <div class="dropdown">
        <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://avatars.githubusercontent.com/u/122830909?v=4" alt="mdo" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu text-small dropdown-menu-right" aria-labelledby="dropdownUser1">
          <li><a class="dropdown-item" href="#">Novo pedido...</a></li>
          <li><a class="dropdown-item" href="#">Configurações</a></li>
          <li><a class="dropdown-item" href="#">Meu Perfil</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="#" onclick="deslogar();">Deslogar</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <?php include './layout.php'; ?>



</body>

</html>