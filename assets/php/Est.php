<?php
// Configuracion de Base de datos 
require '../../../Private/Credentials/DataBase/connection.php'; 
session_start();
try {
    // Verificar si ya se registró una visita en los últimos 30 segundos
    if (isset($_SESSION['last_visit_update']) && (time() - $_SESSION['last_visit_update']) < 7200) {
        echo 'false';
        exit(); // Salir sin hacer nada
    } else {
        // Insertar visita en la base de datos
        $stmt = $conn->prepare("CALL sp_EstVisita()");
        $stmt->execute();

        // Registrar el tiempo de la última visita
        $_SESSION['last_visit_update'] = time();
        echo 'true';
        exit(); 
    }
} catch (Exception $ex) {  
    echo 'error en Est php';
    exit();
}
?>
