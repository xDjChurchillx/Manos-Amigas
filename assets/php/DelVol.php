<?php
// Configuracion de Cookies y Base de datos 
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
    session_set_cookie_params([
        'lifetime' => 0, // Hasta cerrar navegador
        'path' => '/',
        'domain' => '', // Cambia por tu dominio real
        'secure' => true, // Solo HTTPS (IMPORTANTE en producción)
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

    $data = json_decode(file_get_contents('php://input'), true);
    $codigo = $data['codigo'] ?? '';

    //Eliminar en la base de datos
    $stmt = $conn->prepare('CALL sp_EliminarVoluntario(?, ?, ?)');
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
        exit();
    }

    $stmt->bind_param('sss', $username, $token, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (array_key_exists('Error', $row)) {
        echo json_encode([
            'status' => 'error',
            'ex' => $row['Error']
        ]);
    } else {
   
        if(array_key_exists('Success',$row)){
                    echo json_encode([
                        'status' => 'success',
                        'mensaje' => $row['Success']
                    ]);
        }else {
	        echo json_encode([
            'status' => 'error',
            'ex' => 'Error en base de datos'
        ]);
        }  
    }
    $stmt->close();
    $conn->close();
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>
