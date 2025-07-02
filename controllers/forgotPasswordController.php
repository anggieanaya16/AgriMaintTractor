<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host = '127.0.0.1';
$port = '3306';
$dbname = 'mantenimiento_tractores';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);

        if (!$correo) {
            $_SESSION['message'] = "Correo inválido.";
            header("Location: ../auth/forgot_password.php");
            exit;
        }

        // Verificar que el correo existe en la base de datos
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['message'] = "Si el correo existe en nuestro sistema, te enviaremos un enlace para restablecer la contraseña.";
            header("Location: ../auth/forgot_password.php");
            exit;
        }

        // Generar un token único y con fecha de expiración (1 hora)
        $token = bin2hex(random_bytes(16));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar token en tabla password_resets (debes crear esta tabla)
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
        $stmt->execute([$usuario['id'], $token, $expira]);

        // Enviar correo con el enlace de restablecimiento
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'anggienayaperdomo2@gmail.com'; 
            $mail->Password = 'dbmu jcrn tiev yony'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('anggieanayaperdomo2@gmail.com', 'Administrador');
            $mail->addAddress($correo);

            $url = "http://localhost/tractorss/auth/reset_password.php?token=$token";

            $mail->Subject = "Restablecer contraseña";
            $mail->Body = "Hola,\n\nHaz clic en el siguiente enlace para restablecer tu contraseña:\n$url\n\n".
                          "Este enlace expirará en 1 hora.\n\nSi no solicitaste este cambio, ignora este mensaje.";

            $mail->send();

            $_SESSION['message'] = "Si el correo existe en nuestro sistema, te enviaremos un enlace para restablecer la contraseña.";
            header("Location: ../auth/forgot_password.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['message'] = "Error al enviar el correo: " . $mail->ErrorInfo;
            header("Location: ../auth/forgot_password.php");
            exit;
        }
    }
} catch (\PDOException $e) {
    die("Error en conexión: " . $e->getMessage());
}