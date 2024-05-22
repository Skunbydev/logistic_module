<?php
session_name("login_cliente");
session_start();

if ($_SESSION["login_cliente_auth"] != "1") {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: logout.php');
  exit;
}

?>


<!DOCTYPE html>
<html lang="PT-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Envios</title>
</head>


<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'layout.php' ?>
    <div class="flex-grow-1 p-3">
      <div class="row">
        <section class="content-header">
          <h2>Envios <small class="fs-4" style="color: gray">gerenciamento de Envios</small></h2>
        </section>
      </div>
      <div class="row mt-4">
        <div id="div_consulta" class="col-md-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">
                LISTA DE ENVIOS
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a class='hint--right' data-hint='Pesquisar Avançada' style='cursor:pointer;' onclick="PesquisaAvancada()">
                  <i title='Editar pedido' class="bi bi-search" style="width: 15px"></i>
                </a>
              </h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="bi bi-dash-square"></i></button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="Novo()"><i class="bi bi-plus-square" style="margin-right: 5px"></i>Nova Reserva</button>
                    <button type="button" class="btn btn-primary" onclick="PesquisaAvancada()"><span class="bi bi-funnel-fill"></span>&nbsp;&nbsp;FILTRO</button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="tabela_consulta" class="table table-striped table-hover" style="font-size:12px;">
                      <thead>
                        <tr class="bg-primary color-palette">
                          <th style="width: 1%"></th>
                          <th style="width: 1%"></th>
                          <th style="width: 1%"></th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">CÓDIGO Reserva</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Data Reserva</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Sala de Reunião</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Responsável</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Título Reserva</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Horário De</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Horário Até</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Descrição</th>
                          <th style="width:10%; text-align:center; vertical-align:middle;">Cliente</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>




</body>

</html>