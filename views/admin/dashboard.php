<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-5">
    <h1 class="mb-4">Bienvenido, <?= $_SESSION['usuario'] ?> (Administrador)</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <p class="card-text">Crear, editar o eliminar cuentas de usuarios.</p>
                    <a href="gestionar_trabajadores.php" class="btn btn-light">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gestion Tractores</h5>
                    <p class="card-text">Ver y gestionar tractores.</p>
                    <a href="gestionar_tractores.php" class="btn btn-light">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Mantenimiento Programado</h5>
                    <p class="card-text">Ver y gestionar mantenimientos.</p>
                    <a href="reportes_mantenimiento.php" class="btn btn-light">Ir</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
