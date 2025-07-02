<?php
session_start();
require_once '../../model/config.php';

// Obtener operarios (rol_id = 2)
$operarios = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol_id = 2")->fetchAll(PDO::FETCH_ASSOC);

// Procesar creación o edición de tractor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    $id = $_POST['tractor_id'] ?? null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $dias_mantenimiento = $_POST['dias_mantenimiento'];
    $estado = $_POST['estado'];
    $fecha_mantenimiento = $_POST['fecha_proximo_mantenimiento'] ?? '';

    if (empty($fecha_mantenimiento)) {
        $fecha_mantenimiento = date('Y-m-d', strtotime("+$dias_mantenimiento days"));
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE tractores SET nombre = ?, descripcion = ?, fecha_proximo_mantenimiento = ?, dias_mantenimiento = ?, estado = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $fecha_mantenimiento, $dias_mantenimiento, $estado, $id]);
        $_SESSION['success'] = "Tractor actualizado.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tractores (nombre, descripcion, fecha_proximo_mantenimiento, dias_mantenimiento, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $fecha_mantenimiento, $dias_mantenimiento, $estado]);
        $_SESSION['success'] = "Tractor registrado.";
    }

    header("Location: gestionar_tractores.php");
    exit;
}

// Asignar operario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'asignar') {
    $tractor_id = $_POST['tractor_id'];
    $usuario_id = $_POST['usuario_id'];

    $stmt = $pdo->prepare("INSERT INTO asignaciones (tractor_id, usuario_id) VALUES (?, ?)");
    $stmt->execute([$tractor_id, $usuario_id]);
    $_SESSION['success'] = "Operario asignado al tractor.";
    header("Location: gestionar_tractores.php");
    exit;
}

// Eliminar tractor
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM tractores WHERE id = ?");
    $stmt->execute([$_GET['eliminar']]);
    $_SESSION['success'] = "Tractor eliminado.";
    header("Location: gestionar_tractores.php");
    exit;
}

// Obtener tractores con el último operario asignado
$tractores = $pdo->query("
    SELECT t.*, 
           (SELECT u.nombre FROM asignaciones a 
            JOIN usuarios u ON u.id = a.usuario_id 
            WHERE a.tractor_id = t.id 
            ORDER BY a.fecha_asignacion DESC LIMIT 1) AS operario
    FROM tractores t
")->fetchAll(PDO::FETCH_ASSOC);

// Si hay edición de tractor
$tractorEdit = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM tractores WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $tractorEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Tractores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>Gestión de Tractores</h2>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-5">
            <input type="hidden" name="accion" value="guardar">
            <input type="hidden" name="tractor_id" value="<?= $tractorEdit['id'] ?? '' ?>">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= $tractorEdit['nombre'] ?? '' ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control"><?= $tractorEdit['descripcion'] ?? '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha próximo mantenimiento</label>
                <input type="date" name="fecha_proximo_mantenimiento" class="form-control" value="<?= $tractorEdit['fecha_proximo_mantenimiento'] ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Días entre mantenimientos</label>
                <input type="number" name="dias_mantenimiento" class="form-control" value="<?= $tractorEdit['dias_mantenimiento'] ?? '30' ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="Activo" <?= ($tractorEdit['estado'] ?? '') === 'Activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= ($tractorEdit['estado'] ?? '') === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    <option value="En mantenimiento" <?= ($tractorEdit['estado'] ?? '') === 'En mantenimiento' ? 'selected' : '' ?>>En mantenimiento</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?= $tractorEdit ? 'Actualizar' : 'Registrar' ?> Tractor</button>
            <?php if ($tractorEdit): ?>
                <a href="gestionar_tractores.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </form>

        <h4>Tractores Registrados</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Próximo Mantenimiento</th>
                    <th>Días entre Mantenimientos</th>
                    <th>Estado</th>
                    <th>Operario Asignado</th>
                    <th>Acciones</th>
                    <th>Asignar Operario</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tractores as $tractor): ?>
                    <tr>
                        <td><?= $tractor['nombre'] ?></td>
                        <td><?= $tractor['descripcion'] ?></td>
                        <td><?= $tractor['fecha_proximo_mantenimiento'] ?></td>
                        <td><?= $tractor['dias_mantenimiento'] ?> días</td>
                        <td><?= $tractor['estado'] ?></td>
                        <td><?= $tractor['operario'] ?? 'No asignado' ?></td>
                        <td>
                            <a href="?editar=<?= $tractor['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="?eliminar=<?= $tractor['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este tractor?')">Eliminar</a>
                        </td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="accion" value="asignar">
                                <input type="hidden" name="tractor_id" value="<?= $tractor['id'] ?>">
                                <select name="usuario_id" class="form-select me-2" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($operarios as $op): ?>
                                        <option value="<?= $op['id'] ?>"><?= $op['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-success btn-sm">Asignar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>

</html>
