<?php
session_start();

// Simular una base de datos de usuarios (en producci�n usar MySQL)
$usuarios = [
    "admin" => password_hash("admin123", PASSWORD_BCRYPT),
    "usuario" => password_hash("usuario456", PASSWORD_BCRYPT),
];

// Obtener datos del formulario
$username = $_POST["username"];
$password = $_POST["password"];

// Validar usuario
if (array_key_exists($username, $usuarios)) {
    // Verificar contrase�a
    if (password_verify($password, $usuarios[$username])) {
        // Crear sesi�n y redirigir al dashboard
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        // Contrase�a incorrecta
        header("Location: index.html?error=2");
        exit();
    }
} else {
    // Usuario no existe
    header("Location: index.html?error=1");
    exit();
}
?>