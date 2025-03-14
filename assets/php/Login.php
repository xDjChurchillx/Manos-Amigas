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

        // Obtener el hash de la contraseña almacenada desde la base de datos
        $stmt = $conn->prepare("CALL sp_Login(?)");
        if (!$stmt) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en base de datos
            exit();
        }

        // Verificar si el usuario existe
        if ($row = $result->fetch_assoc()) {
            $storedHash = $row["Contrasena"]; // Hash almacenado en la base de datos

            // Verificar la contraseña usando password_verify()
            if (password_verify($password, $storedHash)) {
                $_SESSION["username"] = $username;
                header("Location: /Gestion/panel.html"); // Redirigir a dashboard
                exit();
            } else {
                header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contraseña incorrectos
                exit();
            }
        } else {
            header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contraseña incorrectos
            exit();
        }
    } catch (Exception $ex) {
        header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
        exit();
    }
}