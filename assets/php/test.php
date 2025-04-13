<?php  
    // Importa la clase PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../Private/Credentials/DataBase/connection.php';
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
    $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";    
    $Correo = htmlentities($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
 
 
      $stmt = $conn->prepare('CALL sp_CrearSuscripcion(?)');
     
      $stmt->bind_param('s',$Correo);

      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      // Instancia un nuevo objeto PHPMailer
    $mail = new PHPMailer(true);
      // Configura el servidor SMTP
        //    $mail->isSMTP();
        //    $mail->Host       = 'smtp.hostinger.com';  // Cambia esto por tu servidor SMTP
        //    $mail->SMTPAuth   = true;
        //    $mail->Username   = $mail1; // Cambia esto por tu nombre de usuario SMTP
        //    $mail->Password   = $Pmail1; // Cambia esto por tu contraseña SMTP
        //    $mail->SMTPSecure = 'tls';
        //    $mail->Port       = 587;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $mail1; 
    $mail->Password   = $Pmail1;
    $mail->SMTPSecure = 'tls';                      // También podés usar 'ssl'
    $mail->Port       =  587;   
    // Configura el remitente y el destinatario
    $mail->setFrom($mail1 , 'Suscripcion');
    $mail->addAddress(html_entity_decode($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8'), '');

    // Configura el asunto y el cuerpo del correo
    $mail->Subject = 'Suscripcion';
    $mail->isHTML(true);  
    if (array_key_exists('Success', $row)) {
        // Éxito: se generó el token
         
                               $mail->Body = '
<html>
<head>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #F6F8D5;
            font-family: \'Arial\', sans-serif;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #205781, #4F959D);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .email-body {
            padding: 30px;
            color: #333;
        }
        
        .email-footer {
            padding: 20px;
            text-align: center;
            background-color: #f5f5f5;
            color: #205781;
            font-size: 12px;
        }
        
        .subscribe-btn {
            display: block;
            width: 60%;
            margin: 40px auto;
            padding: 15px;
            background: linear-gradient(135deg, #4F959D, #98D2C0);
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(79, 149, 157, 0.3);
        }
        
        .subscribe-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(79, 149, 157, 0.4);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-family: \'Courier New\', Courier, monospace;
            font-size: 24px;
            color: #205781;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Únete a Manos Amigas</h1>
        </div>
        
        <div class="email-body">
            <div class="logo">Manos Amigas</div>
            
            <p>Hola amigo/a,</p>
            
            <p>Estamos emocionados de invitarte a formar parte de nuestra comunidad. Al suscribirte recibirás:</p>
            
            <ul>
                <li>Actualizaciones exclusivas sobre nuestros proyectos</li>
                <li>Oportunidades para participar como voluntario</li>
                <li>Noticias sobre cómo estamos ayudando a la comunidad</li>
                <li>Promociones especiales para colaboradores</li>
            </ul>
            
            <p>¡No te pierdas esta oportunidad de ser parte del cambio!</p>
            
            <a href="#" class="subscribe-btn">SUSCRIBIRME AHORA</a>
            
            <p>Si prefieres no recibir más comunicaciones, puedes ignorar este mensaje.</p>
            
            <p>Con gratitud,<br>
            El equipo de <strong>Manos Amigas</strong></p>
        </div>
        
        <div class="email-footer">
            © 2023 Manos Amigas. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
';

                                

        // Envía el correo
        if ($mail->send()) {
            echo("1");
            exit();
        } else {
            echo("2"); // Fallo inesperado
            exit();
        }           


       
    } elseif (array_key_exists('Error', $row)) {
        
     echo("3". urlencode( html_entity_decode($Correo))); // Ya verificado        
     exit();
    }elseif (array_key_exists('Mensaje', $row)) {
       $mail->Body = '
<html>
<head>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #F6F8D5;
            font-family: \'Arial\', sans-serif;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #205781, #4F959D);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .email-body {
            padding: 30px;
            color: #333;
        }
        
        .email-footer {
            padding: 20px;
            text-align: center;
            background-color: #f5f5f5;
            color: #205781;
            font-size: 12px;
        }
        
        .subscribe-btn {
            display: block;
            width: 60%;
            margin: 40px auto;
            padding: 15px;
            background: linear-gradient(135deg, #4F959D, #98D2C0);
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(79, 149, 157, 0.3);
        }
        
        .subscribe-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(79, 149, 157, 0.4);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-family: \'Courier New\', Courier, monospace;
            font-size: 24px;
            color: #205781;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Únete a Manos Amigas</h1>
        </div>
        
        <div class="email-body">
            <div class="logo">Manos Amigas</div>
            
            <p>Hola amigo/a,</p>
            
            <p>Estamos emocionados de invitarte a formar parte de nuestra comunidad. Al suscribirte recibirás:</p>
            
            <ul>
                <li>Actualizaciones exclusivas sobre nuestros proyectos</li>
                <li>Oportunidades para participar como voluntario</li>
                <li>Noticias sobre cómo estamos ayudando a la comunidad</li>
                <li>Promociones especiales para colaboradores</li>
            </ul>
            
            <p>¡No te pierdas esta oportunidad de ser parte del cambio!</p>
            
            <a href="#" class="subscribe-btn">SUSCRIBIRME AHORA</a>
            
            <p>Si prefieres no recibir más comunicaciones, puedes ignorar este mensaje.</p>
            
            <p>Con gratitud,<br>
            El equipo de <strong>Manos Amigas</strong></p>
        </div>
        
        <div class="email-footer">
            © 2023 Manos Amigas. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
';

                                

        // Envía el correo
        if ($mail->send()) {
            echo("9".html_entity_decode($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            exit();
        } else {
            echo("8"); // Fallo inesperado
            exit();
        }           
    }
    else {
        echo("7"); // Fallo inesperado
        exit();
    }
      $stmt->close();
      $conn->close();


} else {
    // Si alguien intenta acceder directamente al script sin enviar el formulario
   $Token = $_GET['token'];
}