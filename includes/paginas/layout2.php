<?php include '../bootstrap.php'; ?>
<?php include '../scripts/script.php'; ?>

<style>
  body {
    background-color: #1e2125;
    color: white;
    margin: 0;
    padding: 0;
  }

  .main-header {
    background-color: #343a40;
  }

  .main-sidebar {
    background-color: #343a40;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    transition: transform 0.3s ease;
    transform: translateX(0);
    z-index: 1000;
  }

  .main-sidebar.collapsed {
    transform: translateX(-250px);
  }

  .main-content {
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s ease;
  }

  .main-content.collapsed {
    margin-left: 0;
  }

  .sidebar-toggle {
    position: fixed;
    top: 10px;
    left: 260px;
    z-index: 1100;
    cursor: pointer;
  }

  .sidebar-toggle.collapsed {
    left: 10px;
  }

  .sidebar-toggle .navbar-toggler-icon {
    display: inline-block;
  }

  @media (max-width: 768px) {
    .main-sidebar {
      transform: translateX(-250px);
    }

    .main-sidebar.collapsed {
      transform: translateX(0);
    }

    .main-content {
      margin-left: 0;
    }

    .sidebar-toggle {
      left: 10px;
    }

    .sidebar-toggle.collapsed {
      left: 260px;
    }
  }

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

<body>
  <div class="wrapper">
    <header class="main-header">
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <a href="#" class="nav-link">Item 1</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Item 2</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">Item 3</a>
              </li>
            </ul>
            <div class="dropdown">
              <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="me-2">Nome do Usuário</span>
                <img src="https://avatars.githubusercontent.com/u/122830909?v=4" alt="profile photo" width="32" height="32" class="rounded-circle">
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser1">
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
        </div>
      </nav>
    </header>
    <aside class="main-sidebar" style="height: 100vh">
      <div class="sidebar mt-2">
        <a href="../acesso/home.php" class="navbar-brand">
          <span class="logo-mini"><b>Skunby</b></span>
          <span class="logo-lg"><b>Skunby</b>Tecnologia</span>
        </a>
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
      </div>
    </aside>
    <button class="sidebar-toggle btn btn-primary" id="expandirSideBar" onclick="toggleSidebar()">
      <i class="bi bi-list"></i>
    </button>
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
    function toggleSidebar() {
      const sidebar = document.querySelector('.main-sidebar');
      const mainContent = document.querySelector('.main-content');
      const sidebarToggle = document.querySelector('.sidebar-toggle');

      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('collapsed');
      sidebarToggle.classList.toggle('collapsed');
    }
    window.onload = atualizarLinkAtivo;
  </script>
</body>