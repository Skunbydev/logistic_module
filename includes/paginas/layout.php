<?php include '../bootstrap.php'; ?>
<?php include '../scripts/script.php'; ?>

<style>
  .nav-link:hover {
    color: #FF69B4 !important;
    transition: color 0.3s ease;
  }

  .dropdown-menu {
    border: none;
    background-color: #343a40;
  }

  .dropdown-item {
    color: #FFFFFF;
  }

  .dropdown-item:hover {
    background-color: #495057;
  }
</style>

<body style="background-color: #1e2125 !important; color: white">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <a href="/" class="d-flex align-items-center m-2 m-lg-0 text-dark text-decoration-none">
          <h1 class="h4 mb-0 text-white">PROD</h1>
        </a>

        <ul class="nav col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="" class="nav-link text-white px-2 link-secondary">item 1</a></li>
          <li><a href="" class="nav-link text-white px-2 link-secondary">item 2</a></li>
          <li><a href="" class="nav-link text-white px-2 link-secondary">item 3</a></li>
        </ul>
      </div>
      <div class="dropdown">
        <a href="#" class="d-block link-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://avatars.githubusercontent.com/u/122830909?v=4" alt="profile photo" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu text-small dropdown-menu-right" aria-labelledby="dropdownUser1">
          <li><a class="dropdown-item" href="#">Novo pedido...</a></li>
          <li><a class="dropdown-item" href="#">Configurações</a></li>
          <li><a class="dropdown-item" href="#">Meu Perfil</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="#" onclick="deslogar();">Deslogar</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="d-flex flex-row">
    <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; height: 93vh;">
      <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi pe-none me-2" width="40" height="32">
          <use xlink:href="#bootstrap"></use>
        </svg>
        <span class="fs-4">Dashboard</span>
      </a>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <a href="./home.php" class="nav-link active" aria-current="page">
            <i class="bi bi-house-door"></i>
            Página principal
          </a>
        </li>
        <li class="nav-item">
          <a href="./dashboard.php" class="nav-link text-white">
            <i class="bi bi-speedometer2"></i>
            Dashboard (manutenção)
          </a>
        </li>
        <li class="nav-item">
          <a href="./pedidos.php" class="nav-link text-white">
            <i class="bi bi-table"></i>
            Pedidos
          </a>
        </li>
        <li class="nav-item">
          <a href="./envios.php" class="nav-link text-white">
            <i class="bi bi-grid"></i>
            Envios
          </a>
        </li>
        <li class="nav-item">
          <a href="./produtos.php" class="nav-link text-white">
            <i class="bi bi-cart3"></i>
            Produtos
          </a>
        </li>
        <li class="nav-item">
          <a href="./estoque.php" class="nav-link text-white">
            <i class="bi bi-box-seam"></i>
            Estoque
          </a>
        </li>
        <li class="nav-item">
          <a href="./lista_clientes.php" class="nav-link text-white">
            <i class="bi bi-person-lines-fill"></i>
            Lista Clientes
          </a>
        </li>
      </ul>

      <hr>
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://avatars.githubusercontent.com/u/122830909?v=4" alt="profile photo" width="32" height="32" class="rounded-circle me-2">
          <strong>Luiz Felipe S.</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
          <li><a class="dropdown-item" href="#">New project...</a></li>
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="#">Sign out</a></li>
        </ul>
      </div>
    </div>

    <script>
      function deslogar() {
        Swal.fire({
          title: 'Tem certeza que deseja sair?',
          showCancelButton: true,
          confirmButtonText: 'Sim',
          cancelButtonText: 'Não',
          icon: 'question'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = './logout.php';
          }
        });
      }
      function atualizarLinkAtivo() {
        var links = document.querySelectorAll('.nav-link');
        links.forEach(function (link) {
          link.classList.remove('active');
          link.style.color = 'white';
          var urlSemHash = window.location.href.split('#')[0];
          if (link.href === urlSemHash) {
            link.classList.add('active');
          }
        });
      }

      window.onload = atualizarLinkAtivo;


    </script>