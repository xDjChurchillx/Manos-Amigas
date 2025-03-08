<?php
require '../../../Private/Credentials/DataBase/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar si los datos fueron enviados
        if (!isset($_POST["username"]) || !isset($_POST["password"])) {
            header("Location: ../../html/index.html?error=2"); // Falta de datos
            exit();
        }

        // Sanitización de entrada
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            header("Location: ../../html/index.html?error=2"); // Campos vacíos
            exit();
        }

        // Preparar la consulta con Prepared Statements
        $stmt = $conn->prepare("CALL sp_Login(?, ?)");
        if (!$stmt) {
            header("Location: ../../html/index.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: ../../html/index.html?error=3"); // Error en base de datos
            exit();
        }

        // Verificar si el usuario existe
        if ($row = $result->fetch_assoc()) {
            $_SESSION["user"] = $username;
            $_SESSION["password"] = $password;

            header("Location: ../../html/dashboard.php"); // Redireccionar a dashboard
            exit();
        } else {
            header("Location: ../../html/index.html?error=1"); // Usuario o contraseña incorrectos
            exit();
        }
    } catch (Exception $ex) {
        header("Location: ../../html/index.html?error=4"); // Error inesperado
        exit();
    }
}
?>