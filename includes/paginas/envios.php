<?php
session_name("login_cliente");
session_start();
include '../conexao_BD.php';
$ConexaoMy = DBConnectMy();
if ($_SESSION["login_cliente_auth"] != "1") {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: logout.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente_lista'])) {
  $id_cliente_lista = $_POST["id_cliente_lista"];
  error_log("Recebido id_cliente_lista: " . $id_cliente_lista);

  $SQL = "SELECT id_pedido, id_cliente_lista, valor_total, quantidade_produto, nome_produto, estado_cliente 
          FROM logistic_module.pedidos 
          WHERE id_cliente_lista = ? AND situacao = 1";
  $stmt = $ConexaoMy->prepare($SQL);
  $stmt->bind_param('i', $id_cliente_lista);
  $stmt->execute();
  $result = $stmt->get_result();
  error_log("Number of products found: " . $result->num_rows);
  header('Content-Type: application/json');

  if ($result->num_rows > 0) {
    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
      $pedidos[] = [
        'id_pedido' => $row['id_pedido'],
        'id_cliente_lista' => $row['id_cliente_lista'],
        'valor_total' => 'R$ ' . $row['valor_total'],
        'quantidade_produto' => $row['quantidade_produto'],
        'nome_produto' => $row['nome_produto'],
        'estado_cliente' => $row['estado_cliente']
      ];
    }
    $response = [
      'success' => true,
      'pedidos' => $pedidos
    ];
  } else {
    $response = [
      'success' => false,
      'message' => 'Nenhum pedido disponível para este cliente'
    ];
  }

  $stmt->close();
  $ConexaoMy->close();

  echo json_encode($response);
  exit;
}




if (isset($_POST["metodo"]) && $_POST["metodo"] == 'Salvar') {
  $id_envio = $_POST["id_envio"];
  $id_pedido = $_POST["id_pedido"];
  $id_cliente_lista = $_POST["id_cliente_lista"];
  $nome_produto = $_POST["nome_produto"];
  $quantidade_produto = $_POST["quantidade_produto"];
  $estado_cliente = $_POST["estado_cliente"];
  $transportadora_envio = $_POST["transportadora_envio"];

  $SQL = "INSERT INTO envios (id_pedido, id_cliente_lista, nome_produto, quantidade_produto, estado_cliente, transportadora_envio, situacao)
  VALUES ('$id_pedido', '$id_cliente_lista', '$nome_produto', '$quantidade_produto', '$estado_cliente', '$transportadora_envio', 1)";

  $rsAux = mysqli_query($ConexaoMy, $SQL);
  if ($rsAux) {
    $arRetorno[0] = "1";
    $arRetorno[1] = "Produto cadastrado com sucesso";
    $arRetorno[2] = $SQL;
    DBCLOSE($ConexaoMy);
    die(json_encode($arRetorno));
  } else {
    $arRetorno[0] = "0";
    $arRetorno[1] = "Não foi possível cadastrar o produto (erro 1111)";
    $arRetorno[2] = $SQL;
    DBCLOSE($ConexaoMy);
    die(json_encode($arRetorno));
  }
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
    <div class="wrapper">
      <div class="main-content">
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
                        <input type="hidden" id="hid_id_envio">
                        <button type="button" class="btn btn-primary" onclick="Novo()"><i class="bi bi-plus-square" style="margin-right: 5px"></i>Novo Envio</button>
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
                              <th style="width:11%; text-align:center; vertical-align:middle;">ID envio</th>
                              <th style="width:11%; text-align:center; vertical-align:middle;">ID pedido</th>
                              <th style="width:11%; text-align:center; vertical-align:middle; min-width: 250px">Nome Cliente</th>
                              <th style="width:11%; text-align:center; vertical-align:middle;">Valor Total</th>
                              <th style="width:11%; text-align:center; vertical-align:middle;">Quantidade</th>
                              <th style="width:11%; text-align:center; vertical-align:middle;">Item</th>
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
    <div class="modal fade" id="novoEnvioModal" tabindex="-1" aria-labelledby="novoEnvioModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
          <div class="modal-header bg-primary">
            <h5 class="modal-title" id="titulo_modal_novo_envio">Adicionar Novo Envio</h5>
            <button type="button" class="btn-close btn-close-white" onclick="window.location.reload();" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body bg-white text-black">
            <form>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="id_cliente_lista" class="form-label">Cliente <span style="color: red">*</span></label>
                  <input type="hidden" id="hid_valorCliente">
                  <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="id_cliente_lista" onchange="fetchClientOrders();" name="id_cliente_lista" style="width: 100%;">
                    <option value="">Selecione</option>
                    <?php
                    $SQL = "SELECT id_cliente_lista, nome_cliente_lista FROM logistic_module.lista_clientes WHERE situacao = 1";
                    $rsTipo_lista_clientes = mysqli_query($ConexaoMy, $SQL);
                    while ($arTipo_lista_clientes = mysqli_fetch_assoc($rsTipo_lista_clientes)) {
                      echo "<option style='color:black; background-color: white' value='" . $arTipo_lista_clientes["id_cliente_lista"] . "'>" . $arTipo_lista_clientes["id_cliente_lista"] . ' - ' . $arTipo_lista_clientes["nome_cliente_lista"] . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="id_pedido" class="form-label">Número Pedido<span style="color: red">*</span></label>
                  <input type="hidden" id="hid_id_pedido">
                  <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="id_pedido" name="id_pedido" style="width: 100%;">
                    <option value="">Selecione</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="valor_total" class="form-label">Valor total do pedido:<span style="color: red">*</span></label>
                  <input id="valor_total" class="form-control border p-3 rounded bg-light" type="text" value="Selecione uma categoria para visualizar o valor" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="nome_produto" class="form-label">nome do produto<span style="color: red">*</span></label>
                  <input id="nome_produto" class="form-control border p-3 rounded bg-light" type="text" value="Selecione uma categoria para visualizar o valor" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="quantidade_produto" class="form-label">Quantidade do produto<span style="color: red">*</span></label>
                  <input id="quantidade_produto" class="form-control border p-3 rounded bg-light" type="text" value="Selecione uma categoria para visualizar o valor" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="estado_cliente" class="form-label">Estado do cliente<span style="color: red">*</span></label>
                  <input id="estado_cliente" class="form-control border p-3 rounded bg-light" type="text" value="Selecione um cliente para visualizar" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <label for="transportadora_envio" class="form-label">Transportadora de envio<span style="color: red">*</span></label>
                  <input type="hidden" id="hid_id_transportadora">
                  <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="transportadora_envio" name="transportadora_envio" style="width: 100%;">

                    <option value="">Selecione</option>
                    <?php
                    $SQL = "SELECT id_transportadora, nome_transportadora FROM transportadoras WHERE situacao = 1";
                    $rsTransportadora_envio = mysqli_query($ConexaoMy, $SQL);
                    while ($arTransportadora_envio = mysqli_fetch_assoc($rsTransportadora_envio)) {
                      echo "<option style='color:black; background-color: white' value='" . $arTransportadora_envio["id_transportadora"] . "'>" . $arTransportadora_envio["id_transportadora"] . ' - ' . $arTransportadora_envio["nome_transportadora"] . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer bg-primary">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.reload();">Fechar</button>
            <button type="button" class="btn btn-info" id="btn_cadastrar" onclick="Salvar()">Cadastrar</button>
          </div>
        </div>
      </div>
    </div>



    <script>
      function Novo() {
        $("#titulo_modal_novo_envio").html("Novo Envio");
        $("#novoEnvioModal").modal("show");
      }


      function fetchClientOrders() {
        var selectElement = document.getElementById('id_cliente_lista');
        var selectedClientId = selectElement.value;
        document.getElementById('hid_valorCliente').value = selectedClientId;

        if (selectedClientId) {
          var xhr = new XMLHttpRequest();
          xhr.open('POST', 'envios.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
              if (xhr.status === 200) {
                try {
                  var response = JSON.parse(xhr.responseText);
                  var idPedido = document.getElementById('id_pedido');
                  var valorTotal = document.getElementById('valor_total');
                  var nomeProduto = document.getElementById('nome_produto');
                  var quantidadeProduto = document.getElementById('quantidade_produto');
                  var estadoCliente = document.getElementById('estado_cliente');
                  idPedido.innerHTML = '';

                  if (response.success) {
                    response.pedidos.forEach(function (pedido) {
                      var option = document.createElement('option');
                      option.value = pedido.id_pedido;
                      option.textContent = pedido.id_pedido;
                      idPedido.appendChild(option);
                    });
                    var primeiroPedido = response.pedidos[0];
                    valorTotal.value = primeiroPedido.valor_total;
                    nomeProduto.value = primeiroPedido.nome_produto;
                    quantidadeProduto.value = primeiroPedido.quantidade_produto;
                    estadoCliente.value = primeiroPedido.estado_cliente;
                  } else {
                    var option = document.createElement('option');
                    option.textContent = 'Nenhum pedido disponível';
                    idPedido.appendChild(option);
                    valorTotal.value = 'Nenhum pedido disponível';
                    nomeProduto.value = '';
                    quantidadeProduto.value = '';
                    estadoCliente.value = '';
                    console.error(response.message);
                  }
                } catch (e) {
                  console.error('Erro ao analisar resposta JSON', e);
                  idPedido.innerHTML = 'Erro ao buscar pedido';
                  valorTotal.value = 'Erro ao buscar pedido';
                  nomeProduto.value = '';
                  quantidadeProduto.value = '';
                  estadoCliente.value = '';
                }
              } else {
                console.error('Erro ao processar requisição', xhr.statusText);
                idPedido.innerHTML = 'Erro ao buscar pedido';
                valorTotal.value = 'Erro ao buscar pedido';
                nomeProduto.value = '';
                quantidadeProduto.value = '';
                estadoCliente.value = '';
              }
            }
          };
          xhr.send('id_cliente_lista=' + encodeURIComponent(selectedClientId));
        } else {
          document.getElementById('id_pedido').innerHTML = '<option value="">Selecione</option>';
          document.getElementById('valor_total').value = 'Selecione um pedido para visualizar';
          document.getElementById('nome_produto').value = 'Selecione um pedido para visualizar';
          document.getElementById('quantidade_produto').value = 'Selecione um pedido para visualizar';
          document.getElementById('estado_cliente').value = 'Selecione um pedido para visualizar';
        }
      }




      function validarCampos() {
        if ($("#transportadora_envio").val() == "" || $("#transportadora_envio").val() == null) {
          alert("Informe a transportadora do envio");
          $("#transportadora_envio").focus();
          return false;
        }
        if ($("#id_pedido").val() == "" || $("#id_pedido").val() == null) {
          alert("Informe o pedido do envio");
          $("#id_pedido").focus();
          return false;
        }
        if ($("#id_cliente_lista").val() == "" || $("#id_cliente_lista").val() == null) {
          alert("Informe o cliene do envio");
          $("#id_cliente_lista").focus();
          return false;
        }
        return true;
        console.log('passou por aqui!');
      }

      function Salvar() {
        if (!validarCampos()) {
          return false;
        }

        var parametros = new FormData();

        parametros.append("metodo", "Salvar");
        parametros.append("id_envio", $("#id_envio").val());
        parametros.append("id_cliente_lista", $("#hid_valorCliente").val());
        parametros.append("id_pedido", $("#id_pedido").val());
        parametros.append("nome_produto", $("#nome_produto").val());
        parametros.append("quantidade_produto", $("#quantidade_produto").val());
        parametros.append("estado_cliente", $("#estado_cliente").val());
        parametros.append("transportadora_envio", $("#transportadora_envio").val());

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
                $("#novoEnvioModal").hide();
                window.location.reload();
              } else {
                console.log(arRetorno);
              }
            } catch (erro) {
              console.log(retorno);
              alert("ERRO");
            }
          }
        });
      }




    </script>


</body>

</html>