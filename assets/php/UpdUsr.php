<?php
// Repetimos la misma configuración de sesión para asegurar consistencia
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
session_set_cookie_params([
    'lifetime' => 0, // Hasta cerrar navegador
    'path' => '/',
    'domain' => '', // Cambia por tu dominio real
    'secure' => false, // Solo HTTPS (IMPORTANTE en producción)
    'httponly' => true, // No accesible desde JavaScript
    'samesite' => 'Strict', // Protección contra CSRF
]);

session_start();

// Validación de sesión
if (!isset($_COOKIE['token']) || !isset($_SESSION['username']) ||
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
    $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    // No autenticado o sesión alterada
        setcookie('token', '', time() - 3600, '/');
        session_unset(); // Limpia variables de sesión
        session_destroy(); // Elimina la sesión  
   
       header("Location: /Gestion/ingreso.html?error=1");
       exit();
}
////////////////////////////////////////////////////////////////////////////////////////////
$token = $_COOKIE['token'] ;
$username = $_SESSION['username'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $User = trim($_POST['UserActual'] ?? '');
        $contrasenaActual = trim($_POST['contrasenaActual'] ?? '');
        $nuevaContrasena = trim($_POST['nuevaContrasena'] ?? '');
        $confirmarContrasena = trim($_POST['confirmarContrasena'] ?? '');
        // Validación de datos
        if (empty($User) || empty($contrasenaActual) || empty($nuevaContrasena) || empty($confirmarContrasena)) {
            header("Location: /Gestion/ingreso.html?error=5");
            exit();
        }

        // Additional validations
        if (strlen($User) < 8  || strlen($User) > 41) {
            header("Location: /Gestion/ingreso.html?error=6");
            exit();
        }
         if (strlen($nuevaContrasena) < 10 || strlen($nuevaContrasena) > 20 ) {
            header("Location: /Gestion/ingreso.html?error=7");
            exit();
        }
         if ($nuevaContrasena !== $confirmarContrasena) {
            header("Location: /Gestion/ingreso.html?error=8");
            exit();
        }
        // Obtener el hash de la contraseña almacenada desde la base de datos
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
            if (password_verify($contrasenaActual, $storedHash)) {  

                 // Liberar los resultados de la primera consulta
                $result->free();
                $stmt->close();
                $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
                // Actualizar en la base de datos
                $stmt = $conn->prepare('CALL sp_ActualizarUsuario(?, ?, ?, ?)');
                if (!$stmt) {
                    header("Location: /Gestion/ingreso.html?error=3");
                    exit();
                }

                $stmt->bind_param('ssss', $username, $token, $User,$hash);

                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if (array_key_exists('Success', $row)) {
                    header("Location: /Gestion/ingreso.html?error=10"); // Usuario o contraseña incorrectos
                    exit();
                exit();
                } else {
                     header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contraseña incorrectos
                     exit();
                }

                $stmt->close();
                $conn->close();
            } else {
                header("Location: /Gestion/ingreso.html?error=9"); // Usuario o contraseña incorrectos
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
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>
