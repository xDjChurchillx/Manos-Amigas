<?php  
    // Importa la clase PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
try{
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
   

     // Instancia un nuevo objeto PHPMailer
    $mail = new PHPMailer(true);
    
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
}catch (Exception $ex) {
        echo $ex->getMessage();
        exit();
    } 
        


  