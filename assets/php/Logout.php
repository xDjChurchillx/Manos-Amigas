<?php
require '../../../Private/Credentials/DataBase/connection.php';
session_start();
// Validación de sesión
if (!isset($_COOKIE["token"]) || !isset($_SESSION["username"]) ||
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
    $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    // No autenticado o sesión alterada   
    setcookie("token", "", time() - 3600, "/");
    session_unset(); // Limpia variables de sesión
    session_destroy(); // Elimina la sesión
    header("Location: ../../index.html");
    exit();
}
setcookie("token", "", time() - 3600, "/");
session_unset(); // Limpia variables de sesión
session_destroy(); // Elimina la sesión
$stmt = $conn->prepare("CALL sp_Logout()");
$stmt->execute();
header("Location: ../../index.html");
exit();
?>