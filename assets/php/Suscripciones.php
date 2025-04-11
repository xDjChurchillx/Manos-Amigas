<?php
    require '../../../Private/Credentials/DataBase/connection.php';
// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
  
    if (empty($Correo)) {
         header("Location: /Voluntariado.html?error=1"); // Error en 
         exit();
    }
 
    $Correo = htmlentities($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
 
 
  $stmt = $conn->prepare('CALL sp_CrearVoluntario(?,?,?,?,?,?)');
  if (!$stmt) {
      header("Location: /index.html?error=2"); // Error en BD
      exit();
  }

  $stmt->bind_param('s',$Correo, );

  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if (array_key_exists('Success', $row)) {
         header("Location: /index.html?error=0"); // Success
         exit();
  } else {
      header("Location: /index.html?error=2"); // Error en BD
      exit();
  }
  $stmt->close();
  $conn->close();


} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
    header('Location: ../../index.html');
    exit;
}