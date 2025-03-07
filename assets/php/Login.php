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

        // Sanitizaci�n de entrada
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            header("Location: /Gestion/ingreso.html?error=2"); // Campos vac�os
            exit();
        }

        // Preparar la consulta con Prepared Statements
        $stmt = $conn->prepare("CALL sp_Login(?, ?)");
        if (!$stmt) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en base de datos
            exit();
        }

        // Verificar si el usuario existe
        if ($row = $result->fetch_assoc()) {
            $_SESSION["username"]  = $username;
            $_SESSION["password"] = $password;

            header("Location: /Gestion/panel.html"); // Redirigir a dashboard
            exit();
        } else {
            header("Location: /Gestion/ingreso.html?error=1"); // Usuario o contrase�a incorrectos
            exit();
        }
    } catch (Exception $ex) {
        header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
        exit();
    }
}
