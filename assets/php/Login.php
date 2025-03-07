<?php
require '../../Private/Credentials/DataBase/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Sanitizar datos del formulario
        $username = mysqli_real_escape_string($conn, $_POST["username"]);
        $password = $_POST["password"];

        // Prepared statement para evitar inyección SQL
        $stmt = $conn->prepare("CALL Cte_Login(?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            header("Location: index.html?error=3"); // Error en base de datos
            exit();
        }

        if ($row = $result->fetch_assoc()) {
            $name = $row['Name'];
            $hashed_password = $row['Pass'];

            // Si el SP devuelve la contraseña hasheada, puedes verificar:
            if (hash_equals($hashed_password, hash('sha256', $password))) {
                $_SESSION["username"] = $username;
                $_SESSION["name"] = $name;
                header("Location: dashboard.php"); // Redireccionar a la página principal
                exit();
            } else {
                header("Location: index.html?error=2"); // Contraseña incorrecta
                exit();
            }
        } else {
            header("Location: index.html?error=1"); // Usuario no encontrado
            exit();
        }
    } catch (Exception $ex) {
        header("Location: index.html?error=4"); // Error general
        exit();
    }
}
?>