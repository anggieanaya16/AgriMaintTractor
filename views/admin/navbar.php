<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Admin - Tractores</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link">👤 <?= $_SESSION['usuario'] ?></span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../auth/logout.php">Cerrar sesión</a>
        </li>
      </ul>
    </div>
  </div>
</nav>