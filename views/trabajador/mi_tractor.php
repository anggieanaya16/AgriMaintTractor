<?php
session_start();
require_once '../../model/config.php';

// Verificar que el usuario esté logueado como operario
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: ../../auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar tractores asignados al trabajador y su último mantenimiento
$sql = "
    SELECT 
        t.id,
        t.nombre,
        t.descripcion,
        t.estado,
        t.fecha_proximo_mantenimiento,
        (
            SELECT MAX(fecha)
            FROM reportes r
            WHERE r.tractor_id = t.id
        ) AS ultima_fecha_mantenimiento
    FROM asignaciones a
    INNER JOIN tractores t ON a.tractor_id = t.id
    WHERE a.usuario_id = ?
    ORDER BY t.nombre
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$tractores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Tractores Asignados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Tractores Asignados</h2>

    <?php if (count($tractores) > 0): ?>
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Último Mantenimiento</th>
                    <th>Próximo Mantenimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tractores as $i => $tractor): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($tractor['nombre']) ?></td>
                        <td><?= nl2br(htmlspecialchars($tractor['descripcion'])) ?></td>
                        <td><?= $tractor['estado'] ?></td>
                        <td><?= $tractor['ultima_fecha_mantenimiento'] ?? 'Sin reportes' ?></td>
                        <td><?= $tractor['fecha_proximo_mantenimiento'] ?? 'No definido' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No tienes tractores asignados actualmente.</div>
    <?php endif; ?>
</div>
</body>
</html>
