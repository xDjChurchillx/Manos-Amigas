<?php
// Configuracion de Base de datos 
require '../../../Private/Credentials/DataBase/connection.php';
ini_set('session.use_only_cookies', 1); 

try{
    session_set_cookie_params([
        'lifetime' => 0, // Hasta cerrar navegador
        'path' => '/',
        'domain' => '', 
        'secure' => false, // Solo HTTPS 
        'httponly' => true, // No accesible desde JavaScript
        'samesite' => 'Strict', // Protección contra CSRF
    ]);

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {    
        // Validacion de datos
        if (!isset($_POST["username"]) || !isset($_POST["password"])) {
            header("Location: /Gestion/ingreso.html?error=2"); // Falta de datos
            exit();
        }

        // Sanitización de entrada
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            header("Location: /Gestion/ingreso.html?error=2"); // Campos vacíos
            exit();
        }

        // Obtener el hash de la base de datos
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

            // Verificar la contraseña usando password_verify()
            if (password_verify($password, $storedHash)) {
                session_regenerate_id(true); // Regenerar sesión para evitar fixation
                $_SESSION["username"] = $username;
                $_SESSION["user_agent"] = $_SERVER['HTTP_USER_AGENT']; // Asociar sesión al navegador
                $_SESSION["ip_address"] = $_SERVER['REMOTE_ADDR']; // Opcional: asociar a IP

                // Liberar los resultados de la primera consulta
                $result->free();
                $stmt->close();
                $activationToken = bin2hex(random_bytes(32));

                 //Actualizar la sesión de la base de datos
                $stmt = $conn->prepare("CALL sp_UpdateSession(?, ?)");
                if (!$stmt) {
                    header("Location: /Gestion/ingreso.html?error=3"); // Error en la base de datos
                    exit();
                }

                $stmt->bind_param("ss", $username, $activationToken);
                $stmt->execute();

                setcookie("token", $activationToken, time() + 86400, "/");

                header("Location: /Gestion/panel.html"); // Redirigir a dashboard
                exit();
            } else {
                header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contraseña incorrectos
                exit();
            }
        } else {
            header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contraseña incorrectos
            exit();
        }   
    }
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => 'error en login php'
    ]);
    exit();
}
?>