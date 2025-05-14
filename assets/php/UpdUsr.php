<?php
// Configuracion de Cookies y Base de datos 
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
    session_set_cookie_params([
        'lifetime' => 0, // Hasta cerrar navegador
        'path' => '/',
        'domain' => '', 
        'secure' => true, // Solo HTTPS 
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
        // Retornar error de credenciales invalidas
        echo json_encode([
            'status' => 'error',
            'redirect' => '/Gestion/ingreso.html?error=1'
        ]);
        exit();
    }
   
    //Sesion valida
    $token = $_COOKIE['token'] ;
    $username = $_SESSION['username'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Variables del form
        $User = trim($_POST['UserActual'] ?? '');
        $contrasenaActual = trim($_POST['contrasenaActual'] ?? '');
        $nuevaContrasena = trim($_POST['nuevaContrasena'] ?? '');
        $confirmarContrasena = trim($_POST['confirmarContrasena'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $code1 = trim($_POST['code1'] ?? '');
        $code2 = trim($_POST['code2'] ?? '');
        $code3 = trim($_POST['code3'] ?? '');
        $code4 = trim($_POST['code4'] ?? '');
        $code5 = trim($_POST['code5'] ?? '');

        // Validación de datos
        if (empty($User) || empty($contrasenaActual)) {
            echo json_encode(["status" => "error", "ex" => "Todos los campos son obligatorios."]);
            exit();
        }
//        if (strlen($User) < 8  || strlen($User) > 41) {
//            echo json_encode(["status" => "error", "ex" => "Formato de Usuario incorrecto(de 8 a 40 caracteres)"]);
//            exit();
//       }
//        if (strlen($nuevaContrasena) < 10 || strlen($nuevaContrasena) > 20 || strlen($nuevaContrasena) == 0) {
//            echo json_encode(["status" => "error", "ex" => "Formato de Nueva contraseña incorrecto(de 10 a 20 caracteres)"]);
//            exit();
//        }
        if(strlen($nuevaContrasena) !== 0){
           if (strlen($nuevaContrasena) < 3 || strlen($nuevaContrasena) > 20) {
                echo json_encode(["status" => "error", "ex" => "Formato de Nueva contraseña incorrecto(de 10 a 20 caracteres)"]);
                exit();
            }
             if ($nuevaContrasena !== $confirmarContrasena) {
                echo json_encode(["status" => "error", "ex" => "Contraseña de confirmacion no coincide"]);
                exit();
            }
        }
        
        if(strlen($correo) !== 0){
            $correo = htmlentities($correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (strlen($code1) === 0 || strlen($code2) === 0 || strlen($code3) === 0 || strlen($code4) === 0 || strlen($code5) === 0) {
                echo json_encode(["status" => "error", "ex" => "Introduce el codigo de verificacion que se envio al correo"]);
                exit();
            }
            // Obtener los códigos enviados
            $inputCodes = [
                intval($code1),
                intval($code2),
                intval($code3),
                intval($code4),
                intval($code5)
            ];

            // Obtener los códigos guardados
            $storedCodes = $_SESSION['cartas'] ?? [];
              if (count($storedCodes) !== 5) {
                    echo json_encode(["status" => "error", "ex" => "El código ha expirado."]);
                    exit();
                }
            // Validar
            if ($inputCodes !== $storedCodes) {
               echo json_encode(["status" => "error", "ex" => "Codigo Incorrecto"]);
                exit();
            }
        }

        // Obtener el hash de la base de datos
        $stmt = $conn->prepare("CALL sp_Login(?)");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        // Verificar si el usuario existe
        if ($row = $result->fetch_assoc()) {
            $storedHash = $row["Success"]; // Hash almacenado en la base de datos

            // Verificar la contraseña usando password_verify()
            if (password_verify($contrasenaActual, $storedHash)) {  
                if(strlen($correo) === 0){
                  $correo = $row["Correo"];
                }

                // Liberar los resultados de la primera consulta
                $result->free();
                $stmt->close();
                $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
                // Actualizar usuario en la base de datos
                $stmt = $conn->prepare('CALL sp_ActualizarUsuario(?, ?,?, ?, ?)');
                if (!$stmt) {
                    echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
                    exit();
                }
                if(strlen($nuevaContrasena) === 0){
                   $stmt->bind_param('sssss', $username, $token, $User,$storedHash,$correo);
                }else{
                  $stmt->bind_param('sssss', $username, $token, $User,$hash,$correo);
                }
               

                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if (array_key_exists('Success', $row)) {
                    echo json_encode(['status' => 'success']);
                    exit();
                } else {
                    if (array_key_exists('Repetido', $row)) {
                        echo json_encode(['status' => 'error', 'ex' => 'Usuario o Correo no se pueden repetir entre las cuentas']);
                         exit();
                    } else {
                         echo json_encode(['status' => 'error', 'ex' => 'Error en las credenciales']);
                         exit();
                    }
                }

                $stmt->close();
                $conn->close();
            } else {
                echo json_encode(['status' => 'error', 'ex' => 'Contraseña Incorrecta']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'ex' => 'Usuario Incorrecto']);
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
