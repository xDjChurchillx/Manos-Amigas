<?php
// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : '';
    $donationDestination = isset($_POST['donationDestination']) ? $_POST['donationDestination'] : '';
    $donorMessage = isset($_POST['donorMessage']) ? $_POST['donorMessage'] : '';
    $donorName = isset($_POST['donorName']) ? $_POST['donorName'] : '';
    $donorContact = isset($_POST['donorContact']) ? $_POST['donorContact'] : '';

    // Validar y sanitizar los datos (importante para seguridad)
    $paymentMethod = htmlspecialchars($paymentMethod);
    $donationDestination = htmlspecialchars($donationDestination);
    $donorMessage = htmlspecialchars($donorMessage);
    $donorName = htmlspecialchars($donorName);
    $donorContact = htmlspecialchars($donorContact);

    // Aquí puedes procesar los datos (guardar en base de datos, enviar por email, etc.)
    // Por ejemplo, guardar en un archivo de texto:
    $data = "Método de pago: $paymentMethod\n";
    $data .= "Destino: $donationDestination\n";
    $data .= "Mensaje: $donorMessage\n";
    $data .= "Nombre: $donorName\n";
    $data .= "Contacto: $donorContact\n";
    $data .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
    $data .= "------------------------\n";


    // Redirigir a una página de agradecimiento
    echo "<script>alert(".$data.");</script>";
    exit;
} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
    header('Location: index.html');
    exit;
}