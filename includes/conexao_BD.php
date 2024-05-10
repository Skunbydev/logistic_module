<?php
function DBConnectMy()
{
  $link = mysqli_connect('localhost', 'root', '');
  mysqli_select_db($link, 'logistic_module');

  if (!$link) {
    echo 'Conexão falhou: ' . mysqli_connect_error();
    return false;
  } else {
    return $link;
  }
}
function DBClose($Conexao)
{
  @mysqli_close($Conexao);
}

?>