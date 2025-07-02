<?php
session_start();
require_once '../../model/config.php';

// Obtener lista de tractores
$tractores = $pdo->query("SELECT id, nombre FROM tractores")->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de trabajadores (rol_id = 2)
$trabajadores = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol_id = 2")->fetchAll(PDO::FETCH_ASSOC);

// Inicializar variables de filtrado
$filtro_tractor = $_GET['tractor_id'] ?? '';
$filtro_trabajador = $_GET['usuario_id'] ?? '';

// Construir consulta base
$sql = "
    SELECT r.id, r.fecha, r.observaciones, r.archivo_reporte,
           t.nombre AS tractor, u.nombre AS trabajador
    FROM reportes r
    JOIN tractores t ON r.tractor_id = t.id
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE 1=1
";

// Agregar condiciones de filtrado
$params = [];
if (!empty($filtro_tractor)) {
    $sql .= " AND r.tractor_id = ?";
    $params[] = $filtro_tractor;
}
if (!empty($filtro_trabajador)) {
    $sql .= " AND r.usuario_id = ?";
    $params[] = $filtro_trabajador;
}

$sql .= " ORDER BY r.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>Reportes de Mantenimiento</h2>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="tractor_id" class="form-label">Filtrar por Tractor</label>
                <select name="tractor_id" id="tractor_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($tractores as $tractor): ?>
                        <option value="<?= $tractor['id'] ?>" <?= $filtro_tractor == $tractor['id'] ? 'selected' : '' ?>>
                            <?= $tractor['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="usuario_id" class="form-label">Filtrar por Trabajador</label>
                <select name="usuario_id" id="usuario_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($trabajadores as $trabajador): ?>
                        <option value="<?= $trabajador['id'] ?>" <?= $filtro_trabajador == $trabajador['id'] ? 'selected' : '' ?>>
                            <?= $trabajador['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tractor</th>
                    <th>Trabajador</th>
                    <th>Observaciones</th>
                    <th>Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reportes) > 0): ?>
                    <?php foreach ($reportes as $reporte): ?>
                        <tr>
                            <td><?= htmlspecialchars($reporte['fecha']) ?></td>
                            <td><?= htmlspecialchars($reporte['tractor']) ?></td>
                            <td><?= htmlspecialchars($reporte['trabajador']) ?></td>
                            <td><?= nl2br(htmlspecialchars($reporte['observaciones'])) ?></td>
                            <td>
                                <?php if (!empty($reporte['archivo_reporte'])): ?>
                                    <a href="../../uploads/<?= htmlspecialchars($reporte['archivo_reporte']) ?>" target="_blank">Ver Archivo</a>
                                <?php else: ?>
                                    Sin archivo
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No se encontraron reportes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
