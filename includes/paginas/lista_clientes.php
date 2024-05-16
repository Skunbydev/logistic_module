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
  $SQL = "SELECT * FROM lista_clientes WHERE id_cliente_lista = '" . $_POST["codigo"] . "'";

  $rsDadosLista = mysqli_query($ConexaoMy, $SQL);
  $arDadosLista = mysqli_fetch_assoc($rsDadosLista);

  $arrayRetornoGeral = $arDadosLista;
  die(json_encode($arrayRetornoGeral));
}

if (isset($_POST["metodo"]) && $_POST["metodo"] == "Salvar") {

  $nome_cliente_lista = $_POST["nome_cliente_lista"];
  $email_cliente_lista = $_POST["email_cliente_lista"];
  $telefone_cliente_lista = $_POST["telefone_cliente_lista"];
  $endereco_cliente_lista = $_POST["endereco_cliente_lista"];

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
                      <input type="hidden" id="hid_id_cliente">
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
          function Novo() {
            $("#novoClienteListaModal").modal("show");
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
                    //GLTabela.ajax.url("<?php echo $_SERVER['PHP_SELF']; ?>?metodo=Consultar&filtro=" + JSON.stringify(GLFiltro)).load();
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
        </script>
</body>

</html>