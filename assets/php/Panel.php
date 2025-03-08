<?php
require '../../../Private/Credentials/DataBase/connection.php';
session_start();

// Hacer que la respuesta siempre sea JSON
header('Content-Type: application/json');

try {
    if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
        echo json_encode(array("status" => "error", "message" => "Sesión no iniciada"));
        exit();
    }

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

    if ($row = $result->fetch_assoc()) {
        echo json_encode(array("status" => "success", "user" => $_SESSION["username"]));
    } else {
        echo json_encode(array("status" => "error", "message" => "Usuario o contraseña incorrectos"));
    }
} catch (Exception $ex) {
    echo json_encode(array("status" => "error", "message" => "Error inesperado, por favor intenta más tarde."));
}
exit();
?>
