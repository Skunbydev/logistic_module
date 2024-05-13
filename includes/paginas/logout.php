<?php
session_name("login_cliente");
session_start();
$_SESSION["login_cliente_auth"] = "0";
header('location: ../../index.php');
exit();
?>