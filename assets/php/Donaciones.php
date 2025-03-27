<?php
    require '../../../Private/Credentials/DataBase/connection.php';
// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : '';
    $donationDestination = isset($_POST['donationDestination']) ? $_POST['donationDestination'] : '';
    $donorMessage = isset($_POST['donorMessage']) ? $_POST['donorMessage'] : '';
    $donorName = isset($_POST['donorName']) ? $_POST['donorName'] : '';
    $donorContact = isset($_POST['donorContact']) ? $_POST['donorContact'] : '';



   // Validar campos obligatorios
    if (empty($paymentMethod)) {
         header("Location: /Gestion/ingreso.html?error=1"); // Error en 
         exit();
    }
    if (empty($donationDestination)) {
         header("Location: /Gestion/ingreso.html?error=1"); // Error en 
         exit();
    }

    // Sanitizar TODOS los caracteres especiales (incluyendo =)
    $paymentMethod = htmlentities($paymentMethod, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $donationDestination = htmlentities($donationDestination, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $donorMessage = htmlentities($donorMessage, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $donorName = htmlentities($donorName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $donorContact = htmlentities($donorContact, ENT_QUOTES | ENT_HTML5, 'UTF-8');

  $stmt = $conn->prepare('CALL sp_CrearDonacion(?,?,?,?,?)');
  if (!$stmt) {
      header("Location: /Gestion/ingreso.html?error=2"); // Error en BD
      exit();
  }

  $stmt->bind_param('sssss', $paymentMethod, $donationDestination, $donorMessage, $donorName, $donorContact);

  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if (array_key_exists('Sucess', $row)) {
         header("Location: /Gestion/ingreso.html?error=0"); // SUCESS
         exit();
  } else {
      header("Location: /Gestion/ingreso.html?error=2"); // Error en BD
      exit();
  }
  $stmt->close();
  $conn->close();


} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
    header('Location: ../../index.html');
    exit;
}