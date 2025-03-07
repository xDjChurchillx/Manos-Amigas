<?php
require '../../Private/Credentials/DataBase/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $username = mysqli_real_escape_string($conn, $_POST["username"]);
        $password = mysqli_real_escape_string($conn, $_POST["password"]);

        // Llamar al Stored Procedure con prepared statement
        $stmt = $conn->prepare("CALL sp_Login(?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: index.html?error=3"); // Error en base de datos
            exit();
        }

        if ($row = $result->fetch_assoc()) {
            // Login exitoso, guardar datos en sesión
            $_SESSION["Codigo"] = $row['Codigo'];
            $_SESSION["Usuario"] = $row['Usuario'];

            header("Location: dashboard.php"); // Redireccionar a la página principal
            exit();
        } else {
            header("Location: index.html?error=1"); // Usuario o contraseña incorrectos
            exit();
        }
    } catch (Exception $ex) {
        header("Location: index.html?error=4"); // Error inesperado
        exit();
    }
}
?>
