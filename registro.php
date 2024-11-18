<?php

header("Access-Control-Allow-Origin: *"); // Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Encabezados permitidos

require 'vendor/autoload.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Parámetros de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Establece el encabezado para la respuesta JSON
header('Content-Type: application/json');

// Verifica si se ha enviado la solicitud de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Conexión a la base de datos
        $connect = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Recupera los datos del formulario
        $email = $_POST['email'];
        $user_name = $_POST['user_name'];
        $plain_password = $_POST['password'];

        // Verificación de campos vacíos
        if (empty($email) || empty($user_name) || empty($plain_password)) {
            echo json_encode(["error" => "Todos los campos son requeridos"]);
            exit;
        }

        // Verifica si el correo ya está registrado
        $query = "SELECT * FROM user WHERE user_email = ?";
        $statement = $connect->prepare($query);
        $statement->execute([$email]);
        $existingUser = $statement->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // Si el usuario ya existe
            http_response_code(409);
            echo json_encode(["error" => "El correo electrónico ya está registrado con el nombre $existingUser[user_name]"]);
        } else {
            // Si no existe, realiza el INSERT
            $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

            $query = "INSERT INTO user (user_name, user_email, user_password) VALUES (?, ?, ?)";
            $statement = $connect->prepare($query);
            $statement->execute([$user_name, $email, $hashed_password]);

            // Verificar si el INSERT fue exitoso
            if ($statement->rowCount() > 0) {
                // Si el registro se insertó correctamente, recupera los datos del usuario recién insertado
                $query = "SELECT * FROM user WHERE user_email = ?";
                $statement = $connect->prepare($query);
                $statement->execute([$email]);  
                $data = $statement->fetch(PDO::FETCH_ASSOC);  

                if($data) {
                    // Enviar respuesta de éxito con los datos del usuario
                    $response = [
                        "user_name" => $data["user_name"],
                        "user_email" => $data["user_email"],
                        "user_password" => $data["user_password"],
                        "user_id" => $data["user_id"],
                        "message" => "Usuario registrado con éxito"
                    ];

                    echo json_encode($response);
                }
            } else {
                echo json_encode(["error" => "Error al registrar usuario"]);
            }
        }
    } catch (PDOException $e) {
        // Si hay un error con la conexión a la base de datos
        echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Solicitud inválida"]);
}

?>
