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

    
    // Retornar JSON con error
    echo json_encode([
        'status' => 'error',
        'redirect' => '/Gestion/ingreso.html?error=1'
    ]);
    exit();
}
////////////////////////////////////////////////////////////////////////////////////////////
$token = $_COOKIE['token'] ;
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Asunto = trim($_POST['asunto'] ?? '');
        $Mensaje = trim($_POST['mensaje'] ?? '');
        // Validación de datos
        if (empty($Asunto) || empty($Mensaje)) {
            echo json_encode(["status" => "error", "ex" => "Todos los campos son obligatorios."]);
            exit();
        }
        // 
        $stmt = $conn->prepare('CALL sp_ListarSusActivas(?, ?)');
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        $stmt->bind_param('ss', $username, $token);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $firstRow = $result->fetch_assoc();
    
            // Comprobar si es un error de autenticación
            if (array_key_exists('Error', $firstRow)) {
                echo json_encode([
                    'status' => 'error',
                    'ex' => 'Usuario o token inválido.'
                ]);
                exit();
            }
    
            // Si no es error, procesar todos los resultados
            $subscriptions = [];
    
            // Volvemos a incluir la primera fila (si existe)
            if ($firstRow) {
                $subscriptions[] = $firstRow;
                // Procesar el resto de filas
                while ($row = $result->fetch_assoc()) {
                    $subscriptions[] = $row;
                }
            }
    
            echo json_encode([
                    'status' => 'success'
            ]);
            exit();
        } else {
             echo json_encode([
                    'status' => 'error',
                    'ex' => 'Error en base de datos'
              ]);
              exit();
        }


        $stmt->close();
        $conn->close();
}

} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>
