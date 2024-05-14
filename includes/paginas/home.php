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
<style>
  .jumbotron {
    background-color: #ffffff;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .jumbotron h1 {
    font-size: 2.5rem;
    color: #007bff;
  }

  .jumbotron p {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 1.5rem;
  }

  .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
  }

  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
  }
</style>

<body>

  <?php include './layout.php'; ?>
  <div class="flex-grow-1 p-3">
    <div class="row">
      <div class="col-md-6">
        <?php
        date_default_timezone_set('America/Recife');
        $horario_atual_sistema = date("H:i:s");
        $hora_tarde_recife = date("H:i:s", strtotime("15:00:00"));
        $hora_noite_recife = date("H:i:s", strtotime("20:00:00"));

        $bom_dia = 'Bom dia comercial';
        $boa_tarde = 'Boa tarde comercial';
        $boa_noite = 'Boa noite comercial';
        if ($horario_atual_sistema < '12:00:00') {
          $tratamento_horario = $bom_dia;
        } else if ($horario_atual_sistema < '18:00:00') {
          $tratamento_horario = $boa_tarde;
        } else {
          $tratamento_horario = $boa_noite;
        }
        echo '<h3 class="text-uppercase mb-0">' . $tratamento_horario . '</h3>';
        ?>
      </div>
      <div class="col-md-6 text-center">
        <?php echo 'Data e horário atual: ' . date("d-m-Y") . ' ' . $horario_atual_sistema ?>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-6 mb-4 mb-md-0">
        <div class="jumbotron">
          <h1 class="display-4">Módulo Pedidos</h1>
          <p class="lead">Gerencie seus pedidos de forma eficiente.</p>
          <hr class="my-4">
          <p>Acompanhe seus pedidos, lançamentos, valores e muito mais.</p>
          <a class="btn btn-light btn-lg" href="#" role="button">Acessar módulo pedidos</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="jumbotron">
          <h1 class="display-4">Módulo de Produtos</h1>
          <p class="lead">Gerencie seu catálogo de produtos com facilidade.</p>
          <hr class="my-4">
          <p>Adicione, edite e remova produtos com apenas alguns cliques.</p>
          <a class="btn btn-light btn-lg" href="./produtos.php" role="button">Acessar módulo de produtos</a>
        </div>
      </div>
    </div>
  </div>
  </div>

</body>

</html>