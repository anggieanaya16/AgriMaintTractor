<?php
session_start();

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol_id'];
            header("Location: ../principal.php");
            exit();
        } else {
            $_SESSION['error'] = "Credenciales inválidas";
            header("Location: ../auth/login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en el sistema";
        header("Location: ../auth/login.php");
        exit();
    }
}

