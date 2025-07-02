<?php
session_start();
require_once '../../model/config.php';

// Asegurarse de que solo entren usuarios con rol de operario
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: ../../auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener tractores asignados al usuario
$sql = "SELECT t.id, t.nombre 
        FROM tractores t
        JOIN asignaciones a ON a.tractor_id = t.id
        WHERE a.usuario_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$tractores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar envío del formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tractor_id = $_POST['tractor_id'];
    $observaciones = $_POST['observaciones'];
    $fecha = date('Y-m-d');
    $archivo = '';

    // Manejo del archivo si fue subido
    if (!empty($_FILES['archivo']['name'])) {
        $nombre_archivo = basename($_FILES['archivo']['name']);
        $ruta_archivo = "../../uploads/" . time() . "_" . $nombre_archivo;
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivo)) {
            $archivo = basename($ruta_archivo);
        }
    }

    // Insertar reporte
    $sql = "INSERT INTO reportes (tractor_id, usuario_id, fecha, observaciones, archivo_reporte)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tractor_id, $usuario_id, $fecha, $observaciones, $archivo]);

    $mensaje = "Reporte registrado exitosamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Reporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Registrar Reporte de Mantenimiento</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="tractor_id" class="form-label">Tractor asignado</label>
            <select name="tractor_id" id="tractor_id" class="form-select" required>
                <option value="">Seleccione un tractor</option>
                <?php foreach ($tractores as $tractor): ?>
                    <option value="<?= $tractor['id'] ?>"><?= $tractor['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="archivo" class="form-label">Archivo de Soporte (opcional)</label>
            <input type="file" name="archivo" id="archivo" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Registrar Reporte</button>
    </form>
</div>
</body>
</html>
