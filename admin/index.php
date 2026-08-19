<?php
/**
 * Open Group — Admin Login
 */
require_once __DIR__ . '/config.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Login — Blog Open Group</title>
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css" />
    <link rel="shortcut icon" type="image/png" href="https://www.opengroupsa.com/assets/img/logo/logos/faviconopen.png" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: #0a0d14;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            background: #11151C;
            border: 1px solid #1E2228;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .login-box img {
            height: 50px;
            margin-bottom: 30px;
        }
        .login-box h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .login-box p {
            font-size: 14px;
            color: #B0B2B7;
            margin-bottom: 30px;
        }
        .login-box .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .login-box label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #B0B2B7;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            background: #181C26;
            border: 1px solid #1E2228;
            color: #fff;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        .login-box input:focus {
            outline: none;
            border-color: #73bd1e;
        }
        .login-box button {
            width: 100%;
            background: #73bd1e;
            color: #000;
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            font-family: inherit;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .login-box button:hover {
            background: #8cd44a;
        }
        .error-msg {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            font-size: 14px;
            padding: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Blog Open Group</h2>
        <p>Acceso exclusivo para editores</p>

        <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="username" placeholder="Tu usuario" required autocomplete="username" />
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Tu contraseña" required autocomplete="current-password" />
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
