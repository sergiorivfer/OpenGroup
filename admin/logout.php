<?php
/**
 * Open Group — Admin Logout
 */
require_once __DIR__ . '/config.php';
logout();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2;url=/admin/">
    <title>Sesión cerrada</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0a0d14;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .msg { font-size: 18px; color: #73bd1e; }
    </style>
</head>
<body>
    <div class="msg">Sesión cerrada. Redirigiendo...</div>
</body>
</html>
