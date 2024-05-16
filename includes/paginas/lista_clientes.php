<?php
session_name("login_cliente");
session_start();

if ($_SESSION["login_cliente_auth"] != "1") {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: logout.php');
  exit;
}
include '../conexao_BD.php';
$ConexaoMy = DBConnectMy();

if (isset($_POST["metodo"]) && $_POST["metodo"] == "InativarCliente") {
  if ((int) $_POST["codigo"] > 0) {
    $SQL = "UPDATE lista_clientes SET situacao = '0' WHERE id_cliente_lista = '" . $_POST["codigo"] . "' ";
    $rsAux = mysqli_query($ConexaoMy, $SQL);
  }
  if ($rsAux) {
    $arRetorno[0] = "1";
    $arRetorno[2] = $SQL;
    $arRetorno[1] = "Cliente inativo com sucesso!";
  } else {
    $arRetorno[0] = "0";
    $arRetorno[2] = $SQL;
    $arRetorno[1] = "Não foi possível inativar o Cliente!";
  }
  die(json_encode($arRetorno));
}

if (isset($_POST["metodo"]) && $_POST["metodo"] == "Carregar") {
  $arrayRetornoGeral = array();
  $SQL = "SELECT * FROM lista_clientes WHERE id_cliente_lista = '" . $_POST["codigo"] . "'";

  $rsDadosLista = mysqli_query($ConexaoMy, $SQL);
  $arDadosLista = mysqli_fetch_assoc($rsDadosLista);

  $arrayRetornoGeral = $arDadosLista;
  die(json_encode($arrayRetornoGeral));
}

if (isset($_POST["metodo"]) && $_POST["metodo"] == "Salvar") {
  $id_cliente_lista = $_POST["id_cliente_lista"];
  $nome_cliente_lista = $_POST["nome_cliente_lista"];
  $email_cliente_lista = $_POST["email_cliente_lista"];
  $telefone_cliente_lista = $_POST["telefone_cliente_lista"];
  $endereco_cliente_lista = $_POST["endereco_cliente_lista"];

  if ($id_cliente_lista != "") {
    $SQL = "UPDATE lista_clientes SET 
    nome_cliente_lista = '$nome_cliente_lista', 
    email_cliente_lista = '$email_cliente_lista', 
    telefone_cliente_lista = '$telefone_cliente_lista', 
    endereco_cliente_lista = '$endereco_cliente_lista' 
    WHERE id_cliente_lista = '$id_cliente_lista'";


    $rsAux = mysqli_query($ConexaoMy, $SQL);

    if ($rsAux) {
      $arRetorno[0] = "1";
      $arRetorno[1] = "Cliente editado com sucesso";
      $arRetorno[2] = $SQL;
      DBClose($ConexaoMy);
      die(json_encode($arRetorno));
    } else if (!$rsAux) {
      $arRetorno[0] = "0";
      $arRetorno[1] = "Cliente não foi editado, contate a skunby Tecnologia (1111)";
      $arRetorno[2] = $SQL;
      DBClose($ConexaoMy);
      die(json_encode($arRetorno));
    }

  }
  $SQL = "INSERT INTO lista_clientes (nome_cliente_lista, email_cliente_lista, telefone_cliente_lista, endereco_cliente_lista, situacao) VALUES ('$nome_cliente_lista', '$email_cliente_lista', '$telefone_cliente_lista', '$endereco_cliente_lista', 1)";

  $rsAux = mysqli_query($ConexaoMy, $SQL);
  if ($rsAux) {
    $arRetorno[0] = "1";
    $arRetorno[1] = "Cliente cadastrado com sucesso";
    $arRetorno[2] = $SQL;
    DBClose($ConexaoMy);
    die(json_encode($arRetorno));
  } else if (!$rsAux) {
    $arRetorno[0] = "0";
    $arRetorno[1] = "Cliente não foi cadastrado, contate a skunby Tecnologia (1111)";
    $arRetorno[2] = $SQL;
    DBClose($ConexaoMy);
    die(json_encode($arRetorno));
  }

}

if (isset($_GET['metodo']) && trim($_GET['metodo']) == "Consultar") {
  require ('../plugins/datatable_server_side/scripts/ssp.class.php');
  $SSP = new SSP();
  $columns = array(
    array('db' => 'lsc.id_cliente_lista', 'dt' => 0),
    array('db' => 'lsc.nome_cliente_lista', 'dt' => 1),
    array('db' => 'lsc.email_cliente_lista', 'dt' => 2),
    array('db' => 'lsc.telefone_cliente_lista', 'dt' => 3),
    array('db' => 'lsc.endereco_cliente_lista', 'dt' => 4),
  );
  $bindings = array();
  $limit = @$SSP->limit($_GET, $columns);
  $order = @$SSP->order($_GET, $columns);
  $where = @$SSP->filter($_GET, $columns, $bindings);

  $dados = array();
  $resTotalLength = 0;
  $recordsFiltered = 0;

  $SQL = "SELECT lsc.id_cliente_lista, lsc.nome_cliente_lista, lsc.email_cliente_lista, lsc.telefone_cliente_lista, lsc.endereco_cliente_lista
  FROM lista_clientes lsc
  WHERE lsc.situacao = 1";

  $filtro = json_decode($_GET["filtro"]);

  if ((string) $filtro->nome_cliente_lista_filtro != "") {
    $SQL .= " AND  lsc.nome_cliente_lista IN ($filtro->nome_cliente_lista_filtro)  ";
  }
  if ((string) $filtro->email_cliente_lista_filtro != "") {
    $SQL .= " AND  lsc.email_cliente_lista_filtro IN ($filtro->email_cliente_lista_filtro)  ";
  }
  if ((string) $filtro->telefone_cliente_lista_filtro != "") {
    $SQL .= " AND  lsc.telefone_cliente_lista IN ($filtro->telefone_cliente_lista_filtro)  ";
  }
  if ((string) $filtro->endereco_cliente_lista_filtro != "") {
    $SQL .= " AND  lsc.endereco_cliente_lista IN ($filtro->endereco_cliente_lista_filtro)  ";
  }
  $i = 0;
  $Query = mysqli_query($ConexaoMy, utf8_decode($SQL));
  while ($Aux = mysqli_fetch_assoc($Query)) {
    $link_editar = "<a data-bs-toggle='tooltip' title='Editar Cliente'  onclick='Carregar(" . $Aux["id_cliente_lista"] . ", 1);' style='cursor:pointer; color:green;'>
    <i class='bi bi-pencil'></i>
    </a>";

    $link_detalhe = "<a data-bs-toggle='tooltip' title='Detalhes do Cliente' onclick='Carregar(" . $Aux["id_cliente_lista"] . ", 0);' style='cursor:pointer; color:blue;'>
    <i class='bi bi-search'></i>
    </a>";

    $link_inativar = "<a data-bs-toggle='tooltip' title='Inativar Cliente' onclick='InativarCliente(" . $Aux["id_cliente_lista"] . ")' style='cursor:pointer; color:red;'>
    <i class='bi bi-x'></i>
    </a>";

    $dados[$i][] = $link_editar;
    $dados[$i][] = $link_detalhe;
    $dados[$i][] = $link_inativar;
    $dados[$i][] = $Aux["id_cliente_lista"];
    $dados[$i][] = $Aux["nome_cliente_lista"];
    $dados[$i][] = $Aux["email_cliente_lista"];
    $dados[$i][] = $Aux["telefone_cliente_lista"];
    $dados[$i][] = $Aux["endereco_cliente_lista"];
    $i++;
  }
  $recordsTotal = $i;
  $Arr = array(
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
    "recordsTotal" => intval($recordsTotal),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $dados,
    "linhas" => $i
  );

  echo json_encode($Arr);
  die();
}



?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista clientes</title>
</head>

<body>
  <?php include 'layout.php'; ?>
  <div class="flex-grow-1 p-3">
    <div class="row">
      <section class="content-header">
        <h2>Lista de clientes <small class="fs-4" style="color: gray">gerencie seus clientes</small></h2>
      </section>
      <section class="content mt-4">
        <div id="div_consulta" class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h4 class="card-title text-white mb-0">
                  Lista de clientes
                  <a class="hint--right" data-hint="Pesquisar Avançada" style="cursor:pointer;" onclick="PesquisaAvancada()">
                    <span class="glyphicon glyphicon-search"></span>
                  </a>
                </h4>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="bi bi-file-minus-fill text-white"></i></button>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-9">
                    <div class="btn-group gap-2">
                      <input type="hidden" id="hid_id_cliente_lista">
                      <button type="button" class="btn btn-primary" onclick="Novo()" id="btn_salvar">
                        <i class="bi bi-plus-square"></i> Novo Cliente
                      </button>
                      <button type="button" class="btn btn-primary" onclick="PesquisaAvancada()"><i class="bi bi-funnel-fill"></i> FILTRO</button>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <div class="input-group">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table id="tabela_consulta" class="table table-striped table-hover" style="font-size:12px; min-width: auto">
                <thead>
                  <tr class="bg-light-blue color-palette">
                    <th style="width: 1%"></th>
                    <th style="width: 1%"></th>
                    <th style="width: 1%"></th>
                    <th style="width:11%; text-align:center; vertical-align:middle;">Código</th>
                    <th style="width:11%; text-align:center; vertical-align:middle; min-width: 200px">Nome</th>
                    <th style="width:11%; text-align:center; vertical-align:middle; min-width: 200px">Email cliente</th>
                    <th style="width:11%; text-align:center; vertical-align:middle;">Telefone</th>
                    <th style="width:11%; text-align:center; vertical-align:middle; min-width: 200px">Endereco</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

      </section>
      <div class="modal fade" id="novoClienteListaModal" tabindex="-1" aria-labelledby="novoClienteListaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content bg-dark">
            <div class="modal-header bg-primary">
              <h5 class="modal-title" id="titulo_modal_novo_lista">Adicionar Novo Cliente</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white text-black">
              <form>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nome_cliente_lista" class="form-label">Nome <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="nome_cliente_lista">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email_cliente_lista" class="form-label">Email <span style="color: red">*</span></label>
                    <input type="email" class="form-control" id="email_cliente_lista">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="telefone_cliente_lista" class="form-label">Telefone <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="telefone_cliente_lista">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="endereco_cliente_lista" class="form-label">Endereco <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="endereco_cliente_lista">
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer bg-primary">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="">Fechar</button>
              <button type="button" class="btn btn-info" id="btn_cadastrar" onclick="Salvar()">Cadastrar</button>
            </div>
          </div>
        </div>
      </div>

      <script>
        var GLTabela = null;
        var GLFiltro = [];

        GLFiltro = {
          nome_cliente_lista_filtro: "",
          email_cliente_lista_filtro: "",
          telefone_cliente_lista_filtro: "",
          endereco_cliente_lista_filtro: ""
        }

        GLTabela = $('#tabela_consulta').DataTable({
          "iDisplayLength": 100,
          "searching": false,
          "lengthChange": false,
          "processing": true,
          "serverSide": true,
          "ajax": "<?php echo $_SERVER['PHP_SELF']; ?>?metodo=Consultar&filtro=" + JSON.stringify(GLFiltro),
          "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $($(nRow).find("td")[0]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[1]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[2]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[3]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[4]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[5]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[6]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[7]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[8]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });
            $($(nRow).find("td")[9]).css({
              "text-align": "center",
              "vertical-align": "middle"
            });

          },
          "fnDrawCallback": function () {
            $("#div_load_consulta").hide();
            $("#div_load_filtro").hide();
          },
          "preDrawCallback": function (settings) {
            $("#div_load_consulta").show();
            $("#div_load_filtro").show();
          },
          "initComplete": function (settings, json) {
            $("#div_load_consulta").hide();
            $("#div_load_filtro").hide();
          },
          "aoColumnDefs": [
            // Desabilitando Ordenacao coluna
            {
              'bSortable': false,
              'aTargets': [0, 1, 2]

            },
            // Desabilitando Busca coluna
            {
              "bSearchable": false,
              "aTargets": [0, 1, 2]
            }
          ],
          // Definindo ordenação padrão 3 coluna
          "order": [
            [3, "desc"]
          ],
          "language": {
            "lengthMenu": "Exibindo _MENU_ registros por Página",
            "zeroRecords": "Desculpe - Nenhum registro encontrado",
            "info": "Exibindo página _PAGE_ de _PAGES_ ( Total de _TOTAL_ Registros )",
            "infoEmpty": "",
            "infoFiltered": "(Exibindo _MAX_ total registros)",
            "sSearch": "Pesquisar",
            "oPaginate": {
              "sNext": "",
              "sPrevious": "",
              "sFirst": "",
              "sLast": ""
            },
            "oAria": {
              "sSortAscending": ": Ordenar colunas de forma ascendente",
              "sSortDescending": ": Ordenar colunas de forma descendente"
            }
          }
        });
        function Novo() {
          $("#titulo_modal_novo_lista").html("Novo Cliente");
          $("#novoClienteListaModal").modal("show");
          $("#nome_cliente_lista").val("");
          $("#email_cliente_lista").val("");
          $("#telefone_cliente_lista").val("");
          $("#endereco_cliente_lista").val("");
        }
        function Salvar() {
          $("#novoClienteListaModal").modal("show");

          if ($("#nome_cliente_lista").val() == "" || $("#nome_cliente_lista").val() == null) {
            alert("Informe o nome do Cliente");
            $("#nome_cliente_lista").focus();
            return false;
          }
          if ($("#email_cliente_lista").val() == "" || $("#email_cliente_lista").val() == null) {
            alert("Informe o email do Cliente");
            $("#email_cliente_lista").focus();
            return false;
          }
          if ($("#telefone_cliente_lista").val() == "" || $("#telefone_cliente_lista").val() == null) {
            alert("Informe o telefone do Cliente");
            $("#telefone_cliente_lista").focus();
            return false;
          }
          if ($("#endereco_cliente_lista").val() == "" || $("#endereco_cliente_lista").val() == null) {
            alert("Informe o endereco do Cliente");
            $("#endereco_cliente_lista").focus();
            return false;
          }
          var parametros = new FormData();

          parametros.append("metodo", "Salvar");
          parametros.append("id_cliente_lista", $("#hid_id_cliente_lista").val());
          parametros.append("nome_cliente_lista", $("#nome_cliente_lista").val());
          parametros.append("email_cliente_lista", $("#email_cliente_lista").val());
          parametros.append("telefone_cliente_lista", $("#telefone_cliente_lista").val());
          parametros.append("endereco_cliente_lista", $("#endereco_cliente_lista").val());

          $.ajax({
            type: "POST",
            url: '<?php echo $_SERVER['PHP_SELF'] ?>',
            data: parametros,
            contentType: false,
            processData: false,
            beforeSend: function () {
              $('#div_load_consulta').show();
            },
            success: function (retorno) {
              $('#div_load_consulta').hide();
              try {
                var arRetorno = JSON.parse(retorno);
                alert(arRetorno[1]);
                if (arRetorno[0] == "1") {
                  $("#novoClienteListaModal").hide();
                  console.log('aqui');
                  GLTabela.ajax.url("<?php echo $_SERVER['PHP_SELF']; ?>?metodo=Consultar&filtro=" + JSON.stringify(GLFiltro)).load();
                  window.location.reload();
                } else if (arRetorno[0] === 9999) {
                  console.log("deslogado, safado");
                } else {
                  console.log(arRetorno);
                }
              } catch (erro) {
                console.log(retorno);
                console.log(arRetorno);
                alert("ERRO");
              }
            }
          });
        }
        function InativarCliente(codigo) {
          Swal.fire({
            title: 'Tem certeza que deseja inativar este registro?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            icon: 'question'
          }).then((result) => {
            if (result.isConfirmed) {
              var parametros = new FormData();
              parametros.append("metodo", "InativarCliente");
              parametros.append("codigo", codigo);

              $.ajax({
                type: "POST",
                url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                data: parametros,
                contentType: false,
                processData: false,
                beforeSend: function () {
                  $('#div_load_consulta').show();
                },
                success: function (retorno) {
                  $('#div_load_consulta').hide();
                  try {
                    var arRetorno = JSON.parse(retorno);
                    alert(arRetorno[1]);

                    if (arRetorno[0] == "1") {
                      GLTabela.ajax.url("<?php echo $_SERVER['PHP_SELF']; ?>?metodo=Consultar&filtro=" + JSON.stringify(GLFiltro)).load();
                    } else if (arRetorno[0] == "9999") {
                      window.location = '../includes/logout.php';
                    } else {
                      console.log(retorno);
                      console.log(arRetorno);
                    }
                  } catch (erro) {
                    alert("Não foi possível realizar esta operação! Contate a Skunby Tecnologia (erro 3333).");
                    console.log(retorno);
                    console.log(arRetorno);
                  }
                }
              });
            }
          });
        }
        function Carregar(codigo, flag_disabled) {
          function StringPad(str, pad, length) {
            str = str.toString();
            while (str.length < length) {
              str = pad + str;
            }
            return str;
          }
          // var btn = document.getElementById('btn_cadastrar');
          // if (btn.classList.contains('btn-primary')) {
          //   btn.innerHTML = 'EDITAR';
          //   btn.style.visibility = 'visible';
          // }
          $("#hid_id_cliente_lista").val(codigo);
          var parametros = new FormData();
          parametros.append("metodo", "Carregar");
          parametros.append("codigo", codigo);
          $.ajax({
            type: "POST",
            url: '<?php echo $_SERVER['PHP_SELF']; ?>',
            data: parametros,
            contentType: false,
            processData: false,
            beforeSend: function () {
              $('#div_load_consulta').show();
            },
            success: function (retorno) {
              $('#div_load_consulta').hide();
              try {
                var arRetorno = JSON.parse(retorno);
                $("#nome_cliente_lista").val(arRetorno.nome_cliente_lista);
                $("#email_cliente_lista").val(arRetorno.email_cliente_lista);
                $("#telefone_cliente_lista").val(arRetorno.telefone_cliente_lista);
                $("#endereco_cliente_lista").val(arRetorno.endereco_cliente_lista);

                $("#nome_cliente_lista").prop("disabled", flag_disabled == "1" ? false : true);
                $("#email_cliente_lista").prop("disabled", flag_disabled == "1" ? false : true);
                $("#telefone_cliente_lista").prop("disabled", flag_disabled == "1" ? false : true);
                $("#endereco_cliente_lista").prop("disabled", flag_disabled == "1" ? false : true);
                $("#novoClienteListaModal").modal("show");
                flag_disabled == "1" ? $("#titulo_modal_novo_lista").html("Editação do cliente Cód: " + StringPad(codigo, "0000")) : $("#titulo_modal_novo_lista").html("Detalhe do cliente Cód: " + StringPad(codigo, "0000"));
              } catch (erro) {
                alert("Não foi possível realizar esta operação! Contate a Skunby Tecnologia2222.");
                console.log(retorno);
                console.log(arRetorno);
              }
            }
          });
        }



      </script>
</body>

</html>