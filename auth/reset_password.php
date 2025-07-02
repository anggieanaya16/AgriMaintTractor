<?php
session_start();

$token = $_GET['token'] ?? null;

if (!$token) {
    $_SESSION['message'] = 'Token no proporcionado.';
    header("Location: forgot_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Restablecer Contraseña</h2>
    <div class="row justify-content-center">
        <div class="col-md-5">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <div class="card shadow">
                <div class="card-body">
                    <form method="post" action="../controllers/resetPasswordController.php">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>