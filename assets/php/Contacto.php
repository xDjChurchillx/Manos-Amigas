<?php
// Configuracion de la clase PHPMailer y Base de datos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
header('Content-Type: application/json; charset=UTF-8');
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
      if (true) {
         header("Location: /Contacto.html?error=1"); // Error en 
         exit();
    }
     // Instancia un nuevo objeto PHPMailer
            $mail = new PHPMailer(true);
            // Configura el servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';  // Cambia esto por tu servidor SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = $mail1; // Cambia esto por tu nombre de usuario SMTP
            $mail->Password   = $Pmail1; // Cambia esto por tu contraseña SMTP
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Configura el remitente y el destinatario
            $mail->setFrom($mail1 , 'ManosAmigas');
            $mail->addAddress( $mail1 , ''); 
            $mail->Subject = $Asunto;
            $mail->isHTML(true);  
            $mail->Body = '
            <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Mensaje de Usuario</title>
                </head>
                <body style="margin: 0; padding: 0; background-color: #f8f9fa; line-height: 1.6;">

                    <!-- Hero con forma diagonal mejorado -->
                    <header style="
                        background: linear-gradient(135deg, #205781 0%, #4F959D 100%);
                        min-height: 23vh;
                        clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
                        color: white;
                        display: flex;
                        align-items: center;
                        padding-bottom: 5rem;
                    ">
                        <div style="width: 100%; text-align: center; padding-top: 3rem;">
                                <h1 style="
                                font-size: 2.5rem;
                                letter-spacing: 0.05em;
                                margin-bottom: 1rem;
                                text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
                                font-weight: 300;
                            ">
                                Centro Diurno Manos Amigas
                            </h1>
                                <div style="width: 400px; height: 3px; background: #F6F8D5; margin: 0 auto;"></div>
                            <h1 style="
               
                                    margin: 0;
                                font-size: 28px;
                                font-weight: 600;
                                letter-spacing: 0.5px;
                            ">
                               Mensaje de Usuario
                            </h1>
           
                        </div>
                    </header>

                    <!-- Contenedor principal mejorado -->
                    <div style="
                        background-color: white;
                        overflow: hidden;
                    ">
                        <!-- Contenido mejorado -->
                        <div style="padding: 40px 30px; color: #444;">

                            <p style="
                                margin: 0 0 20px;
                                font-size: 16px;
                                line-height: 1.7;
                            ">
                                '.$Mensaje.'
                            </p>                           

                            <p style="
                                margin: 40px 0 20px;
                                font-style: italic;
                                color: #555;
                            ">
                                Con gratitud,<br>
                                El equipo de <strong style="color: #205781;">Centro Diurno Manos Amigas</strong>
                            </p>
                        </div>

                        <!-- Pie de página mejorado -->
                        <div style="
                            padding: 20px;
                            text-align: center;
                            background-color: #f5f7fa;
                            color: #205781;
                            font-size: 12px;
                            border-top: 1px solid #eaeaea;
                        ">
                            © 2025 Manos Amigas. Todos los derechos reservados.<br>
                            <span style="font-size: 11px; opacity: 0.7;">Cuidando de nuestros adultos mayores con amor y dedicación</span>
                        </div>
                    </div>
                </body>
                </html>
            ';
            if ($mail->send()) {
                echo json_encode([
                        'status' => 'success'
                ]);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'ex' => 'Error enviando correo'
                ]);
                exit();
            }     

} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
    header('Location: ../../index.html');
    exit;
}