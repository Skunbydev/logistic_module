<?php session_name("login_cliente");
session_start();

if ($_SESSION["login_cliente_auth"] != "1") {
  $_SESSION["login_cliente_auth"] = "0";
  header('location: logout.php');
  exit;
}
include '../conexao_BD.php';
$ConexaoMy = DBConnectMy();

if (isset($_POST["metodo"]) && $_POST["metodo"] == 'Salvar') {
  $id_produto = $_POST["id_produto"];
  $nome_produto = $_POST["nome_produto"];
  $descricao_produto = $_POST["descricao_produto"];
  $valor_produto = $_POST["valor_produto"];
  $quantidade_produto = $_POST["quantidade_produto"];
  $codigo_categoria_produto = $_POST["codigo_categoria_produto"];

  $sql = "INSERT INTO estoque (nome_produto, descricao_produto, valor_produto, quantidade_produto, codigo_categoria_produto)
  VALUES ('$nome_produto', '$descricao_produto', '$valor_produto', '$quantidade_produto', '$codigo_categoria_produto')";

  $rsAux = mysqli_query($ConexaoMy, utf8_decode($sql));
  if ($rsAux) {
    $arRetorno[0] = "1";
    $arRetorno[1] = "Cadastrado com sucesso";
    $arRetorno[2] = $sql;
    DBCLOSE($ConexaoMy);
    die(json_encode($arRetorno));
  } else if (!$rsAux) {
    $arRetorno[0] = "0";
    $arRetorno[1] = "Deu bug viu";
    $arRetorno[2] = $sql;
    DBCLOSE($ConexaoMy);
    die(json_encode($arRetorno));
  } else {
    $arRetorno[0] = "2";
    $arRetorno[1] = "Debug";
    $arRetorno[2] = $sql;
    DBCLOSE($ConexaoMy);
    die(json_encode($arRetorno));
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
  <div class="flex-grow-1 p-3">
    <div class="row">
      <section class="content-header">
        <h2>Estoque <small class="fs-4" style="color: gray">gerenciamento de produtos</small></h2>
      </section>
      <section class="content mt-4">
        <div id="div_consulta" class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h4 class="card-title text-white mb-0">
                  LISTA DE PRODUTOS
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
                      <input type="hidden" id="hid_id_produto">
                      <button type="button" class="btn btn-primary" onclick="Novo()" id="btn_salvar">
                        <i class="bi bi-plus-square"></i> Novo Produto
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
      </section>
      <div class="modal fade" id="novoProdutoModal" tabindex="-1" aria-labelledby="novoProdutoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content bg-dark">
            <div class="modal-header">
              <h5 class="modal-title" id="novoProdutoModalLabel">Adicionar Novo Produto</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form>
                <div class="mb-3">
                  <label for="nome_produto" class="form-label">Nome</label>
                  <input type="text" class="form-control" id="nome_produto">
                </div>
                <div class="mb-3">
                  <label for="descricao_produto" class="form-label">Descrição</label>
                  <input type="text" class="form-control" id="descricao_produto">
                </div>
                <div class="mb-3">
                  <label for="valor_produto" class="form-label">Valor</label>
                  <input type="text" class="form-control" id="valor_produto">
                </div>
                <div class="mb-3">
                  <label for="quantidade_produto" class="form-label">Quantidade</label>
                  <input type="number" class="form-control" id="quantidade_produto">
                </div>
                <div class="mb-3">
                  <label for="codigo_categoria_produto" class="form-label">Categoria do produto</label>
                  <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="codigo_categoria_produto" name="codigo_categoria_produto" style="width: 100%;">
                    <option value="">selecione</option>
                    <?php
                    $SQL = "SELECT id_categoria, nome_categoria
                    FROM logistic_module.categoria_produtos 
                    WHERE situacao = 1";
                    $rsTipo_categoria_produto = mysqli_query($ConexaoMy, $SQL);
                    while ($arTipo_categoria_produto = mysqli_fetch_assoc($rsTipo_categoria_produto)) {
                      $arTipo_categoria_produto = array_map("utf8_encode", $arTipo_categoria_produto);
                      echo "<option value='" . utf8_decode($arTipo_categoria_produto["id_categoria"]) . "'>" . utf8_decode($arTipo_categoria_produto["nome_categoria"]) . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="">Fechar</button>
              <button type="button" class="btn btn-primary" onclick="Salvar()">Cadastrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $("#valor_produto").mask("#00,00", { reverse: true });


    function Novo() {
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
              modal.close();
              GLTabela.ajax.url("<?php echo $_SERVER['PHP_SELF'] ?>?metodo=Consultar&filtro=" + JSON.stringify(GLFiltro)).load();
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
  </script>

</body>

</html>