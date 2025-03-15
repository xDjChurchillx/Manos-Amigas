<?php
require '../../../Private/Credentials/DataBase/connection.php';


ini_set('session.use_only_cookies', 1); // Solo cookies, no IDs en URL

session_set_cookie_params([
    'lifetime' => 0, // Hasta cerrar navegador
    'path' => '/',
    'domain' => '', // Cambia por tu dominio real
    'secure' => false, // Solo HTTPS (IMPORTANTE en producci�n)
    'httponly' => true, // No accesible desde JavaScript
    'samesite' => 'Strict', // Protecci�n contra CSRF
]);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar si los datos fueron enviados correctamente
        if (!isset($_POST["username"]) || !isset($_POST["password"])) {
            header("Location: /Gestion/ingreso.html?error=2"); // Falta de datos
            exit();
        }

        // Sanitizaci�n de entrada
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $activationToken = password_hash($password . rand(100000, 999999), PASSWORD_DEFAULT);
        if (empty($username) || empty($password)) {
            header("Location: /Gestion/ingreso.html?error=2"); // Campos vac�os
            exit();
        }

        // Obtener el hash de la contrase�a almacenada desde la base de datos
        $stmt = $conn->prepare("CALL sp_Login(?)");
        if (!$stmt) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en base de datos
            exit();
        }

        // Verificar si el usuario existe
        if ($row = $result->fetch_assoc()) {
            $storedHash = $row["Contrasena"]; // Hash almacenado en la base de datos

            // Verificar la contrase�a usando password_verify()
            if (password_verify($password, $storedHash)) {
                session_regenerate_id(true); // Regenerar sesi�n para evitar fixation
                $_SESSION["username"] = $username;
                $_SESSION["user_agent"] = $_SERVER['HTTP_USER_AGENT']; // Asociar sesi�n al navegador
                $_SESSION["ip_address"] = $_SERVER['REMOTE_ADDR']; // Opcional: asociar a IP
                setcookie("token", $activationToken, time() + 86400, "/"); 
                 // Llamar al procedimiento almacenado para actualizar el token
                $stmt = $conn->prepare("CALL sp_UpdateActivationToken(?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ss", $username, $activationToken);
                    $stmt->execute();
                }
                header("Location: /Gestion/panel.html"); // Redirigir a dashboard
                exit();
            } else {
                header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contrase�a incorrectos
                exit();
            }
        } else {
            header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contrase�a incorrectos
            exit();
        }
    } catch (Exception $ex) {
        header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
        exit();
    }
}