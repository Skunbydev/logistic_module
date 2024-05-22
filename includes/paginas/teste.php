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

if (isset($_POST["metodo"]) && $_POST["metodo"] == "Salvar") {
  $id_pedido = $_POST["id_pedido"];
  $codigo_categoria_produto = $_POST["codigo_categoria_produto"];
  $id_cliente_lista = $_POST["id_cliente_lista"];
  $endereco_cliente = $_POST["endereco_cliente"];
  $numero_endereco_cliente = $_POST["numero_endereco_cliente"];
  $cep_cliente = $_POST["cep_cliente"];
  $quantidade_produto = $_POST["quantidade_produto"];
  $nome_produto = $_POST["nome_produto"];
  $valor_total = $_POST["valor_total"];


  if ($id_pedido != "") {
    $SQL = "UPDATE INTO";
  } else {
    $SQL = "INSERT INTO pedidos (codigo_categoria_produto, id_cliente_lista, endereco_cliente, numero_endereco_cliente, cep_cliente, quantidade_produto, nome_produto, valor_total) 
    VALUES ('$codigo_categoria_produto', '$id_cliente_lista', '$endereco_cliente', '$numero_endereco_cliente', '$cep_cliente', '$quantidade_produto', '$nome_produto', '$valor_total')";


    $rsAux = mysqli_query($ConexaoMy, $SQL);

    if ($rsAux) {
      $arRetorno[0] = "1";
      $arRetorno[1] = "Pedido cadastrado com sucesso!";
      $arRetorno[2] = $SQL;
      DBClose($ConexaoMy);
      die(json_encode($arRetorno));
    } else if (!$rsAux) {
      $arRetorno[0] = "0";
      $arRetorno[1] = "Pedido não cadastrado error C-[111]";
      $arRetorno[2] = $SQL;
      DBClose($ConexaoMy);
      die(json_encode($arRetorno));
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_id'])) {
  $client_id = intval($_POST['client_id']);

  $SQL = "SELECT endereco_cliente_lista, numero_rua_cliente_lista, cep_cliente_lista FROM logistic_module.lista_clientes WHERE id_cliente_lista = ? AND situacao = 1";
  $stmt = $ConexaoMy->prepare($SQL);
  $stmt->bind_param('i', $client_id);
  $stmt->execute();
  $result = $stmt->get_result();

  header('Content-Type: application/json');

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = [
      'success' => true,
      'address' => $row['endereco_cliente_lista'],
      'number' => utf8_encode($row['numero_rua_cliente_lista']),
      'cep' => utf8_encode($row["cep_cliente_lista"])
    ];
  } else {
    $response = [
      'success' => false,
      'message' => 'Endereço não encontrado'
    ];
  }

  $stmt->close();
  $ConexaoMy->close();

  echo json_encode($response);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoria_id'])) {
  $categoria_id = intval($_POST['categoria_id']);
  error_log("Received categoria_id: " . $categoria_id);

  $SQL = "SELECT nome_produto, valor_produto, quantidade_produto 
            FROM logistic_module.estoque 
            WHERE codigo_categoria_produto = ? AND situacao = 1";
  $stmt = $ConexaoMy->prepare($SQL);
  $stmt->bind_param('i', $categoria_id);
  $stmt->execute();
  $result = $stmt->get_result();
  error_log("Number of products found: " . $result->num_rows);

  header('Content-Type: application/json');

  if ($result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
      $products[] = [
        'nome_produto' => $row['nome_produto'],
        'valor_produto' => $row['valor_produto'],
        'quantidade_produto' => $row['quantidade_produto']
      ];
    }
    $response = [
      'success' => true,
      'products' => $products
    ];
  } else {
    $response = [
      'success' => false,
      'message' => 'Nenhum produto disponível para esta categoria'
    ];
  }
  $stmt->close();
  $ConexaoMy->close();

  echo json_encode($response);
  exit;
}

if (isset($_POST["metodo"]) && $_POST["metodo"] == "Carregar") {
  $arrayRetornoGeral = array();
  $SQL = "SELECT * FROM pedidos 
					WHERE id_pedido = '" . $_POST["codigo"] . "'";

  $rsDadosPedidos = mysqli_query($ConexaoMy, $SQL);
  $arDadosPedidos = mysqli_fetch_assoc($rsDadosPedidos);

  $arrayRetornoGeral = $arDadosPedidos;
  die(json_encode($arrayRetornoGeral));

}

if (isset($_GET['metodo']) && trim($_GET['metodo']) == "Consultar") {
  require ('../plugins/datatable_server_side/scripts/ssp.class.php');
  $SSP = new SSP();
  $columns = array(
    array('db' => 'pd.id_pedido', 'dt' => 0),
    array('db' => 'pd.nome_produto', 'dt' => 1),
    array('db' => 'pd.id_cliente_lista', 'dt' => 2),
    array('db' => 'pd.valor_total', 'dt' => 3),
    array('db' => 'pd.quantidade_produto', 'dt' => 4),
    array('db' => 'pd.endereco_cliente', 'dt' => 5),
    array('db' => 'pd.numero_endereco_cliente', 'dt' => 6),
    array('db' => 'pd.cep_cliente', 'dt' => 7),
  );
  $bindings = array();
  $limit = @$SSP->limit($_GET, $columns);
  $order = @$SSP->order($_GET, $columns);
  $where = @$SSP->filter($_GET, $columns, $bindings);

  $dados = array();
  $resTotalLength = 0;
  $recordsFiltered = 0;

  $SQL = "SELECT pd.id_pedido, pd.nome_produto, pd.id_cliente_lista, pd.valor_total, pd.quantidade_produto, pd.endereco_cliente, pd.numero_endereco_cliente, pd.cep_cliente, lc.nome_cliente_lista
  FROM pedidos pd
  LEFT JOIN lista_clientes lc ON lc.id_cliente_lista = pd.id_cliente_lista
  WHERE pd.situacao = 1";

  $filtro = json_decode($_GET["filtro"]);
  $i = 0;
  $Query = mysqli_query($ConexaoMy, utf8_decode($SQL));

  while ($Aux = mysqli_fetch_assoc($Query)) {
    $link_editar = "<a data-bs-toggle='tooltip' title='Editar pedido' data-hint='Detalhe da Ata' onclick='Carregar(" . $Aux["id_pedido"] . ", 1);' style='cursor:pointer; color:green;'>
    <i class='bi bi-pencil'></i>
</a>";


    $link_detalhe = "<a data-bs-toggle='tooltip' title='Detalhes do Pedido' data-hint='Detalhe da Ata' onclick='Carregar(" . $Aux["id_pedido"] . ", 0);' style='cursor:pointer; color:blue;'>
    <i class='bi bi-search'></i>
</a>";



    $link_inativar = "<a data-bs-toggle='tooltip' title='Inativar Produto' style='cursor:pointer; color:red;'>
    <i class='bi bi-x'></i>
</a>";

    $dados[$i][] = $link_editar;
    $dados[$i][] = $link_detalhe;
    $dados[$i][] = $link_inativar;

    $dados[$i][] = '<span style="width: 100px;">' . $Aux["id_pedido"] . '</span>';
    // $dados[$i][] = "<span style='display: block; margin-left: auto; margin-right: auto; text-align: center; min-width: 600px;'>" . $Aux["nome_cliente_lista"] . "</span>";
    $dados[$i][] = $Aux["nome_cliente_lista"];
    $dados[$i][] = '<span style="width: 100px;">' . $Aux["endereco_cliente"] . '</span>';
    $dados[$i][] = '<span style="width: 100px;">' . $Aux["numero_endereco_cliente"] . '</span>';
    $dados[$i][] = '<span style="width: 100px;">' . $Aux["cep_cliente"] . '</span>';
    $dados[$i][] = '<span style="width: 100px;">' . $Aux["nome_produto"] . '</span>';
    $dados[$i][] = '<span style="width: 100px;">' . $Aux["quantidade_produto"] . '</span>';
    $dados[$i][] = '<span style="width: 100px;">' . 'R$ ' . $Aux["valor_total"] . '</span>';
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
  <title>Pedidos</title>
</head>

<body>
  <?php include 'layout2.php' ?>
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
                          <tr class="bg-primary text-white">
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width: 1%"></th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Código</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Nome Cliente</th>
                            <th style="width:11%; text-align:center; vertical-align:middle; min-width: 250px">Endereço</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Numero</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Cep</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Nome produto</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Quantidade</th>
                            <th style="width:11%; text-align:center; vertical-align:middle;">Valor Total</th>
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
      <div class="modal fade" id="novoPedidoModal" tabindex="-1" aria-labelledby="novoPedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content bg-dark">
            <div class="modal-header bg-primary">
              <h5 class="modal-title" id="titulo_modal_novo_pedido">Adicionar Novo Pedido</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white text-black">
              <input type="hidden" id="hid_id_pedido">
              <form>
                <div class="row">
                  <div class="col-md-12 mb-6">
                    <label for="id_cliente_lista" class="form-label">Cliente <span style="color: red">*</span></label>
                    <input type="hidden" id="hid_valorCliente">
                    <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="id_cliente_lista" onchange="fetchClientAddress();" name="id_cliente_lista" style="width: 100%;">
                      <option value="">Selecione</option>
                      <?php
                      $SQL = "SELECT id_cliente_lista, nome_cliente_lista, email_cliente_lista, telefone_cliente_lista, endereco_cliente_lista
                        FROM logistic_module.lista_clientes 
                        WHERE situacao = 1";
                      $rsTipo_lista_clientes = mysqli_query($ConexaoMy, $SQL);
                      while ($arTipo_lista_clientes = mysqli_fetch_assoc($rsTipo_lista_clientes)) {
                        $arTipo_lista_clientes = array_map("utf8_encode", $arTipo_lista_clientes);
                        echo "<option style='color:black; background-color: white' value='" . utf8_decode($arTipo_lista_clientes["id_cliente_lista"]) . "'>" . $arTipo_lista_clientes["id_cliente_lista"] . ' - ' . utf8_decode($arTipo_lista_clientes["nome_cliente_lista"]) . "</option>";
                      }
                      ?>
                    </select>

                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-6">
                    <label for="endereco_cliente" class="form-label">Endereço do cliente <span style="color: red">*</span></label>
                    <textarea id="endereco_cliente" class="form-control border p-3 rounded bg-light" cols="20" rows="1">Selecione um cliente para visualizar o endereço</textarea>

                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="numero_rua" class="form-label">Numero <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="numero_endereco_cliente" placeholder="Digite o número do endereço do cliente">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="cep_cliente" class="form-label">Cep Cliente <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="cep_cliente" class="form-control" placeholder="Digite o cep do endereço do cliente">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <input type="hidden" id="hid_codigoCategoria">
                    <label for="codigo_categoria_produto" class="form-label">Categoria do produto <span style="color: red">*</span></label>
                    <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="codigo_categoria_produto" name="codigo_categoria_produto" style="width: 100%;" onchange="fetchProdutos()">
                      <option value="">selecione</option>
                      <?php
                      $SQL = "SELECT id_categoria, nome_categoria FROM logistic_module.categoria_produtos WHERE situacao = 1";
                      $rsTipo_categoria_produto = mysqli_query($ConexaoMy, $SQL);
                      while ($arTipo_categoria_produto = mysqli_fetch_assoc($rsTipo_categoria_produto)) {
                        $arTipo_categoria_produto = array_map("utf8_encode", $arTipo_categoria_produto);
                        echo "<option style='color:black; background-color: white' value='" . utf8_decode($arTipo_categoria_produto["id_categoria"]) . "'>" . utf8_decode($arTipo_categoria_produto["nome_categoria"]) . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="nome_produto" class="form-label">Nome do produto <span style="color: red">*</span></label>
                    <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="nome_produto" name="nome_produto" style="width: 100%;">
                      <option value="">selecione</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="valor_produto_banco" class="form-label">Valor (unitário) <span style="color: red">*</span></label>
                    <input id="valor_produto_banco" class="form-control border p-3 rounded bg-light" type="text" value="Selecione uma categoria para visualizar o valor" readonly>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="quantidade_produto_banco" class="form-label">Quantidade disponível:<span style="color: red">*</span></label>
                    <input id="quantidade_produto_banco" class="form-control border p-3 rounded bg-light" type="text" value="Selecione uma categoria para visualizar o estoque" readonly>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="quantidade_produto" class="form-label">Quantidade ofertada <span style="color: red">*</span></label>
                    <input type="number" class="form-control" id="quantidade_produto" class="form-control" placeholder="Insira a quantidade desejada pelo cliente" onchange="calcularValorTotal()">

                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="valor_total" class="form-label">Valor TOTAL<span style="color: red">*</span></label>
                    <input id="valor_total" class="form-control border p-3 rounded bg-light" type="text" value="Selecione uma categoria para visualizar o valor" readonly>
                  </div>
                </div>
              </form>
            </div>


            <div class="modal-footer bg-primary">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="recarregarPagina();">Fechar</button>
              <button type="button" class="btn btn-info" id="btn_cadastrar" onclick="Salvar()">Cadastrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<script>
  $(document).ready(function () {
    $('#codigo_categoria_produto').select2({
      dropdownParent: $("#novoPedidoModal")
    });
  });
  $(document).ready(function () {
    $('#nome_produto').select2({
      dropdownParent: $("#novoPedidoModal")
    });
  });
  var GLTabela = null;
  var GLFiltro = [];

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

  function fetchClientAddress() {
    var selectElement = document.getElementById('id_cliente_lista');
    var selectedClientId = selectElement.value;
    document.getElementById('hid_codigoCategoria').value = selectedClientId;

    if (selectedClientId) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'pedidos.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            try {
              var response = JSON.parse(xhr.responseText);
              if (response.success) {
                document.getElementById('endereco_cliente').innerHTML = response.address;
                document.getElementById('numero_endereco_cliente').value = response.number;
                document.getElementById('cep_cliente').value = response.cep;
              } else {
                document.getElementById('endereco_cliente').innerHTML = 'Endereço não encontrado';
              }
            } catch (e) {
              console.error('Erro ao analisar resposta JSON: ', e);
              console.log(e.JSON);
              document.getElementById('endereco_cliente').innerHTML = 'Erro ao buscar endereço';
            }
          } else {
            console.error('Erro na requisição: ', xhr.statusText);
            document.getElementById('endereco_cliente').innerHTML = 'Erro ao buscar endereço';
          }
        }
      };
      xhr.send('client_id=' + selectedClientId);
    } else {
      document.getElementById('endereco_cliente').innerHTML = '';
    }
  }

  var produtoSelecionado = null;

  var produtos = [];
  function recarregarPagina() {
    window.location.reload(true);
  }
  function fetchProdutos() {
    var selectElement = document.getElementById('codigo_categoria_produto');
    var selectedCategoryId = selectElement.value;
    document.getElementById('hid_valorCliente').value = selectedCategoryId;

    if (selectedCategoryId) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'pedidos.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            try {
              var response = JSON.parse(xhr.responseText);


              var nomeProduto = document.getElementById('nome_produto');
              nomeProduto.innerHTML = '';
              produtos = [];

              if (response.success) {
                response.products.forEach(function (product) {
                  produtos.push(product);

                  var option = document.createElement('option');
                  option.value = product.nome_produto;
                  option.textContent = product.nome_produto;
                  nomeProduto.appendChild(option);
                });
                produtoSelecionado = produtos[0];
                document.getElementById('valor_produto_banco').value = 'R$ ' + produtoSelecionado.valor_produto;
                document.getElementById('quantidade_produto_banco').value = produtoSelecionado.quantidade_produto;

              } else {
                var option = document.createElement('option');
                option.textContent = 'Nenhum produto disponível';
                nomeProduto.appendChild(option);
              }
            } catch (e) {
              console.error('Erro ao analisar resposta JSON: ', e);
              document.getElementById('nome_produto').innerHTML = 'Erro ao buscar produtos';
            }
          } else {
            console.error('Erro na requisição: ', xhr.statusText);
            document.getElementById('nome_produto').innerHTML = 'Erro ao buscar produtos';
          }
        }
      };
      xhr.send('categoria_id=' + selectedCategoryId);
    } else {
      document.getElementById('nome_produto').innerHTML = '<option value="">selecione</option>';
    }
  }
  function calcularValorTotal() {
    var nomeProdutoSelect = document.getElementById('nome_produto');
    var produtoSelecionadoNome = nomeProdutoSelect.value;
    var produtoSelecionado = produtos.find(p => p.nome_produto === produtoSelecionadoNome);

    if (produtoSelecionado) {
      var quantidadeDesejada = parseInt(document.getElementById('quantidade_produto').value);
      var quantidadeDisponivel = parseInt(produtoSelecionado.quantidade_produto);
      var valorUnitario = parseFloat(produtoSelecionado.valor_produto.replace(',', '.'));

      if (isNaN(quantidadeDesejada) || quantidadeDesejada <= 0) {
        document.getElementById('valor_total').value = 'Quantidade inválida';
        return;
      }

      if (quantidadeDesejada > quantidadeDisponivel) {
        document.getElementById('valor_total').value = 'Apenas ' + quantidadeDisponivel + ' disponíveis';
      } else {
        var valorTotal = quantidadeDesejada * valorUnitario;
        document.getElementById('valor_total').value = 'R$ ' + valorTotal.toFixed(2).replace('.', ',');
      }
    }
  }

  function Novo() {
    $("#titulo_modal_novo_pedido").html("Novo Pedido");
    $("#expandirSideBar").hide();

    // $("#id_cliente_lista").select2("val", "");
    //$("#endereco_cliente").val("");
    $("#numero_endereco_cliente").val("");
    $("#cep_cliente").val("");
    $("#codigo_categoria_produto").select2("val", "");
    $("#nome_produto").select2("val", "");
    $("#quantidade_produto").val("");
    // $("#valor_total").val("");
    $("#novoPedidoModal").modal("show");
  }

  function Salvar() {

    var modal = $("#novoPedidoModal").modal("show");

    if ($("#id_cliente_lista").val() == "" || $("#id_cliente_lista").val() == null) {
      alert("Informe o cliente do produto");
      $("#id_cliente_lista").focus();
      return false;
    }
    if ($("#endereco_cliente").val() == "" || $("#endereco_cliente").val() == null) {
      alert("Informe o endereço do cliente");
      $("#endereco_cliente").focus();
      return false;
    }
    if ($("#numero_endereco_cliente").val() == "" || $("#numero_endereco_cliente").val() == null) {
      alert("Informe o numero do endereço");
      $("#numero_endereco_cliente").focus();
      return false;
    }
    if ($("#cep_cliente").val() == "" || $("#cep_cliente").val() == null) {
      alert("Informe o cep do endereço");
      $("#cep_cliente").focus();
      return false;
    }
    if ($("#codigo_categoria_produto").val() == "" || $("#codigo_categoria_produto").val() == null) {
      alert("Informe a categoria do produto");
      $("#codigo_categoria_produto").focus();
      return false;
    }
    if ($("#nome_produto").val() == "" || $("#nome_produto").val() == null) {
      alert("Informe o nome do produto");
      $("#nome_produto").focus();
      return false;
    }
    if ($("#quantidade_produto").val() == "" || $("#quantidade_produto").val() == null) {
      alert("Informe a quantidade de produto");
      $("#quantidade_produto").focus();
      return false;
    }
    var parametros = new FormData();
    parametros.append("metodo", "Salvar");
    parametros.append("id_pedido", $("#hid_id_pedido").val());
    parametros.append("id_cliente_lista", $("#id_cliente_lista").val());
    parametros.append("endereco_cliente", $("#endereco_cliente").val());
    parametros.append("numero_endereco_cliente", $("#numero_endereco_cliente").val());
    parametros.append("cep_cliente", $("#cep_cliente").val());
    parametros.append("codigo_categoria_produto", $("#codigo_categoria_produto").val());
    parametros.append("nome_produto", $("#nome_produto").val());
    parametros.append("quantidade_produto", $("#quantidade_produto").val());
    parametros.append("valor_total", $("#valor_total").val());


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
            $("#novoPedidoModal").hide();
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
    $("#hid_id_pedido").val(codigo);
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
          $("#id_cliente_lista").val(arRetorno.id_cliente_lista);
          $("#endereco_cliente").val(arRetorno.endereco_cliente);
          $("#numero_endereco_cliente").val(arRetorno.numero_endereco_cliente);
          $("#cep_cliente").val(arRetorno.cep_cliente);
          $("#codigo_categoria_produto").select2("val", arRetorno.codigo_categoria_produto);
          $("#nome_produto").val(arRetorno.nome_produto);
          $("#quantidade_produto").val(arRetorno.quantidade_produto);
          $("#valor_total").val(arRetorno.valor_total);


          $("#id_cliente_lista").prop("disabled", flag_disabled == "1" ? false : true);
          $("#endereco_cliente").prop("disabled", flag_disabled == "1" ? false : true);
          $("#numero_endereco_cliente").prop("disabled", flag_disabled == "1" ? false : true);
          $("#cep_cliente").prop("disabled", flag_disabled == "1" ? false : true);
          $("#codigo_categoria_produto").prop("disabled", flag_disabled == "1" ? false : true);
          $("#nome_produto").prop("disabled", flag_disabled == "1" ? false : true);
          $("#quantidade_produto").prop("disabled", flag_disabled == "1" ? false : true);
          $("#valor_total").prop("disabled", flag_disabled == "1" ? false : true);

          $("#novoPedidoModal").modal("show");
          flag_disabled == "1" ? $("#titulo_modal_novo_pedido").html("Editação do produto Cód: " + StringPad(codigo, "0000")) : $("#titulo_modal_novo_pedido").html("Detalhe do produto Cód: " + StringPad(codigo, "0000"));
          if (flag_disabled == 0) {
            $("#btn_cadastrar").hide();
          } if (flag_disabled == 1) {
            $("#btn_cadastrar").show();
            var btn = $("#btn_cadastrar");
            btn.text("Editar");

          }
        } catch (erro) {
          alert("Não foi possível realizar esta operação! C-2222.");
          console.log(retorno);
          console.log(arRetorno);
        }
      }
    });
  }

</script>

</body>

</html>