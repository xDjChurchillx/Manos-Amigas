﻿<?php
    require '../../../Private/Credentials/DataBase/connection.php';
// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $Nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
    $Telefono = isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
    $Institucion = isset($_POST['Institucion']) ? $_POST['Institucion'] : '';
    $Carrera = isset($_POST['Carrera']) ? $_POST['Carrera'] : '';
    $Propuesta = isset($_POST['Propuesta']) ? $_POST['Propuesta'] : '';
    $otraPropuesta = isset($_POST['otraPropuesta']) ? $_POST['otraPropuesta'] : '';

   // Validar campos obligatorios
    if (empty($Nombre)) {
         header("Location: /Voluntariado.html?error=1"); // Error en 
         exit();
    }
    if (empty($Correo) && empty($Telefono)) {
         header("Location: /Voluntariado.html?error=1"); // Error en 
         exit();
    }
   if (empty($Propuesta)) {
         header("Location: /Voluntariado.html?error=1"); // Error en 
         exit();
    }
    // Sanitizar TODOS los caracteres especiales (incluyendo =)
    $Nombre = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $Correo = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $Telefono = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $Institucion = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8'); '';
    $Carrera = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $Propuesta = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $otraPropuesta = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  
    if($Propuesta == 'otro'){
      $Propuesta = $otraPropuesta;
    }
  $stmt = $conn->prepare('CALL sp_CrearVoluntario(?,?,?,?,?,?)');
  if (!$stmt) {
      header("Location: /Voluntariado.html?error=2"); // Error en BD
      exit();
  }

  $stmt->bind_param('ssssss', $Nombre,$Correo, $Telefono, $Institucion, $Carrera, $Propuesta);

  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if (array_key_exists('Sucess', $row)) {
         header("Location: /Voluntariado.html?error=0"); // SUCESS
         exit();
  } else {
      header("Location: /Voluntariado.html?error=2"); // Error en BD
      exit();
  }
  $stmt->close();
  $conn->close();


} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
    header('Location: ../../index.html');
    exit;
}