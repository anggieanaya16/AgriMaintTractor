<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a5f23, #2c8439);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #2c8439, #4caF50);
        }

        .logo {
            width: 100px;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
            font-size: 14px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #27ae60;
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2);
        }

        .btn-login {
            background: #27ae60;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #219653;
        }

        .footer {
            margin-top: 25px;
            color: #7f8c8d;
            font-size: 13px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7f8c8d;
        }

        .register-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- ✅ Aquí va tu logo -->
        <img src="AgriMaintTractors.jpeg" alt="Logo AgriMaint Tractors" class="logo">

        <h1>Agrimaint Tractors</h1>
        <p class="subtitle">Iniciar Sesión</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post" action="../controllers/loginController.php">
            <div class="form-group">
                <label for="correo">Correo Electrónico *</label>
                <div class="input-with-icon">
                    <span class="input-icon">✉</span>
                    <input type="email" id="correo" name="correo" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña *</label>
                <div class="input-with-icon">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" required>
                    <span class="password-toggle" onclick="togglePassword('password')">👁</span>
                </div>
            </div>

            <button type="submit" class="btn-login">Iniciar Sesión</button>
            <div class="mt-3 text-center">
                <a href="forgot_password.php">¿Olvidaste tu contraseña?</a>
            </div>
        </form>

        <div class="footer">
            © 02:11 PM -05, Saturday, May 31, 2025 Agrimaint Tractors. Todos los derechos reservados.
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling;
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            toggleIcon.textContent = passwordField.type === 'password' ? '👁' : '👁‍🗨';
        }
    </script>
</body>
</html>
