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
        <a href="#" class="nav-link active" aria-current="page">
          <i class="bi bi-house-door"></i>
          Página principal
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-speedometer2"></i>
          Dashboard (manutenção)
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-table"></i>
          Pedidos
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-grid"></i>
          Envios
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-cart3"></i>
          Produtos
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-white">
          <i class="bi bi-box-seam"></i>
          Estoque
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-white">
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