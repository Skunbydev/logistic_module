<?php
session_name("login_cliente");
session_start();

if ($_SESSION["login_cliente_auth"] = "1") {
  include './includes/conexao_BD.php';
  $ConexaoMy = DBConnectMy();
} else {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: ../index.php');
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HOME</title>
</head>

<body>
  <h1>oi</h1>
</body>

</html>