<?php
require '../../../Private/Credentials/DataBase/connection.php'; // Asumo que es MySQLi
session_start();

try {
    // Verificar si ya se registr� una visita en los �ltimos 30 segundos
    if (isset($_SESSION['last_visit_update']) && (time() - $_SESSION['last_visit_update']) < 7200) {
        echo '2';
        exit(); // Salir sin hacer nada
    } else {
        // Llamar al procedimiento almacenado sin par�metros
        $stmt = $conn->prepare("CALL sp_Visitas()");
        $stmt->execute(); // Ejecutar el procedimiento

        // Registrar el tiempo de la �ltima visita
        $_SESSION['last_visit_update'] = time();
        echo '1';
        exit(); // Salir correctamente
    }
} catch (Exception $ex) {  
    echo $ex;
    exit();
}
?>
