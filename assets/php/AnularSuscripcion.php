<?php  
    // Importa la clase PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../Private/Credentials/DataBase/connection.php';
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

 $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
  
    //$dominio = "https://" . $_SERVER['HTTP_HOST'];
     $dominio = "http://" . $_SERVER['HTTP_HOST'];
    if (!preg_match($regex, $Correo)) {
        header("Location: /index.html?error=1"); // Error correo no valido
         exit();
    }  
    $Correo = htmlentities($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');   

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
    $mail->Port       = 587;
    // Configura el remitente y el destinatario
    $mail->setFrom($mail1 , 'ManosAmigas');
    $mail->addAddress( html_entity_decode($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8'), '');

    // Configura el asunto y el cuerpo del correo
    $mail->Subject = 'Anular Suscripcion';
    $mail->isHTML(true);  
 
    $stmt = $conn->prepare('CALL sp_GenerarAnularSuscripcion(?)');
    if (!$stmt) {
        header("Location: /index.html?error=4"); // Error en BD
        exit();
    }

    $stmt->bind_param('s',$Correo);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    if (array_key_exists('Success', $row)) {
        // Éxito: se generó el token       
        // Envía el correo
           $mail->Body = '
            <html>
            <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>'Anular'</title>
            </head>
            <body>
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
                                Anular Suscripcion
                            </h1>
 
                        </div>
                    </header>
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
                                    Hola amigo/a,
                                </p>
                                <p style="
                                    margin: 0 0 20px;
                                    font-size: 16px;
                                    line-height: 1.7;
                                ">
                                    Este correo es para anular la suscripcion a noticias y actividades de Centro Diurno Manos Amigas
                                </p>
            
                                <a href="'.$dominio.'/assets/php/AnularSuscripcion.php?correo='.urlencode(html_entity_decode($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8')).'&token='.urlencode($row['Success']).'" 
                            class="anular-btn" 
                            style="display: block; width: 60%; margin: 40px auto; padding: 15px; background-color: #ff0000 !important; color: white !important; text-align: center; text-decoration: none; font-weight: bold; border-radius: 50px; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(79, 149, 157, 0.3);">
                                    Anular Suscripcion
                                </a>

                            <p style="
                                margin: 30px 0 20px;
                                font-size: 14px;
                                color: #666;
                            ">
                                Si no solicitaste anular la suscripcion y dejar de recibir noticias sobre nosotros, puedes ignorar este mensaje.
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
            header("Location: /index.html?error=9");
            exit();
        } else {
            header("Location: /index.html?error=5"); // Fallo inesperado
            exit();
        }  
    } elseif (array_key_exists('Error', $row)) {
         // Correo o token invalido
         header("Location: /index.html?error=7"); // Correo o token invalido       
         exit();
    }
    else {
        header("Location: /index.html?error=5"); // Fallo inesperado
        exit();
    }
}else {
    if (!isset($_GET['correo']) || !isset($_GET['token'])) {
        header("Location: /index.html?error=5"); // Fallo inesperado
            exit();
    }
	$Correo = urldecode($_GET['correo']);   
    //$dominio = "https://" . $_SERVER['HTTP_HOST'];
    $dominio = "http://" . $_SERVER['HTTP_HOST'];
    if (!preg_match($regex, $Correo)) {
        header("Location: /index.html?error=1"); // Error correo no valido
         exit();
    }  
    $Correo = htmlentities($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $Token = urldecode($_GET['token']);
    $regex2 = "/^[a-z0-9]+$/";
    
    if (!preg_match($regex2, $Token)) {
        header("Location: /index.html?error=6"); // Error token no valido
         exit();
    }  
    $stmt = $conn->prepare('CALL sp_AnularSuscripcion(?,?)');
    if (!$stmt) {
        header("Location: /index.html?error=4"); // Error en BD
        exit();
    }

    $stmt->bind_param('ss',$Correo,$Token);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    if (array_key_exists('Success', $row)) {
     header("Location: /index.html?error=10");
      exit();
    }else {
	 header("Location: /index.html?error=7"); // Error correo no valido o token
     exit();
    }

   
}