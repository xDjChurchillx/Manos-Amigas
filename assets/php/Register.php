<?php
require '../../../Private/Credentials/DataBase/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        echo "Error: Todos los campos son obligatorios.";
        exit();
    }

    // Hashear la contraseña
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar el usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuario (Usuario, Contrasena) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hash);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Usuario registrado exitosamente.";
    } else {
        echo "Error al registrar el usuario.";
    }
}