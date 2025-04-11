<?php
    require '../../../Private/Credentials/DataBase/connection.php';
// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $Nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
    $Telefono = isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
    $Mensaje = isset($_POST['Mensaje']) ? $_POST['Mensaje'] : '';

   // Validar campos obligatorios
    if (empty($Mensaje)) {
         header("Location: /Contacto.html?error=1"); // Error en 
         exit();
    }
    if (empty($Correo) && empty($Telefono)) {
         header("Location: /Contacto.html?error=1"); // Error en 
         exit();
    }
  


} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
    header('Location: ../../index.html');
    exit;
}