<?php
session_start();
require_once '../../model/config.php';

// Verificar que el usuario esté logueado como operario
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: ../../auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar reportes realizados por este usuario
$sql = "SELECT r.id, t.nombre AS tractor, r.fecha, r.observaciones, r.archivo_reporte
        FROM reportes r
        JOIN tractores t ON t.id = r.tractor_id
        WHERE r.usuario_id = ?
        ORDER BY r.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Historial de Reportes Realizados</h2>

    <?php if (count($reportes) > 0): ?>
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Tractor</th>
                    <th>Fecha</th>
                    <th>Observaciones</th>
                    <th>Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportes as $i => $reporte): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($reporte['tractor']) ?></td>
                        <td><?= $reporte['fecha'] ?></td>
                        <td><?= nl2br(htmlspecialchars($reporte['observaciones'])) ?></td>
                        <td>
                            <?php if ($reporte['archivo_reporte']): ?>
                                <a href="../../uploads/<?= htmlspecialchars($reporte['archivo_reporte']) ?>" target="_blank" class="btn btn-sm btn-primary">Ver archivo</a>
                            <?php else: ?>
                                <span class="text-muted">No adjunto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No has registrado ningún reporte aún.</div>
    <?php endif; ?>
</div>
</body>
</html>
