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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedidos</title>
</head>

<body>
  <?php include 'layout.php' ?>
  <div class="flex-grow-1 p-3">
    <div class="row">
      <section class="content-header">
        <h2>Pedidos <small class="fs-4" style="color: gray">gerenciamento de PEDIDOS</small></h2>
      </section>
      <section class="content mt-4">
        <div id="div_consulta" class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h4 class="card-title text-white mb-0">
                  LISTA DE PEDIDOS
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
                        <i class="bi bi-plus-square"></i> Novo pedido
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

        <div class="modal fade" id="novoPedidoModal" tabindex="-1" aria-labelledby="novoPedidoModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark">
              <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titulo_modal_novo_pedido">Adicionar Novo Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body bg-white text-black">
                <form>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="cliente_pedido" class="form-label">Cliente <span style="color: red">*</span></label>
                      <select class="form-control select2" data-placeholder="Selecione" data-allow-clear="true" id="cliente_pedido" name="cliente_pedido" style="width: 100%;">
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
                    <div class="col-md-6 mb-3">
                      <label for="descricao_produto" class="form-label">Descrição <span style="color: red">*</span></label>
                      <input type="text" class="form-control" id="descricao_produto">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="valor_produto" class="form-label">Valor <span style="color: red">*</span></label>
                      <input type="text" class="form-control" id="valor_produto">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="quantidade_produto" class="form-label">Quantidade <span style="color: red">*</span></label>
                      <input type="number" class="form-control" id="quantidade_produto">
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
          function Novo() {
            $("#novoPedidoModal").modal("show");
          }
        </script>


</body>

</html>