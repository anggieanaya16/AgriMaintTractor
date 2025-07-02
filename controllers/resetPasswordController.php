<?php
session_start();
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? null;
    $newPassword = $_POST['password'] ?? null;
    $confirmPassword = $_POST['confirm_password'] ?? null;

    // Validaciones básicas
    if (!$token || !$newPassword || !$confirmPassword) {
        $_SESSION['message'] = "Todos los campos son obligatorios";
        header("Location: ../auth/reset_password.php?token=" . urlencode($token));
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['message'] = "Las contraseñas no coinciden";
        header("Location: ../auth/reset_password.php?token=" . urlencode($token));
        exit;
    }

    try {
        // Buscar token válido
        $stmt = $pdo->prepare("SELECT * FROM password_resets 
                               WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $_SESSION['message'] = "Token inválido o expirado";
            header("Location: ../auth/forgot_password.php");
            exit;
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $reset['user_id']]);

        // Eliminar token
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$reset['user_id']]);

        $_SESSION['message'] = "Contraseña actualizada correctamente";
        header("Location: ../auth/login.php");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}