<?php

require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "ejercicio_login";

// Verificar si el token está presente en las cookies
if (isset($_COOKIE["token"])) {
    try {
        // Decodificar el token con la clave secreta
        $decoded = JWT::decode($_COOKIE["token"], new Key($key, "HS256"));
        $userName = $decoded->data->user_name;  // Obtener el nombre del usuario
        $userId = $decoded->data->user_id;  // Obtener el ID del usuario
    } catch (Exception $e) {
        // Si el token no es válido, redirigir al login
        header("Location: index.php");
        exit();
    }
} else {
    // Si no hay token, redirigir al login
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
            text-align: center;
        }

        .welcome-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .welcome-container h1 {
            font-size: 36px;
            color: #4CAF50;
        }

        .welcome-container p {
            font-size: 18px;
            color: #333;
        }

        .btn-logout {
            background-color: #ff4d4d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-logout:hover {
            background-color: #ff3333;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($userName); ?>!</h1>
        <p>Tu ID de usuario es: <?php echo htmlspecialchars($userId); ?></p>
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</body>
</html>
