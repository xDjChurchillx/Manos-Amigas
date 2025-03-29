<?php
require '../../../Private/Credentials/DataBase/connection.php'; // Asumo que es MySQLi
session_start();

try {
    // Verificar si ya se registró una visita en los últimos 30 segundos
    if (isset($_SESSION['last_visit_update']) && (time() - $_SESSION['last_visit_update']) < 7200) {
        echo 'false';
        exit(); // Salir sin hacer nada
    } else {
        // Llamar al procedimiento almacenado sin parámetros
        $stmt = $conn->prepare("CALL sp_Visitas()");
        $stmt->execute(); // Ejecutar el procedimiento

        // Registrar el tiempo de la última visita
        $_SESSION['last_visit_update'] = time();
        echo 'true';
        exit(); // Salir correctamente
    }
} catch (Exception $ex) {  
    echo $ex->getMessage();
    exit();
}
?>
