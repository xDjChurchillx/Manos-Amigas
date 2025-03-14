<?php
require '../../../Private/Credentials/DataBase/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar si los datos fueron enviados correctamente
        if (!isset($_POST["username"]) || !isset($_POST["password"])) {
            header("Location: /Gestion/ingreso.html?error=2"); // Falta de datos
            exit();
        }

        // Sanitización de entrada
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            header("Location: /Gestion/ingreso.html?error=2"); // Campos vacíos
            exit();
        }

        // Generar el hash de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Llamar al procedimiento almacenado con el hash
        $stmt = $conn->prepare("CALL sp_Login(?, ?)");
        if (!$stmt) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en base de datos
            exit();
        }

        // Verificar si el usuario existe y los hashes coinciden
        if ($row = $result->fetch_assoc()) {
            $_SESSION["username"] = $username;
            header("Location: /Gestion/panel.html"); // Redirigir a dashboard
            exit();
        } else {
            header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contraseña incorrectos
            exit();
        }
    } catch (Exception $ex) {
        header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
        exit();
    }
}