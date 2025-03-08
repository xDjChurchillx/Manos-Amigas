<?php
require '../../../Private/Credentials/DataBase/connection.php';
session_start();

try {
    // Verificar si la sesión está iniciada
    if (isset($_SESSION["username"]) && isset($_SESSION["password"])) {
        // Preparar la consulta con Prepared Statements
        $stmt = $conn->prepare("CALL sp_Login(?, ?)");
        if (!$stmt) {
            echo json_encode(array("status" => "error", "message" => "Error al preparar la consulta"));
            exit();
        }

        $stmt->bind_param("ss", $_SESSION["username"], $_SESSION["password"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            echo json_encode(array("status" => "error", "message" => "Error en la base de datos"));
            exit();
        }

        // Verificar si el usuario existe
        if ($row = $result->fetch_assoc()) {
            // Si el usuario existe, devolver datos
            echo json_encode(array("status" => "success", "user" => $_SESSION["username"]));
            exit();
        } else {
            // Si no se encuentra el usuario, enviar error
            echo json_encode(array("status" => "error", "message" => "Usuario o contraseña incorrectos"));
            exit();
        }
    } else {
        // Si no está autenticado, enviar error
        echo json_encode(array("status" => "error", "message" => "Sesión no iniciada"));
    }
} catch (Exception $ex) {
    // En caso de error inesperado, enviar mensaje general
    echo json_encode(array("status" => "error", "message" => "Error inesperado, por favor intenta más tarde."));
    exit();
}
?>