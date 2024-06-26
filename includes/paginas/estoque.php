<?php session_name("login_cliente");
session_start();

if ($_SESSION["login_cliente_auth"] != "1") {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: logout.php');
  exit;
}
include '../conexao_BD.php';
$ConexaoMy = DBConnectMy();

if (isset($_POST["metodo"]) && $_POST["metodo"] == "Carregar") {
  $arrayRetornoGeral = array();
  $SQL = "SELECT * FROM estoque 
					WHERE id_produto = '" . $_POST["codigo"] . "'";

  $rsDadosEstoque = mysqli_query($ConexaoMy, $SQL);
  $arDadosEstoque = mysqli_fetch_assoc($rsDadosEstoque);

  $arrayRetornoGeral = $arDadosEstoque;
  die(json_encode($arrayRetornoGeral));

}

if (isset($_POST["metodo"]) && $_POST["metodo"] == "InativarProduto") {
  if ((int) $_POST["codigo"] > 0) {
    $SQL = "UPDATE estoque SET situacao = '0' WHERE id_produto = '" . $_POST["codigo"] . "' ";
    $rsAux = mysqli_query($ConexaoMy, $SQL);
  }
  if ($rsAux) {
    $arRetorno[0] = "1";
    $arRetorno[1] = "Produto inativo com sucesso!";
  } else {
    $arRetorno[0] = "0";
    $arRetorno[2] = $sql;
    $arRetorno[1] = "Não foi possível inativar o Produto!";
  }
  die(json_encode($arRetorno));
}

if (isset($_GET['metodo']) && trim($_GET['metodo']) == "Consultar") {
  require ('../plugins/datatable_server_side/scripts/ssp.class.php');
  $SSP = new SSP();
  $columns = array(
    array('db' => 'est.id_produto', 'dt' => 0),
    array('db' => 'est.nome_produto', 'dt' => 1),
    array('db' => 'est.descricao_produto', 'dt' => 2),
    array('db' => 'est.valor_produto', 'dt' => 3),
    array('db' => 'est.quantidade_produto', 'dt' => 4),
    array('db' => 'est.codigo_categoria_produto', 'dt' => 5),
  );
  $bindings = array();
  $limit = @$SSP->limit($_GET, $columns);
  $order = @$SSP->order($_GET, $columns);
  $where = @$SSP->filter($_GET, $columns, $bindings);

  $dados = array();
  $resTotalLength = 0;
  $recordsFiltered = 0;

  $SQL = "SELECT est.id_produto, est.nome_produto, est.descricao_produto, est.valor_produto, est.quantidade_produto, est.codigo_categoria_produto, cpr.nome_categoria
  FROM estoque est
  LEFT JOIN categoria_produtos cpr ON cpr.codigo_categoria_produto  = est.codigo_categoria_produto
  WHERE est.situacao = 1";

  $filtro = json_decode($_GET["filtro"]);

  if ((string) $filtro->nome_produto_filtro != "") {
    $SQL .= " AND  est.nome_produto IN ($filtro->nome_produto_filtro)  ";
  }

  if ((string) $filtro->descricao_produto_filtro != "") {
    $SQL .= " AND est.descricao_produto IN ($filtro-> descricao_produto_filtro) ";
  }
  if ((string) $filtro->valor_produto_filtro != "") {
    $SQL .= " AND est.valor_produto IN ($filtro-> valor_produto_filtro) ";
  }
  if ((string) $filtro->quantidade_produto_filtro != "") {
    $SQL .= " AND est.quantidade_produto IN ($filtro-> quantidade_produto_filtro) ";
  }
  if ((string) $filtro->codigo_categoria_produto_filtro != "") {
    $SQL .= " AND est.codigo_categoria_produto IN ($filtro->codigo_categoria_produto_filtro) ";
  }


  $i = 0;
  $Query = mysqli_query($ConexaoMy, utf8_decode($SQL));

  while ($Aux = mysqli_fetch_assoc($Query)) {

    $valor_produto_formatado = 'R$: ' . $Aux["valor_produto"];

    $link_editar = "<a data-bs-toggle='tooltip' title='Editar Produto'  onclick='Carregar(" . $Aux["id_produto"] . ", 1);' style='cursor:pointer; color:green;'>
    <i class='bi bi-pencil'></i>
    </a>";

    $link_detalhe = "<a data-bs-toggle='tooltip' title='Detalhes do Produto'  onclick='Carregar(" . $Aux["id_produto"] . ", 0);' style='cursor:pointer; color:blue;'>
    <i class='bi bi-search'></i>
    </a>";


    $link_inativar = "<a data-bs-toggle='tooltip' title='Inativar Produto' onclick='InativarProduto(" . $Aux["id_produto"] . ")' style='cursor:pointer; color:red;'>
    <i class='bi bi-x'></i>
</a>";


    $dados[$i][] = $link_editar;
    $dados[$i][] = $link_detalhe;
    $dados[$i][] = $link_inativar;
    $dados[$i][] = $Aux["id_produto"];
    $dados[$i][] = $Aux["nome_produto"];
    $dados[$i][] = $Aux["descricao_produto"];
    $dados[$i][] = $valor_produto_formatado;
    $dados[$i][] = $Aux["quantidade_produto"];
    $dados[$i][] = $Aux["nome_categoria"];
    $i++;
  }
  $dados[$i] = array(
    "",
    "",
    "",
    "",
    "",
    "<td style='width: 100%;'>VALOR TOTAL EM ESTOQUE:</td>",
    "<td>R$ </td>",
    "",
    "",
  );
  $total_valor = 0;
  $total_quantidade = 0;

  foreach ($dados as $produto) {
    $valor_produto = str_replace('R$: ', '', $produto[6]);
    $valor_produto = str_replace(',', '.', $valor_produto);

    if (is_numeric($valor_produto)) {
      $valor_produto = floatval($valor_produto);
    } else {
      $valor_produto = 0;
    }

    if (is_numeric($produto[7])) {
      $quantidade_produto = intval($produto[7]);
    } else {
      $quantidade_produto = 0;
    }
    $total_valor += $valor_produto * $quantidade_produto;
    $total_quantidade += $quantidade_produto;
  }

  $dados[$i][6] = 'R$: ' . number_format($total_valor, 2, ',', '.');

  $dados[$i][7] = $total_quantidade;

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


if (isset($_POST["metodo"]) && $_POST["metodo"] == 'Salvar') {
  $id_produto = $_POST["id_produto"];
  $nome_produto = $_POST["nome_produto"];
  $descricao_produto = $_POST["descricao_produto"];
  $valor_produto = $_POST["valor_produto"];
  $quantidade_produto = $_POST["quantidade_produto"];
  $codigo_categoria_produto = $_POST["codigo_categoria_produto"];

  if ($id_produto != "") {
    $SQL = " UPDATE estoque SET 
    nome_produto = '$nome_produto', 
    descricao_produto = '$descricao_produto', 
    valor_produto = '$valor_produto', 
    quantidade_produto = '$quantidade_produto', 
    codigo_categoria_produto = '$codigo_categoria_produto'
    WHERE id_produto = '$id_produto'";

    $rsAux = mysqli_query($ConexaoMy, $SQL);

    if ($rsAux) {
      $arRetorno[0] = "1";
      $arRetorno[1] = "Produto atualizado com sucesso";
      $arRetorno[2] = $SQL;
      DBClose($ConexaoMy);
      die(json_encode($arRetorno));
    } else if (!$rsAux) {
      $arRetorno[0] = "0";
      $arRetorno[1] = "Não foi possível atualizar o produto";
      $arRetorno[2] = $SQL;
    }
  } else {
    $SQL = "INSERT INTO estoque (nome_produto, descricao_produto, valor_produto, quantidade_produto, codigo_categoria_produto, situacao)
  VALUES ('$nome_produto', '$descricao_produto', '$valor_produto', '$quantidade_produto', '$codigo_categoria_produto', 1)";

    $rsAux = mysqli_query($ConexaoMy, $SQL);
    if ($rsAux) {
      $arRetorno[0] = "1";
      $arRetorno[1] = "Produto cadastrado com sucesso";
      $arRetorno[2] = $SQL;
      DBCLOSE($ConexaoMy);
      die(json_encode($arRetorno));
    } else if (!$rsAux) {
      $arRetorno[0] = "0";
      $arRetorno[1] = "Não foi possível cadastrar o produto (erro 1111)";
      $arRetorno[2] = $SQL;
      DBCLOSE($ConexaoMy);
      die(json_encode($arRetorno));
    } else {
      $arRetorno[0] = "2";
      $arRetorno[1] = "Debug";
      $arRetorno[2] = $SQL;
      DBCLOSE($ConexaoMy);
      die(json_encode($arRetorno));
    }
  }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estoque</title>
</head>

<body>

  <?php include './layout.php'; ?>
  <div class="wrapper">
    <div class="main-content">
      <div class="flex-grow-1 p-3">
        <div class="row">
          <section class="content-header">
            <h2>Pedidos <small class="fs-4" style="color: gray">gerenciamento de Pedidos</small></h2>
          </section>
        </div>
        <div class="row mt-4">
          <div id="div_consulta" class="col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  LISTA DE PEDIDOS
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
                      <input type="hidden" id="hid_id_produto">
                      <button type="button" class="btn btn-primary" onclick="Novo()"><i class="bi bi-plus-square" style="margin-right: 5px"></i>Novo Pedido</button>
                      <button type="button" class="btn btn-primary" onclick="PesquisaAvancada()"><span class="bi bi-funnel-fill"></span>&nbsp;&nbsp;FILTRO</button>
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
                            <th style="width:11%; text-align:center; vertical-align:middle;">Nome</th>
                            <th style="width:11%; text-align:center; vertical-align:middle; min-width: 250px">Descricao</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Valor</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Quantidade</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Categoria</th>
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
  </div>
  <div class="modal fade" id="novoProdutoModal" tabindex="-1" aria-labelledby="novoProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content bg-dark">
        <div class="modal-header bg-primary">
          <h5 class="modal-title" id="titulo_modal_novo_produto">Adicionar Novo Produto</h5>
          <button type="button" class="btn-close btn-close-white" onclick="window.location.reload();" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body bg-white text-black">
          <form>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nome_produto" class="form-label">Nome <span style="color: red">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-tag"></i>
                  </span>
                  <input type="text" class="form-control" id="nome_produto">
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="descricao_produto" class="form-label">Descrição <span style="color: red">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-card-text"></i>
                  </span>
                  <input type="text" class="form-control" id="descricao_produto">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-group">
                  <label for="valor_produto" class="form-label">Valor <span style="color: red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-cash-stack"></i>
                    </span>
                    <input type="text" class="form-control" id="valor_produto">
                  </div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="quantidade_produto" class="form-label">Quantidade <span style="color: red">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-hash"></i>
                  </span>
                  <input type="number" class="form-control" id="quantidade_produto">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="codigo_categoria_produto" class="form-label">Categoria do produto <span style="color: red">*</span></label>
                <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="codigo_categoria_produto" name="codigo_categoria_produto" style="width: 100%;">
                  <option value="">selecione</option>
                  <?php
                  $SQL = "SELECT id_categoria, nome_categoria
                      FROM logistic_module.categoria_produtos 
                      WHERE situacao = 1";
                  $rsTipo_categoria_produto = mysqli_query($ConexaoMy, $SQL);
                  while ($arTipo_categoria_produto = mysqli_fetch_assoc($rsTipo_categoria_produto)) {
                    $arTipo_categoria_produto = array_map("utf8_encode", $arTipo_categoria_produto);
                    echo "<option style='color:black; background-color: white' value='" . utf8_decode($arTipo_categoria_produto["id_categoria"]) . "'>" . utf8_decode($arTipo_categoria_produto["nome_categoria"]) . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
        </div>
        </form>
        <div class="modal-footer bg-primary">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.reload();">Fechar</button>
          <button type="button" class="btn btn-info" id="btn_cadastrar" onclick="Salvar()">Cadastrar</button>
        </div>
      </div>
    </div>
  </div>


  <script>
    $("#valor_produto").mask("#00,00", { reverse: true });
    var GLTabela = null;
    var GLFiltro = [];


    GLFiltro = {
      nome_produto_filtro: "",
      descricao_produto_filtro: "",
      valor_produto_filtro: "",
      quantidade_produto_filtro: "",
      codigo_categoria_produto_filtro: ""
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
    function PesquisaAvancada() {
      GLModalAtual = null;
      GLModalAtual = $("#filtroAvancado").modal("show");
    }

    function Filtrar() {
      GLFILTRO = {
        nome_produto_filtro: $("#nome_produto_filtro").val(),
        descricao_produto_filtro: $("#descricao_produto_filtro").val(),
        valor_produto_filtro: $("#valor_produto_filtro").val(),
        quantidade_produto_filtro: $("#quantidade_produto_filtro").val(),
        codigo_categoria_produto_filtro: $("#codigo_categoria_produto_filtro").val(),
      }
      GLTabela.ajax.url("<?php echo $_SERVER['PHP_SELF']; ?>?metodo=Consultar&filtro=" + JSON.stringify(GLFiltro)).load();
      if (GLModalAtual != null) {
        if (GLModalAtual.getState() == "opened") {
          GLModalAtual.close();
        }
      }
    }

    function InativarProduto(codigo) {
      Swal.fire({
        title: 'Tem certeza que deseja inativar este registro?',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        icon: 'question'
      }).then((result) => {
        if (result.isConfirmed) {
          var parametros = new FormData();
          parametros.append("metodo", "InativarProduto");
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
      var btn = document.getElementById('btn_cadastrar');
      if (btn.classList.contains('btn-primary')) {
        btn.innerHTML = 'EDITAR';
        btn.style.visibility = 'visible';
      }
      $("#hid_id_produto").val(codigo);
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
            $("#nome_produto").val(arRetorno.nome_produto);
            $("#descricao_produto").val(arRetorno.descricao_produto);
            $("#valor_produto").val(arRetorno.valor_produto);
            $("#quantidade_produto").val(arRetorno.quantidade_produto);
            $("#codigo_categoria_produto").select2("val", arRetorno.codigo_categoria_produto);

            $("#nome_produto").prop("disabled", flag_disabled == "1" ? false : true);
            $("#descricao_produto").prop("disabled", flag_disabled == "1" ? false : true);
            $("#valor_produto").prop("disabled", flag_disabled == "1" ? false : true);
            $("#quantidade_produto").prop("disabled", flag_disabled == "1" ? false : true);
            $("#codigo_categoria_produto").prop("disabled", flag_disabled == "1" ? false : true);
            $("#novoProdutoModal").modal("show");
            flag_disabled == "1" ? $("#titulo_modal_novo_produto").html("Editação do produto Cód: " + StringPad(codigo, "0000")) : $("#titulo_modal_novo_produto").html("Detalhe do produto Cód: " + StringPad(codigo, "0000"));
          } catch (erro) {
            alert("Não foi possível realizar esta operação! Contate a Skunby Tecnologia2222.");
            console.log(retorno);
            console.log(arRetorno);
          }
        }
      });
    }

    function Novo() {
      $("#titulo_modal_novo_produto").html("Novo Produto");
      $("#nome_produto").val("");
      $("#descricao_produto").val("");
      $("#valor_produto").val("");
      $("#quantidade_produto").val("");
      $("#codigo_categoria_produto").select2("val", "");
      $("#btn_salvar").prop("disabled", false);
      $("#novoProdutoModal").modal("show");
    }

    function Salvar() {

      var modal = $("#novoProdutoModal").modal("show");

      if ($("#nome_produto").val() == "" || $("#nome_produto").val() == null) {
        alert("Informe o nome do produto");
        $("#nome_produto").focus();
        return false;
      }

      if ($("#descricao_produto").val() == "" || $("#descricao_produto").val() == null) {
        alert("Informe a descrição do produto");
        $("#descricao_produto").focus();
        return false;
      }

      if ($("#valor_produto").val() == "" || $("#valor_produto").val() == null) {
        alert("Informe o valor do produto");
        $("#valor_produto").focus();
        return false;
      }

      if ($("#quantidade_produto").val() == "" || $("#quantidade_produto").val() == null) {
        alert("Informe a quantidade do produto");
        $("#quantidade_produto").focus();
        return false;
      }

      if ($("#codigo_categoria_produto").val() == "" || $("#codigo_categoria_produto").val() == null) {
        alert("Selecione a categoria do produto");
        $("#codigo_categoria_produto").focus();
        return false;
      }
      var parametros = new FormData();

      parametros.append("metodo", "Salvar");
      parametros.append("id_produto", $("#hid_id_produto").val());
      parametros.append("nome_produto", $("#nome_produto").val());
      parametros.append("descricao_produto", $("#descricao_produto").val());
      parametros.append("valor_produto", $("#valor_produto").val());
      parametros.append("quantidade_produto", $("#quantidade_produto").val());
      parametros.append("codigo_categoria_produto", $("#codigo_categoria_produto").val());

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
              $("#novoProdutoModal").hide();
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
    $(document).ready(function () {
      $('#codigo_categoria_produto').select2({
        dropdownParent: $("#novoProdutoModal")
      });
    });
  </script>

</body>

</html>