<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$error = ''; 

// Solo procesar si la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = new PDO("mysql:host=localhost;dbname=usuarios", "root", "");

    if (empty($_POST["email"])) {
        $error = "Email is required";
    } else if (empty($_POST["password"])) {
        $error = "Password is required";
    } else {
        $query = "SELECT * FROM user WHERE user_email = ?";
        $statement = $connect->prepare($query);
        $statement->execute([$_POST["email"]]);  // Ejecuta el query
        $data = $statement->fetch(PDO::FETCH_ASSOC);  // Obtiene los datos de la consulta

        $plainPass = $_POST["password"];
        echo $plainPass;
        echo $data["user_password"];
        if ($data) {
            if (password_verify($plainPass, $data["user_password"])) {
                $key = "ejercicio_login";
                $token = JWT::encode(
                    array(
                        "iat" => time(),
                        "nbf" => time(),
                        "exp" => time() + 3600,  
                        "data" => [
                            "user_id" => $data["user_id"],
                            "user_name" => $data["user_name"],
                        ]
                    ),
                    $key,
                    "HS256"
                );

                $response = [
                    "user_name" => $data["user_name"],
                    "token" => $token
                ];

                header('Content-Type: application/json');

                echo json_encode($response);
                
                exit(); 
            } else {
                $error = "Wrong Password";
            }
        } else {
            $error = "Wrong Email Address";
        }
    }

    if ($error) {
        header('Content-Type: application/json');
        echo json_encode(["error" => $error]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid request method"]);
}
?>
