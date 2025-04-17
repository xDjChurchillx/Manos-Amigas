﻿<?php
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
        if (strlen($User) < 8 || strlen($nuevaContrasena) < 10 || strlen($User) > 41 || strlen($nuevaContrasena) > 20 || $nuevaContrasena !== $confirmarContrasena) {
            header("Location: /Gestion/ingreso.html?error=5");
            exit();
        }
        // Actualizar en la base de datos
        $stmt = $conn->prepare('CALL sp_ActualizarUsuario(?, ?, ?, ?)');
        if (!$stmt) {
            header("Location: /Gestion/ingreso.html?error=3");
            exit();
        }

        $stmt->bind_param('ssss', $username, $token, $User,$nuevaContrasena);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
            echo json_encode([
                    'status' => 'success'
                ]);
        } else {
             echo json_encode([
                    'status' => 'error',
                    'ex' => 'Usuario o token inválido.'
                ]);
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
