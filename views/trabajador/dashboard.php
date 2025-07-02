<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'trabajador') {
    header("Location: ../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Trabajador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-5">
    <h1 class="mb-4">Hola, <?= $_SESSION['usuario'] ?> (Trabajador)</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Mis Tractores</h5>
                    <p class="card-text">Consulta tus tareas asignadas.</p>
                    <a href="mi_tractor.php" class="btn btn-dark">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Registrar Actividad</h5>
                    <p class="card-text">Reporta los trabajos realizados.</p>
                    <a href="crear_reporte.php" class="btn btn-dark">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Historial de Mantenimientos</h5>
                    <p class="card-text">Consulta tu historial de mantenimientos.</p>
                    <a href="historial_reportes.php" class="btn btn-dark">Ir</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
