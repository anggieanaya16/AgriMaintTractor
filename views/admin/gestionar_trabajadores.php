<?php
session_start();
require_once '../../model/config.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Función para generar contraseña aleatoria
function generarContrasena($longitud = 8) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $longitud);
}

// Eliminar trabajador
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Trabajador eliminado correctamente.";
    header("Location: gestionar_trabajadores.php");
    exit;
}

// Reestablecer contraseña
if (isset($_GET['reset'])) {
    $id = $_GET['reset'];
    $stmt = $pdo->prepare("SELECT nombre, correo FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $clavePlano = generarContrasena();
        $claveHash = password_hash($clavePlano, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
        $stmt->execute([$claveHash, $id]);

        // Enviar nueva contraseña por correo
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'andresfelipealmario2309@gmail.com'; // tu correo
            $mail->Password = 'dbmu jcrn tiev yony'; // tu clave de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('andresfelipealmario2309@gmail.com', 'Administrador');
            $mail->addAddress($usuario['correo'], $usuario['nombre']);

            $mail->Subject = 'Reestablecimiento de contraseña';
            $mail->Body    = "Hola {$usuario['nombre']},\n\nTu nueva contraseña es: $clavePlano\n\nPor favor cámbiala al ingresar.";

            $mail->send();
            $_SESSION['success'] = "Contraseña reestablecida y enviada por correo.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al enviar correo: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Trabajador no encontrado.";
    }
    header("Location: gestionar_trabajadores.php");
    exit;
}

// Registrar nuevo trabajador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';

    if (empty($nombre) || empty($correo)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    } else {
        $clavePlano = generarContrasena();
        $claveHash = password_hash($clavePlano, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $correo, $claveHash, 2]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'andresfelipealmario2309@gmail.com';
            $mail->Password = 'dbmu jcrn tiev yony';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('andresfelipealmario2309@gmail.com', 'Administrador');
            $mail->addAddress($correo, $nombre);

            $mail->Subject = 'Tus credenciales para ingresar';
            $mail->Body    = "Hola $nombre,\n\nTu cuenta ha sido creada.\nCorreo: $correo\nContraseña: $clavePlano\n\nPor favor cámbiala al ingresar.";

            $mail->send();
            $_SESSION['success'] = "Trabajador registrado y correo enviado.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al enviar correo: {$mail->ErrorInfo}";
        }
    }

    header("Location: gestionar_trabajadores.php");
    exit;
}

// Obtener lista
$stmt = $pdo->query("SELECT id, nombre, correo FROM usuarios WHERE rol_id = 2");
$trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Trabajadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Gestionar Trabajadores</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" name="correo" id="correo" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Trabajador</button>
    </form>

    <h4>Lista de Trabajadores</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trabajadores as $trabajador): ?>
                <tr>
                    <td><?= $trabajador['id'] ?></td>
                    <td><?= $trabajador['nombre'] ?></td>
                    <td><?= $trabajador['correo'] ?></td>
                    <td>
                        <a href="?reset=<?= $trabajador['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('¿Reiniciar contraseña?')">Reiniciar Contraseña</a>
                        <a href="?eliminar=<?= $trabajador['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar trabajador?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
